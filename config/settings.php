<?php

return [
    'forum_name' => env('FORUM_NAME', env('APP_NAME', 'AL-INSAF')),
    'forum_description' => env('FORUM_DESCRIPTION', 'Форум сообщества AL-INSAF'),
    'forum_keywords' => env('FORUM_KEYWORDS', 'форум, сообщество, обсуждения'),
    'posts_per_page' => env('POSTS_PER_PAGE', 10),
    'topics_per_page' => env('TOPICS_PER_PAGE', 20),
    'allow_registration' => env('ALLOW_REGISTRATION', true),
    'require_email_verification' => env('REQUIRE_EMAIL_VERIFICATION', false),
    'max_avatar_size' => env('MAX_AVATAR_SIZE', 2048), // KB
    'timezone' => env('APP_TIMEZONE', 'UTC'),
];
