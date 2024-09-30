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
    'mail_subjects' => [
        'password_recovery' => [
            'en' => 'Recover your password',
            'de' => 'Passwort wiederherstellen',
        ],
        'coworker_invitation' => [
            'en' => 'Invitation to collaborate on Riconas',
            'de' => 'Einladung zur Mitarbeit bei Riconas',
        ],
    ],
];