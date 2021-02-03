<?php

return [
    'publishable_key' => [
        'xtype' => 'textfield',
        'value' => '',
        'area' => 'stripe_main',
    ],
    'secret_key' => [
        'xtype' => 'textfield',
        'value' => '',
        'area' => 'stripe_main',
    ],
    'currency' => [
        'xtype' => 'textfield',
        'value' => 'usd',
        'area' => 'stripe_main',
    ],
    'success_url' => [
        'xtype' => 'textfield',
        'value' => '/assets/components/stripe/success.php',
        'area' => 'stripe_main',
    ],
    'cancel_url' => [
        'xtype' => 'textfield',
        'value' => '/assets/components/stripe/cancel.php',
        'area' => 'stripe_main',
    ],
    'confirm_page' => [
        'xtype' => 'textfield',
        'value' => '',
        'area' => 'stripe_main'
    ]
];