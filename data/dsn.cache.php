<?php

if (!defined('IN_PX'))
    exit;
/**
 * 返回数据库访问
 * 本地缓存
 */
return array(
    'default' => array(
        'charset' => 'utf8',
        'prefix' => 'px_',
        
        // 'driver' => 'mysql',
        // 'persistent' => false,//使用长连接
        // 'host' => 'localhost',
        // 'port' => 3306,
        // 'dbName' => 'php-admin',
        // 'user' => 'root',
        // 'password' => 'root',

        'driver' => 'mysql',
        'persistent' => false,//使用长连接
        'host' => 'mysql.sql131.cdncenter.net',
        'port' => 3306,
        'dbName' => 'sq_hengxcs18',
        'user' => 'sq_hengxcs18',
        'password' => 'hengxiang2018',


//        'driver' => 'oci',
//        'host' => '192.168.0.x',
//        'port' => 1521,
//        'dbName' => 'ORCL',
//        'user' => 'xxx',
//        'password' => 'xxx',

        'useSequencer' => false,//是否使用全局序列
        'sequencer' => '__SEQ__',//全局序列占位符
        'level2cache' => true,
        'level2TopLimit' => 0,
        'sessionType' => 'database', //database or memcache,memcached,redis
        'cacheType' => 'file', //file or memcache,memcached,redis
        'memServers' => array(
//            'aliocs' => array('***.m.cnhzaliqshpub001.ocs.aliyuncs.com', 11211, '***', '***')
//            'memcache' => array('127.0.0.1', 11211)
            'memcached' => array(
                array('127.0.0.1', 11211, 1)
            )
        )
    )
);
