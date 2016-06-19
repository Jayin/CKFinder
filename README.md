http://docs.cksource.com/ckfinder3-php/quickstart.html

## 快速部署

NOTE: 

- `dist/`目录下就是打包后的ckfinder

方法一：

```
$ cd path/to/dist/
$ php -S 127.0.0.1:8081 -t ./
```

访问`http://localhost:8081/`

2. `dist/`放到apache/nginx 的www根目录

访问： `http://your_domain:port/dist/index.html`

## 注意：
- 服务器允许上传文件的大小  

### Nginx

打开nginx主配置文件nginx.conf，一般在/usr/local/nginx/conf/nginx.conf这个位置，找到http{}段，修改或者添加
```
client_max_body_size 2m;
```

### Apache 

`httpd.conf`

```
LimitRequestBody 6550000
```

Apache 文档：

```
LimitRequestBody Directive

Description:	Restricts the total size of the HTTP request body sent from the client
Syntax:	LimitRequestBody bytes
Default:	LimitRequestBody 0 （默认是无限制）
Context:	server config, virtual host, directory, .htaccess
Override:	All
Status:	Core
Module:	core
```

`This directive specifies the number of bytes from 0 (meaning unlimited) to 2147483647 (2GB) that are allowed in a request body. `

### `php.ini`

```
post_max_size = 2M
upload_max_filesize = 2M
```

### `config.php`

```
$config['resourceTypes'][] = array(
    'name'              => 'Images',
    'directory'         => 'images',
    'maxSize'           => 0, //限制最大上传文件大小
    'allowedExtensions' => 'bmp,gif,jpeg,jpg,png',
    'deniedExtensions'  => '',
    'backend'           => 'default'
);
```

## 注意：权限

一般部署出现`Not found XX file` 一般是服务器没有权限创建文件

```
# 給Apache权限
$ chown -R www-data:www-data ./ckfinder  
```


## 版权信息

购买授权后请在`config.php`中添加授权码Name/key

```php
/*============================ License Key ============================================*/
// http://docs.cksource.com/ckfinder3-php/configuration.html#configuration_options_licenseKey

$config['licenseName'] = '';
$config['licenseKey']  = '';

```

## 新增备份

> **注意**： 请保证服务器有对移动存储设备的读写权限

- 默认的CKfinder映射在`config.php`这一层目录下的`zkuploader`
- 一键备份文件时会忽略`zkuploader`文件夹，配置见`config.php`
    ```php
    
        define('PRIVATE_DIR','zkuploader');
    
    ```
- 轮询移动设备是否挂载在/mnt/sdb1的时间间隔为8秒，在`plugins/backup/backup.js`中配置Connectstatus_Interval
- 挂载目录相关配置：根目录下的`config.php`
```
//外接设备挂载目录
define('EXTERNAL_FOLDER', '/mnt/sdb1');
//备份的目录文件名名
define('BACKUP_DIRECTORY', 'joinca_backup');
```
- 
