<?php
header('Content-Type:text/html;charset=utf-8'); //定义字符集（如果报错统一调用404的函数进行渲染，就不需要这个了）

// 下面是使用 imagick 处理图片
defined('ROOT_PATH') || define('ROOT_PATH', realpath(dirname(__FILE__))); // 后面不带 /
defined('LIBRARIES_PATH') || define('LIBRARIES_PATH', realpath(ROOT_PATH . '/libraries')); // 后面不带 /
defined('CONFIG_PATH') || define('CONFIG_PATH', ROOT_PATH . '/Configs'); // 定义config文件的目录(不包括/)
defined('W_JOIN_H') || define('W_JOIN_H', 'x'); // 连接宽和高的字符串

include_once LIBRARIES_PATH . '/Common/Image/Abstract.php';
include_once LIBRARIES_PATH . '/Common/Imagick.php';
include_once LIBRARIES_PATH . '/Common/Config.php';

// 分析路径  http://img.mvc.com/user_pic/15_11_19/lEUZ3utS151119111110_x120_w_s.jpeg
$filePath = $_SERVER['REQUEST_URI']; // /user_pic/15_10_15/fBjmVpy4151015101330_100x100_w_s.jpeg?csc86=861
$filePath = preg_replace('/\?.*/', '', $filePath);
$fileName = pathinfo($filePath, PATHINFO_FILENAME); // 文件名，不包括后缀
$extension = pathinfo($filePath, PATHINFO_EXTENSION); // 后缀，不包括点
$dirname = pathinfo($filePath, PATHINFO_DIRNAME); // 文件目录，最后不包括斜杠/
$path = trim($filePath, '/');
$position = strpos($path, '/');
$module = substr($path, 0, $position);
$arrayFileName = explode('_', $fileName);
$size = count($arrayFileName);
if ($size < 2) { // 原图的缩略图（不符合规范）
    // TODO 抛404错误页面
    die('不符合规范！！！！');
}

// 查找对应的模块是否有该缩略图尺寸的配置
$moduleConfig = Common_Config::getImgThumbnail($module);
if (empty($moduleConfig)) {
    // TODO 抛404错误页面
    exit('不存在此模块的缩略图配置或配置为空');
}
$isExist = false;
$arrayFileName[1] = str_replace('X', W_JOIN_H, $arrayFileName[1]);
foreach ($moduleConfig as $k=>$v) {
    if (is_array($v)) { // 有裁剪的，取“键”
        $k = str_replace('X', W_JOIN_H, $k);
        if ($k == $arrayFileName[1]) {
            $isExist = true;
            break;
        }
    } else { // 无需裁剪的，取“值”
        $v = str_replace('X', W_JOIN_H, $v);
        if ($v == $arrayFileName[1]) {
            $isExist = true;
            break;
        }
    }
}
// TODO 目前不精确到裁剪，只看尺寸
if (!$isExist) { // 系统不支持此缩略图
    die('不存在此尺寸的缩略图');
}

// 查看源文件是否存在
$originPath = ROOT_PATH . $dirname . '/origin/' . $arrayFileName[0] . '.' . $extension;
if (!is_file($originPath)) {
// TODO 可以返回 404 not find
    exit('源文件不存在！！！！');
}

$widthPlace = array('c'=>'center', 'w'=>'west', 'e'=>'east');
$heightPlace = array('c'=>'center', 'n'=>'north', 's'=>'south');

// 生成缩略图
$boolean = false;
$image = new Common_Imagick($originPath);
$arrayWidthHeight = explode(W_JOIN_H, $arrayFileName[1]);
// 没有写 x 是不对的
if (sizeof($arrayWidthHeight) != 2) { // 宽和高一点要设对
    // TODO 可以返回 404 not find
    die('宽、高不符合规范');
}
$arrayWidthHeight[0] = (int) $arrayWidthHeight[0];
$arrayWidthHeight[1] = (int) $arrayWidthHeight[1];
if ($size < 3) { // 仅缩略
    $image->thumbnail($arrayWidthHeight[0], $arrayWidthHeight[1]);
} else { // 缩略后进行裁剪
    if (! array_key_exists($arrayFileName[2], $widthPlace)) {
        // TODO 可以返回 404 not find
        die('不符合规范！');
    }
    if (isset($arrayFileName[3])) { // 有这个参数，需要符合规范
        if (! array_key_exists($arrayFileName[3], $heightPlace)) {
            // TODO 可以返回 404 not find
            die('不符合规范！');
        }
    } else { // 没有，默认为 c
        $arrayFileName[3] = 'c';
    }
    $image->resize($arrayWidthHeight[0], $arrayWidthHeight[1], $widthPlace[$arrayFileName[2]], $heightPlace[$arrayFileName[3]]);
}
$boolean = $image->write(ROOT_PATH . $filePath);

if ($boolean) { // 输出缩略图
    $image->show();
} else { // 不正常的流程，抛异常（文件没有写的权限等）
    // TODO 记录日记，友好提示
    throw new Exception($image->getError());
    
}
