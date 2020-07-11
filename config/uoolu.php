<?php

declare(strict_types=1);

return [
    'appid' => 'tt8646f0fb196ce1d7',
    'secret' => '50a5064534ee33e311050bb378008ea1670f788f',
    'pages' => [
        'pages/index/index' => '首页',
        'pages/my/my' => '我的',
        'pages/detail/detail' => '房屋详情',
        'pages/article/article' => '文章',
        'pages/search/search' => '搜索',
        'pages/search-result/search-result' => '搜索结果',
        'pages/index/basic/choose' => '一键选房',
        'pages/index/basic/recent' => '一键收租',
        'pages/my/basic/collection' => '我的收藏',
        'pages/my/basic/like' => '我的点赞',
        'pages/my/basic/history' => '我的浏览',
        'pages/my/basic/share' => '我的分享',
        'pages/my/basic/mapping' => '我的匹配',
        'pages/my/basic/mapping-result' => '我的匹配结果'
    ],
    'captcha' => [
        'appid' =>  'ngus29G81YkOjk4tM',
        'secret' => 'Ab8Z1NhVTap2y4qNtetzihKfzWMyI5yf4A4yupJX',
        'area_code' => '86'
    ],
    'column' => [
        // 房源字段
        'name' => '房源名称',
        'desc' => '泰国曼谷',
        'location' => '房源位置',
        'keywords' => '房源标签',
        'type' => '房源类型',
        'recommend' => '推荐语',
        'price' => '房源价格',
        'recent' => '房产租金',
        'down_payment' => '房产首付',
        'rise' => '近一年涨幅',
        'country' => '国家',
        'province' => '省份',
        'model' => '户型介绍',
        'basic_info' => '基础信息',
        'first_image' => '首图',
        'group_images' => '组图',
        'recent_information' => '租房信息',
        'graphic_information' => '图文信息',

        // 文章字段
        'title' => '文章名称',
        'sub_title' => '文章二级标题',
        'user_name' => '用户名称',
        'user_avatar' => '发布人头像',
        'content' => '文章详情'
    ],
    'url' => [
        // 房源详情
        'house_url' => 'https://api.uoolu.com/ext/sync/house?id=',
        // 文章详情
        'article_url' => 'https://api.uoolu.com/ext/sync/article?id=',
        // 国家
        'city_url' => 'https://api.uoolu.com/ext/country/?locale=cn',
        // 配置
        'config' => 'https://api.uoolu.com/ext/config/',
        // 表单
        'form' => 'https://api.uoolu.com/ext/sync/customer/'
    ],
    'country' => [
        [
            'label' => '泰国',
            'value' => '20',
        ],
        [
            'label' => '美国',
            'value' => '6',
        ],
        [
            'label' => '日本',
            'value' => '17',
        ],
        [
            'label' => '越南',
            'value' => '195',
        ],
        [
            'label' => '菲律宾',
            'value' => '165',
        ],
        [
            'label' => '澳大利亚',
            'value' => '16',
        ],
        [
            'label' => '阿拉酋',
            'value' => '139',
        ],
        [
            'label' => '英国',
            'value' => '11',
        ],
        [
            'label' => '马来西亚',
            'value' => '18',
        ],
        [
            'label' => '柬埔寨',
            'value' => '161',
        ],
        [
            'label' => '希腊',
            'value' => '1013',
        ],
        [
            'label' => '新西兰',
            'value' => '177',
        ],
        [
            'label' => '新加坡',
            'value' => '152',
        ],
        [
            'label' => '加拿大',
            'value' => '7',
        ],
        [
            'label' => '意大利',
            'value' => '13',
        ],
        [
            'label' => '西班牙',
            'value' => '14',
        ],
        [
            'label' => '葡萄牙',
            'value' => '15',
        ],
        [
            'label' => '德国',
            'value' => '119',
        ]
    ]
];
