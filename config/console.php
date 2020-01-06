<?php

$config = [
    'id' => 'yii2-api-skeleton',
    'language' => 'ru-RU',
    'basePath' => dirname(__DIR__),
    'bootstrap' => ['log'],
    'timeZone' => 'UTC',
    'modules' => [
        'v1' => ['class' => 'app\modules\v1\Module'],
    ],
    'controllerNamespace' => 'app\commands',
    'components' => [
        'db' => [
            'class' => \yii\db\Connection::className(),
        ],
        'amqp' => [
            'class' => \devmustafa\amqp\components\Amqp::className(),
            'vhost' => 'vhost',
        ],
        'log' => [
            'traceLevel' => YII_DEBUG ? 3 : 0,
            'targets' => [
                [
                    'class' => 'yii\log\FileTarget',
                    'levels' => ['error', 'warning'],
                ],
            ],
        ],
        'cache' => [
            'class' => 'yii\caching\FileCache',
        ],
    ],
    'params' => [
        'adminEmail' => 'admin@host',
        'accept_exchg' => 'exchg.event.accept',
        'accept_queue' => 'queue.event.accept',
        'accept_type'  => 'fanout',
        'mongo' => true,
    ]
];

if (YII_ENV_DEV) {
    $config['bootstrap'][] = 'gii';
    $config['modules']['gii'] = [
        'class' => \yii\gii\Module::className(),
        // uncomment the following to add your IP if you are not connecting from localhost.
        'allowedIPs' => ['127.0.0.1', '::1'],
    ];
}

// GET DEVELOPER SETTINGS
if (file_exists(__DIR__ . '/config-local.php') && is_readable(__DIR__ . '/config-local.php')) {

    $config = \yii\helpers\ArrayHelper::merge($config, require __DIR__ . '/config-local.php');
}

return $config;