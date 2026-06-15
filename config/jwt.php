<?php

declare(strict_types=1);

return [
    'secret' => env('JWT_SECRET'),

    'algorithm' => env('JWT_ALGORITHM', 'HS256'),
];
