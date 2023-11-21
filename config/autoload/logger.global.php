<?php

use Monolog\Level;

return [
    'logger' => [
        'name' => 'logger',
        'handlers' => [
            'stream' => [
                'name' => 'php://stdout',
                'level' => Level::Debug
            ],
            'file' => [
                'name' => '/tmp/app.log',
                'level' => Level::Debug
            ],
        ]
    ]
];
