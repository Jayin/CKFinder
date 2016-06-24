<?php

// +----------------------------------------------------------------------
// | Copyright (c) Zhutibang.Inc 2016 http://zhutibang.cn All rights reserved.
// +----------------------------------------------------------------------
// | Author: Jayin Ton <tonjayin@gmail.com>
// +----------------------------------------------------------------------

namespace Joinca\ZKUploader\Plugin\Getallfiles;


use Joinca\ZKUploader\Command\CommandAbstract;
use Joinca\ZKUploader\Plugin\PluginInterface;
use Symfony\Component\HttpFoundation\Request;
use Joinca\ZKUploader\Config;

/**
 *
 * API: http://you.donmain/ckfinder/core/connector/php/connector.php?command=Getallfiles
 * 获取存储目录下的所有文件
 *
 * @package Joinca\ZKUploader\Plugin\Getallfiles
 * @return array 文件列表
 */
class Getallfiles extends CommandAbstract implements PluginInterface{

    /**
     * Returns an array with the default configuration for this plugin. Any of
     * the plugin configuration options can be overwritten in the ZKUploader configuration file.
     *
     * @return array default plugin configuration
     */
    public function getDefaultConfig() {
        return [];
    }

    public function execute(Request $request, Config $config){
        $backends = $config->get('backends');
        $default = $backends['default']; //默认配置
        $root = $default['root'];

        // xxxx/userfiles/files
        $result = self::read_all_files($root.HDD_DIRECTORY);
        $files = self::compress_file_path($result['files'], $root.HDD_DIRECTORY);

        return count($files) > 0 ? $files : [];
    }

    /**
     * // 当文件夹以.开头，默认忽略
     *  获取一目录下的所有文件/文件夹
     * Finds path, relative to the given root folder, of all files and directories in the given directory and its sub-directories non recursively.
     * Will return an array of the form
     * array(
     *   'files' => [],
     *   'dirs'  => [],
     * )
     * @author sreekumar
     * @param string $root
     * @return array
     */
    static function read_all_files($root = '.'){
        $files  = array('files'=>array(), 'dirs'=>array());
        $directories  = array();
        $last_letter  = $root[strlen($root)-1];
        $root  = ($last_letter == '\\' || $last_letter == '/') ? $root : $root.DIRECTORY_SEPARATOR;

        $directories[]  = $root;

        while (sizeof($directories)) {
            $dir  = array_pop($directories);
            if(basename($dir) === PRIVATE_DIR) continue; // 当默认忽略 zkuploader文件（PRIVATE_DIR默认值为zkuploader文件）

            if ($handle = opendir($dir)) {
                while (false !== ($file = readdir($handle))) {
                    if ($file == '.' || $file == '..') {
                        continue;
                    }
                    $file  = $dir.$file;
                    if (is_dir($file)) {
                        $directory_path = $file.DIRECTORY_SEPARATOR;
                        array_push($directories, $directory_path);
                        $files['dirs'][]  = $directory_path;
                    } elseif (is_file($file)) {
                        $files['files'][]  = $file;
                    }
                }
                closedir($handle);
            }
        }
        return $files;
    }

    /**
     * 隐藏掉 root
     * @param $files
     * @param $root
     * @return array
     */
    static function compress_file_path($files, $root){
        $result = [];
        foreach($files as $index => $f){
            $result[] = str_replace($root, '', $f);
        }
        return $result;
    }
}