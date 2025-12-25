<?php

return [
    // After this time(minutes) instances will be removed from the database and file system
    'lifetime' => env('SESSION_LIFETIME', 120),
    'storage' => 'private',
];