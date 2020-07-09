<?php

declare(strict_types=1);

return [
    'bind' => [
        'UserLogin' => 'app\admin\event\UserLogin',
    ],

    'listen' => [
        'AppInit'  => [],
        'HttpRun'  => [],
        'HttpEnd'  => [],
        'LogLevel' => [],
        'LogWrite' => [],
    ],

    'subscribe' => [
        'app\admin\subscribe\User',
    ],
];
