<?php

namespace App\Handler\Admin\Structure\File;

if (!defined('IN_PX'))
    exit;

use App\Handler\Admin\AbstractCommon;

/**
 * 删除
 */
class Delete extends AbstractCommon {

    public function processRequest(Array & $context) {
        echo($this->_publicDeleteFieldByPostItem($_POST['id'], '`#@__@file`', 'file_id'));
    }

}
