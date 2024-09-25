<?php

return [
    'mailgun' => [
        'api_key' => $_ENV['MAILGUN_API_KEY'],
        'domain' => $_ENV['MAILGUN_DOMAIN'],
        'from' => [
            'name' => 'Riconas GMBH',
            'email' => $_ENV['MAILGUN_FROM_EMAIL'],
        ],
    ],
];