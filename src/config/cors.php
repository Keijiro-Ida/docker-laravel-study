<?php

return [
    'paths' => ['*'], // ✅ sanctumのCSRFルートも入れる
    'allowed_methods' => ['*'],
    'allowed_origins' => ['http://localhost:3000'], // ✅ Reactからのアクセスを許可
    'allowed_headers' => ['*'],
    'exposed_headers' => [],
    'max_age' => 0,
    'supports_credentials' => true, // ✅ これが超重要！
];
