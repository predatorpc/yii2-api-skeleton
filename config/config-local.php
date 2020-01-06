<?php

/*
 *
 * config for DB and so on
 *
 */

return [
    'components' => [
        'db' => [
            'class' => 'yii\db\Connection',
            'dsn' => 'mysql:host=mysql;dbname=testdb',
            'username' => 'admin',
            'password' => 'adminpass',
            'charset' => 'utf8',
        ],
        'amqp' => [
            'host' => 'rabbitmq',
            'port' => 5672,
            'user' => 'admin',
            'password' => 'adminpass',
        ]
    ],
];