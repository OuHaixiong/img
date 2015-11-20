<?php

/**
 * 各模块对应需要生成的缩略图尺寸
 */
return array(
    'product_list' => array(), // 产品列表
    'user_pic' => array( // 用户头像
        '80x80', // 等比缩放为80x80最大宽度或高度不会超过80
        '100x100' => array('tooWideCutPosition'=>'e', 'tooHighCutPosition'=>'n'), // 等比缩放后进行裁剪，宽度和高度为100，如果宽度超过居右裁剪，如果高度超过居上裁剪
        '50x', // 等比缩放为宽度50，高度任意 (注意格式一定要这样x不能少)
        'X120', // 等比缩放为高度为120，宽度任意(X可大写，也可以小写)
        'x208' => array('tooWideCutPosition'=>'w', 'tooHighCutPosition'=>'s'), 
        '50X318'
    ),
);
