<?php

return [
    'server' => [
        'version' => env('SRO_VERSION', 'iSRO'), // or 'vSRO'
        //'saltKey' => 'eset5ag.nsy-g6ky5.mp',
        //'WebMallPass' => 'ISRO-R Development',
        //'WebMallAddr' => "http://webmall.luxor-online.com/gateway.asp"
    ],
    'cache' => [
        'account_info' => 5, //5 seconds
        'online_counter' => 1, //1 minute
        'event_schedule' => 10080, //1 weeek
        'fortress_history' => 10080, //1 weeek
        'unique_history' => 10, //10 minutes
        'globals_history' => 10, //10 minutes
        'character_info' => 1440, //1 day
        'guild_info' => 1440, //1 day
        'ranking_player' => 60, //1 Hours
        'ranking_guild' => 60, //1 Hours
        'ranking_unique' => 60, //1 Hours
        'ranking_unique_monthly' => 60, //1 Hours
        'ranking_job' => 60, //1 Hours
        'ranking_honor' => 60, //1 Hours
        'ranking_fortress_player' => 60, //1 Hours
        'ranking_fortress_guild' => 60, //1 Hours
    ],
    'languages' => [
        'en' => [
            'name' => 'English',
            'flag' => 'gb'
        ],
        'tr' => [
            'name' => 'Türkçe',
            'flag' => 'tr',
        ],
        'ar' => [
            'name' => 'العربية',
            'flag' => 'sa',
        ],
        'es' => [
            'name' => 'Español',
            'flag' => 'es',
        ],
        'de' => [
            'name' => 'Deutsch',
            'flag' => 'de',
        ],
        'zh_CN' => [
            'name' => '简体中文',
            'flag' => 'cn',
        ],
    ],
    'homepage' => [
        'type' => 'landing', //blog or landing
        'news_limit' => 3,
    ],
    'referral' => [
        'enabled' => true,
        'reward_points' => 5, //5 Default, 0 = No eward
        'minimum_redeem' => 25,
    ],
    'tickets' => [
        'enabled' => true,
        'categories' => [
            'sales' => 'Sales',
            'bugs' => 'Bugs',
            'other' => 'Other',
        ]
    ],
    'sliders' => [
        0 => [
            'title' => 'Example headline',
            'title_color' => '#fff',
            'desc' => 'Some representative placeholder content for the first slide of the carousel.',
            'desc_color' => '#fff',
            'image' => 'https://wallpapercave.com/wp/wp7441040.jpg',
            'btn_label' => 'Sign Up',
            'btn_url' => '#',
        ],
        1 => [
            'title' => 'Example headline',
            'title_color' => '#fff',
            'desc' => 'Some representative placeholder content for the first slide of the carousel.',
            'desc_color' => '#fff',
            'image' => 'https://wallpapercave.com/wp/wp7441040.jpg',
            'btn_label' => 'Play Now',
            'btn_url' => '#',
        ],
        2 => [
            'title' => 'Example headline',
            'title_color' => '#fff',
            'desc' => 'Some representative placeholder content for the first slide of the carousel.',
            'desc_color' => '#fff',
            'image' => 'https://wallpapercave.com/wp/wp7441040.jpg',
            'btn_label' => 'Download Now',
            'btn_url' => '#',
        ],
    ],
    'footer' => [
        'general' => [
            0 => [
                'name' => 'Home',
                'url' => '#',
                'image' => '',
            ],
            1 => [
                'name' => 'Privacy Policy',
                'url' => '#',
                'image' => '',
            ],
            2 => [
                'name' => 'Terms & Conditions',
                'url' => '#',
                'image' => '',
            ],
        ],
        'social' => [
            0 => [
                'name' => 'Facebook',
                'url' => 'https://www.facebook.com/',
                'image' => '<i class="fab fa-facebook-f"></i>',
            ],
            1 => [
                'name' => 'Discord',
                'url' => 'https://discord.com/',
                'image' => '<i class="fab fa-discord"></i>',
            ],
            2 => [
                'name' => 'Youtube',
                'url' => 'https://www.youtube.com/',
                'image' => '<i class="fab fa-youtube"></i>',
            ],
        ],
        'backlink' => [
            0 => [
                'name' => 'Elitepvpers',
                'url' => 'https://www.elitepvpers.com/forum/sro-pserver-advertising/',
                'image' => 'https://www.elitepvpers.com/images/logo.png',
            ],
            1 => [
                'name' => 'SIlkroad4arab',
                'url' => 'https://www.silkroad4arab.com/vb/forumdisplay.php?f=85',
                'image' => 'https://www.silkroad4arab.com/vb/sawaweb/images/logo.png',
            ],
            2 => [
                'name' => 'SroCave',
                'url' => 'https://srocave.com/forum/sro-private-server-advertising.34/',
                'image' => 'https://srocave.com/data/assets/logo/SCLogo.png',
            ],
        ],
    ],
];
