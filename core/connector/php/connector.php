<?php

require_once __DIR__ . '/vendor/autoload.php';

use Joinca\ZKUploader\ZKUploader;

$zkuploader = new ZKUploader(__DIR__ . '/../../../config.php');

$zkuploader->run();
