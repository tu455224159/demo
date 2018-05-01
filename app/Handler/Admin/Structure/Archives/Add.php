<?php

namespace App\Handler\Admin\Structure\Archives;

if (!defined('IN_PX'))
    exit;

use App\Handler\Admin\AbstractCommon;
use App\Service\Archives;
use App\Service\Templates;
use App\Service\UPFile;
use App\Tools\Html;
use Phoenix\Support\MsgHelper;
use Exception;

/**
 * 添加
 *
 */
class Add extends AbstractCommon {

    protected function __Inject($db, $cache, $session, UPFile $upFile,
                                Archives $serviceArchives = null, Templates $toolsTemplates = null) {}

    public function processRequest(Array & $context) {
        try {
            $this->db->beginTransaction();

            $this->_pushSetting();
            $this->_processingParameters();

            //$this->db->debug();
            if (!isset($_POST['seo_url'])) {
                $_POST['seo_url'] = $_POST['title'];
            }

            if (preg_match('/^[a-zA-Z0-9\-\s\x{4e00}-\x{9fa5}]*$/u', $_POST['seo_url'])) {
                $_POST['seo_url'] = $this->_filterToSeoUrl($_POST['seo_url']);
            }
            else {
                $_POST['seo_url'] = '';
            }

            if ($_POST['seo_url'] != '' && $this->db->table('`#@__@archives`')
                    ->where('seo_url = ?')
                    ->bind(array($_POST['seo_url']))
                    ->exists()) {//不能有相同的seo url
                echo(MsgHelper::json('SEO_URL_IS_EXISTS', 'seo url重复存在'));
                exit;
            }

            $_POST['title'] = Html::getTextToHtml($_POST['title']);
            $_POST['is_home_display'] = isset($_POST['is_home_display']) ? intval($_POST['is_home_display']) : 0;
            $_POST['view_count'] = isset($_POST['view_count']) ? intval($_POST['view_count']) : 0;
            $_POST['master_id'] = $this->session->adminUser['id'];
            $_POST['add_date'] = time();
            $_POST['release_date'] = $_POST['add_date'];

            //关联文档
            $_POST['file_id'] = implode('|', json_decode($_POST['link_json'], true));
            $_POST['archives_id_str'] = implode('|', json_decode($_POST['link_jsonLin'], true));

            $_identity = $this->db->table('`#@__@archives`')
                ->row(array(
                    '`category_id`' => '?',
                    '`title`' => '?',
                    '`title_english`' => '?',
                    '`seo_url`' => '?',
                    '`synopsis`' => '?',
                    '`cover`' => '?',
                    '`file_id`' => '?',
                    '`archives_id_str`' => '?',
//                    '`attachment`' => '?',
                    '`is_home_display`' => '?',
//                    '`view_count`' => '?',
                    '`seo_title`' => '?',
                    '`seo_keywords`' => '?',
                    '`seo_description`' => '?',
                    '`master_id`' => '?',
                    '`add_date`' => '?',
                    '`sort`' => '?',
//                    '`video_url`' => '?',
                    '`release_date`' => '?',
                    '`language`' => '?'
                ))
                ->bind($_POST)
                ->save();

            //$_POST['substance'] = $this->serviceArchives->addAnchorText($_POST['substance']);
            //$this->db->debug();
            $this->db->table('`#@__@archives_substance`')
                ->row(array(
                    '`archives_id`' => '?',
                    '`substance`' => '?'
                ))
                ->bind(array(
                    $_identity,
                    $this->serviceArchives->addAnchorText($_POST['substance'])
                    //, $_POST['substance']
                ))
                ->save();

            $this->db->table('`#@__@category`')
                ->row(array(
                    '`total`' => '(SELECT COUNT(*) FROM `#@__@archives` WHERE `category_id` = ? AND `is_display` = 1)'
                ))
                ->where('`category_id` = ?')
                ->bind(array(
                    $_POST['category_id'],
                    $_POST['category_id']
                ))
                ->update();

            if ($_identity) {
                $_POST['archives_id'] = $_identity;
                if (isset($_POST['src']) && count($_POST['src']) > 0) {
                    foreach ($_POST['src'] as $_k => $_v) {
                        $this->db->table('`#@__@archives_attach`')
                            ->row(array(
                                '`archives_id`' => '?',
                                '`src`' => '?'
                            ))
                            ->bind(array(
                                $_POST['archives_id'],
                                $_v
                            ))
                            ->save();
                        $this->upFile->createimg($_v);
                    }
                }
            }

            $this->cache->delete($this->setting['aryArchivesDeleteCacheBindId']);
            if (isset($_POST['archives_tags'])) {
                $this->_updateTags($_POST['archives_tags'], $_identity);
            }

            $this->_createImg('cover');

            if ((int) $this->cfg['is_html_page'] > 0) {
                $this->toolsTemplates->createColumn($_POST['category_id']);
                $this->toolsTemplates->createArchives($_identity, 'archives');
            }

            $this->db->commit();

            echo(MsgHelper::json($_identity ? 'SUCCESS' : 'DB_ERROR'));
        } catch (Exception $e) {

            $this->db->rollBack();
            echo(MsgHelper::json('DB_ERROR'));
        }
    }

}
