<?php

return [
    'key' => $_ENV['JWT_SECRET'],
    'algo' => 'HS256',
    'issuer' => 'riconas',
    'expiration_time_seconds' => 7200,
];