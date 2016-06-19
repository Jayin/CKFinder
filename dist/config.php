<?php


// Production
 error_reporting(E_ALL & ~E_DEPRECATED & ~E_STRICT);
 ini_set('display_errors', 0);

// Development
//error_reporting(E_ALL);
//ini_set('display_errors', 1);

//外接设备挂载目录
define('EXTERNAL_FOLDER', '/mnt/sdb1');
//备份的目录文件名名
define('BACKUP_DIRECTORY', 'joinca_backup');
//备份目录
define('BACKUP_FOLDER', EXTERNAL_FOLDER.'/'.BACKUP_DIRECTORY.'/'); //注意必须以/结尾
define('PRIVATE_DIR','zkuploader'); //私有目录，不会被备份

/*============================ General Settings =======================================*/

$config = array();

/*============================ Enable PHP Connector HERE ==============================*/

$config['authentication'] = function () {
    return true;
};

/*============================ License Key ============================================*/
$config['licenseName'] = 'img.joinca.com';
$config['licenseKey']  = 'S9C9-YHBK-41YE-V5MY-RA5J-Q52N-SJCK';

/*============================  Internal Directory ============================*/
$config['privateDir'] = array(
    'backend' => 'default',
    'tags'   => PRIVATE_DIR.'/tags', //PRIVATE_DIR.(zkuploader) 目录不会被备份
    'logs'   => PRIVATE_DIR.'/logs',
    'cache'  => PRIVATE_DIR.'/cache',
    'thumbs' => PRIVATE_DIR.'/cache/thumbs',
);

/*============================ Images and Thumbnails ==================================*/

$config['images'] = array(
    'maxWidth'  => 1600,
    'maxHeight' => 1200,
    'quality'   => 80,
    'sizes' => array(
        'small'  => array('width' => 480, 'height' => 320, 'quality' => 80),
        'medium' => array('width' => 600, 'height' => 480, 'quality' => 80),
        'large'  => array('width' => 800, 'height' => 600, 'quality' => 80)
    )
);

/*=================================== Backends ========================================*/

$config['backends'][] = array(
    'name'         => 'default',
    'adapter'      => 'local',
    'baseUrl'      => '/zkuploader/userfiles/',
     'root'         => __DIR__.'/zkuploader/userfiles/',//当前目录下，//注意必须以/结尾 // Can be used to explicitly set the user files directory.
    'chmodFiles'   => 0644,
    'chmodFolders' => 0755,
    'filesystemEncoding' => 'UTF-8',
);

//外置存储
$config['backends'][] = array(
    'name'         => 'backup',
    'adapter'      => 'local',
    'baseUrl'      => '/',
    'root'         => EXTERNAL_FOLDER, // Can be used to explicitly set the user files directory.
    'chmodFiles'   => 0644,
    'chmodFolders' => 0755,
    'filesystemEncoding' => 'UTF-8',
);

/*================================ Resource Types =====================================*/

$config['defaultResourceTypes'] = '';

$config['resourceTypes'][] = array(
    'name'              => '文件', // Single quotes not allowed.
    'directory'         => 'files',
    'maxSize'           => 0,
    'allowedExtensions' => '',
    'deniedExtensions'  => 'php,sh',
    'backend'           => 'default'
);


//$config['resourceTypes'][] = array(
//    'name'              => '图片',
//    'directory'         => 'images',
//    'maxSize'           => 0,
//    'allowedExtensions' => '',
//    'deniedExtensions'  => 'php,sh',
//    'backend'           => 'default'
//);

//外置存储
if(file_exists(EXTERNAL_FOLDER)){  //
    $config['resourceTypes'][] = array(
        'name'              => '移动存储', //请不要随意修改，受影响的文件有：plugins/backup/backup.js
        'directory'         => BACKUP_DIRECTORY,
        'maxSize'           => 0,
        'allowedExtensions' => '',
        'deniedExtensions'  => 'php,sh',
        'backend'           => 'backup'
    );
}



/*================================ Access Control =====================================*/

$config['roleSessionVar'] = 'ZKUploader_UserRole';

$config['accessControl'][] = array(
    'role'                => '*',
    'resourceType'        => '*',
    'folder'              => '/',

    'FOLDER_VIEW'         => true,
    'FOLDER_CREATE'       => true,
    'FOLDER_RENAME'       => true,
    'FOLDER_DELETE'       => true,

    'FILE_VIEW'           => true,
    'FILE_CREATE'         => true,
    'FILE_RENAME'         => true,
    'FILE_DELETE'         => true,

    'IMAGE_RESIZE'        => true,
    'IMAGE_RESIZE_CUSTOM' => true
);


/*================================ Other Settings =====================================*/

$config['overwriteOnUpload'] = false;
$config['checkDoubleExtension'] = true;
$config['disallowUnsafeCharacters'] = false;
$config['secureImageUploads'] = true;
$config['checkSizeAfterScaling'] = true;
$config['htmlExtensions'] = array('html', 'htm', 'xml', 'js');
$config['hideFolders'] = array('.*', 'CVS', '__thumbs');
$config['hideFiles'] = array('.*');
$config['forceAscii'] = false;
$config['xSendfile'] = false;

$config['debug'] = false;

/*==================================== Plugins ========================================*/
$config['pluginsDirectory'] = __DIR__ . '/plugins';
$config['plugins'] = array(
    'Connectstatus', 'Backupfile', 'Getallfiles'
);

/*================================ Cache settings =====================================*/
$config['cache'] = array(
    'imagePreview' => 24 * 3600,
    'thumbnails'   => 24 * 3600 * 365,
    'proxyCommand' => 0
);

/*============================ Temp Directory settings ================================*/
$config['tempDirectory'] = sys_get_temp_dir();

/*============================ Session Cause Performance Issues =======================*/
$config['sessionWriteClose'] = true;

/*================================= CSRF protection ===================================*/
$config['csrfProtection'] = true;

/*============================== End of Configuration =================================*/

// Config must be returned - do not change it.
return $config;
