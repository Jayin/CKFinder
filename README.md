http://docs.cksource.com/ckfinder3-php/quickstart.html


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



