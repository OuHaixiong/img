<?php

defined('ROOT_PATH') || define('ROOT_PATH', realpath(dirname(__FILE__)));
defined('LIBRARIES_PATH') || define('LIBRARIES_PATH', realpath(ROOT_PATH . '/libraries')); // 后面不带 /

include_once LIBRARIES_PATH . '/Common/Image/Abstract.php';
include_once LIBRARIES_PATH . '/Common/Image.php';
include_once LIBRARIES_PATH . '/Common/Imagick.php';
var_dump($_SERVER);exit;
// 分析路径
$filePath = $_SERVER['REDIRECT_URL']; // /user_pic/15_10_15/fBjmVpy4151015101330_100x100_w_s.jpeg
$fileName = pathinfo($filePath, PATHINFO_FILENAME); // 文件名，不包括后缀
$extension = pathinfo($filePath, PATHINFO_EXTENSION); // 后缀，不包括点
$dirname = pathinfo($filePath, PATHINFO_DIRNAME); // 文件目录，不包括斜杠/
$path = trim($filePath, '/');
$position = strpos($path, '/');
$module = substr($path, 0, $position);
$arrayFileName = explode('_', $fileName);

// 查找对应的模块是否有该缩略图尺寸的配置

// 查看源文件是否存在
$originPath = ROOT_PATH . $dirname . '/origin/' . $arrayFileName[0] . '.' . $extension;
if (!is_file($originPath)) {
    die('源文件不存在！！！！');
//     exit();
}

$widthPlace = array('c'=>'center', 'w'=>'west', 'e'=>'east');
$heightPlace = array('c'=>'center', 'n'=>'north', 's'=>'south');

// 生成缩略图
$size = count($arrayFileName);
$boolean = false;
if ($size < 2) { // 原图的缩略图（不符合规范）
//     exit('不符合规范！！！！');
    die();
} else {
    $image = new Common_Image($originPath);
    $arrayWidthHeight = explode('x', $arrayFileName[1]);
    if ($size < 3) { // 仅缩略
        $image->thumbnail($arrayWidthHeight[0], $arrayWidthHeight[1]);
    } else { // 缩略后进行裁剪
        if (!array_key_exists($arrayFileName[2], $widthPlace)) {
            die('不符合规范！');
        }
        if ((!isset($arrayFileName[3])) || (!array_key_exists($arrayFileName[3], $heightPlace))) {
            $arrayFileName[3] = 'c';
        }
        $image->resize($arrayWidthHeight[0], $arrayWidthHeight[1], $widthPlace[$arrayFileName[2]], $heightPlace[$arrayFileName[3]]);
    }
    $boolean = $image->write(ROOT_PATH . $filePath);
}

// 输出缩略图
if ($boolean) {
    $image->show();
}
