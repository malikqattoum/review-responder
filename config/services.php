<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Third Party Services
    |--------------------------------------------------------------------------
    */

    'postmark' => [
        'key' => env('POSTMARK_API_KEY'),
    ],

    'resend' => [
        'key' => env('RESEND_API_KEY'),
    ],

    'ses' => [
        'key' => env('AWS_ACCESS_KEY_ID'),
        'secret' => env('AWS_SECRET_ACCESS_KEY'),
        'region' => env('AWS_DEFAULT_REGION', 'us-east-1'),
    ],

    'slack' => [
        'notifications' => [
            'bot_user_oauth_token' => env('SLACK_BOT_USER_OAUTH_TOKEN'),
            'channel' => env('SLACK_BOT_USER_DEFAULT_CHANNEL'),
        ],
    ],

    'google' => [
        'places_api_key' => env('GOOGLE_PLACES_API_KEY'),
    ],

    'yelp' => [
        'fusion_api_key' => env('YELP_FUSION_API_KEY'),
    ],

    'ai' => [
        'provider' => env('AI_PROVIDER', 'openrouter'),
        
        // OpenRouter (supports many models including MiniMax, OpenAI, Claude, etc.)
        'openrouter_api_key' => env('OPENROUTER_API_KEY'),
        'openrouter_model' => env('OPENROUTER_MODEL', 'openai/gpt-4o-mini'),
        
        // MiniMax (Chinese AI)
        'minimax_api_key' => env('MINIMAX_API_KEY'),
        'minimax_model' => env('MINIMAX_MODEL', 'minimax/-01'),
    ],

];
