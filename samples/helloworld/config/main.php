<?php

return [
    'id' => 'Yii2 Queue Test',
    'basePath' => dirname(__DIR__),
    'components' => [
        'swfClient' => [
            'class' => 'UrbanIndo\\Yii2\\Aws\\Swf\\Client',
            'domain' => 'HelloWorld',
            'workflowNamespace' => 'app/components/swf/workflows',
            'activityNamespace' => 'app/components/swf/activities',
            'config' => [
                'region' => 'ap-southeast-1'
            ]
        ]
    ]
];
