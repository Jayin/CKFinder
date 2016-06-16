<?php

// +----------------------------------------------------------------------
// | Copyright (c) Zhutibang.Inc 2016 http://zhutibang.cn All rights reserved.
// +----------------------------------------------------------------------
// | Author: Jayin Ton <tonjayin@gmail.com>
// +----------------------------------------------------------------------

namespace Joinca\ZKUploader\Plugin\Connectstatus;


use Joinca\ZKUploader\Command\CommandAbstract;
use Joinca\ZKUploader\Config;
use Joinca\ZKUploader\Filesystem\Folder\WorkingFolder;
use Joinca\ZKUploader\Plugin\PluginInterface;
use Symfony\Component\HttpFoundation\Request;

/**
 * API: http://you.donmain/ckfinder/core/connector/php/connector.php?command=Connectstatus
 *
 * Joinca 移动存储状态检测
 *
 * @return json
 * {"status": "ok"}
 *
 * @package Joinca\ZKUploader\Plugin\Connectstatus
 */
class Connectstatus extends CommandAbstract implements PluginInterface{


    /**
     * Returns an array with the default configuration for this plugin. Any of
     * the plugin configuration options can be overwritten in the ZKUploader configuration file.
     *
     * @return array default plugin configuration
     */
    public function getDefaultConfig() {
        return [];
    }

    public function execute(Request $request, Config $config) {


        if(!file_exists(EXTERNAL_FOLDER)){
            return array('status' => 'faild');
        }

        return array('status' => 'ok');
    }
}