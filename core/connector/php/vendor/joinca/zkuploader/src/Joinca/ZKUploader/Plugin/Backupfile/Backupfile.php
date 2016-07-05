<?php

// +----------------------------------------------------------------------
// | Copyright (c) Zhutibang.Inc 2016 http://zhutibang.cn All rights reserved.
// +----------------------------------------------------------------------
// | Author: Jayin Ton <tonjayin@gmail.com>
// +----------------------------------------------------------------------

namespace Joinca\ZKUploader\Plugin\Backupfile;


use Joinca\ZKUploader\Command\CommandAbstract;
use Joinca\ZKUploader\Plugin\PluginInterface;
use Symfony\Component\HttpFoundation\Request;
use Joinca\ZKUploader\Config;

/**
 * API:  http://you.donmain/ckfinder/core/connector/php/connector.php?command=Backupfile&cpfile=files/foo/bar/1.jpg
 * 注意： cpfile 就是需要复制的文件，可以从Getallfiles上获取 （http://you.donmain/ckfinder/core/connector/php/connector.php?command=Getallfiles）
 * @package Joinca\ZKUploader\Plugin\Backupfile
 */
class Backupfile extends CommandAbstract implements PluginInterface{

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
        $cpfile =  urldecode($_GET['cpfile']);

        $backends = $config->get('backends');
        $default = $backends['default']; //默认配置
        $default_root = $default['root'];

        $backup = $backends['backup']; //备份配置
        $backup_root = $backup['root'];

        if(!file_exists($backup_root)){
            return array('status' => 'faild', 'msg' => '移动存储设备未连接');
        }

        if(!file_exists($default_root.HDD_DIRECTORY.$cpfile)){
            return array('status' => 'faild', 'msg' => $cpfile.'文件不存在');
        }

        //创建复制目录
        @mkdir(dirname(BACKUP_FOLDER.$cpfile), $backup['chmodFolders'], true);

        if(copy($default_root.HDD_DIRECTORY.$cpfile, BACKUP_FOLDER.$cpfile)){
            chmod(BACKUP_FOLDER.$cpfile, $backup['chmodFiles']);
            return array('status' => 'ok', 'msg' => $cpfile.'文件备份成功');
        }else{
            //设备断开，设备容量已满 用户权限
            return array('status' => 'faild', 'msg' => '备份出现错误，请重试!');
        }
    }

}
