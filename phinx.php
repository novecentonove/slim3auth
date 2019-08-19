<?php

require_once __DIR__ . '/bootstrap/app.php';

$config = $container['settings']['db'];

return [
    'paths' => [
        'migrations' => 'database/migrations'
    ],
    'environments' => [
        'default_migration_table' => 'migrations',
        'default' => [
            'adapter' => $config['driver'],
            'host' => $config['host'],
            'name' => $config['database'],
            'user' => $config['username'],
            'pass' => $config['password']
        ]
    ]
];