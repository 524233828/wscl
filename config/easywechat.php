<?php

return [
    'debug'  => true,
    'app_id'  => 'wx355eaa042399666a',         // AppID
    'secret'  => '9511d3b38e4626b101499c1ec5454bc5',     // AppSecret
    'log' => [
        'level'      => 'debug',
        'permission' => 0777,
        'file'       => '../runtime/logs/easywechat.log',
    ],

    'oauth' => [
        'scopes'   => ['snsapi_base'],
        'callback' => '/',
    ],
];