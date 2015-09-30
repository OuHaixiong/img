<?php

defined('ROOT_PATH') || define('ROOT_PATH', realpath(dirname(__FILE__)));
defined('LIBRARIES_PATH') || define('LIBRARIES_PATH', realpath(ROOT_PATH . '/libraries')); // 后面不带 /

include_once LIBRARIES_PATH . '/Common/Abstract.php';
include_once LIBRARIES_PATH . '/Common/Image.php';
include_once LIBRARIES_PATH . '/Common/Imagick.php';


