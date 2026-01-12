<?php

return [
    'menu' => [
        'ranking_player' => [
            'enabled' => true,
            'name' => 'Player Ranking',
            'image' => 'fa fa-users',
            'route' => 'ranking.player',
        ],
        'ranking_guild' => [
            'enabled' => true,
            'name' => 'Guild Ranking',
            'image' => 'fa fa-users',
            'route' => 'ranking.guild',
        ],
        'ranking_job' => [
            'enabled' => true,
            'name' => 'Job Ranking',
            'image' => 'fa fa-users',
            'route' => 'ranking.job',
        ],
        'ranking-honor' => [
            'enabled' => true,
            'name' => 'Honor Ranking',
            'image' => 'fa fa-users',
            'route' => 'ranking.honor',
        ],
        'ranking_unique' => [
            'enabled' => true,
            'name' => 'Unique Ranking',
            'image' => 'fa fa-users',
            'route' => 'ranking.unique',
        ],
        'ranking_unique-monthly' => [
            'enabled' => false,
            'name' => 'Unique Ranking (Monthly)',
            'image' => 'fa fa-users',
            'route' => 'ranking.unique-monthly',
        ],
        'ranking-fortress-player' => [
            'enabled' => false,
            'name' => 'Fortress War (Player)',
            'image' => 'fa fa-users',
            'route' => 'ranking.fortress-player',
        ],
        'ranking-fortress-guild' => [
            'enabled' => false,
            'name' => 'Fortress War (Guild)',
            'image' => 'fa fa-users',
            'route' => 'ranking.fortress-guild',
        ],
        'ranking-pvp-kd' => [
            'enabled' => false,
            'name' => 'Pvp K/D Ranking',
            'image' => 'fa fa-users',
            'route' => 'ranking.pvp-kd',
        ],
        'ranking-job-kd' => [
            'enabled' => false,
            'name' => 'Job K/D Ranking',
            'image' => 'fa fa-users',
            'route' => 'ranking.job-kd',
        ],
    ],
    'job_menu' => [
        'ranking_job_all' => [
            'enabled' => true,
            'name' => 'All',
            'image' => 'fa fa-users',
            'route' => 'ranking.job-all',
        ],
        'ranking_job_hunters' => [
            'enabled' => true,
            'name' => 'Hunters',
            'image' => 'fa fa-users',
            'route' => 'ranking.job-hunter',
        ],
        'ranking_job_thieves' => [
            'enabled' => true,
            'name' => 'Thieves',
            'image' => 'fa fa-users',
            'route' => 'ranking.job-thieve',
        ],
        'ranking_job_traders' => [
            'enabled' => true,
            'name' => 'Traders',
            'image' => 'fa fa-users',
            'route' => 'ranking.job-trader',
        ],
    ],
    'custom' => [
        'level' => [
            'enabled' => false,
            'name' => 'Level Ranking',
            'image' => 'fa fa-users',
            'route' => [
                'name' => 'ranking.custom',
                'params' => ['type' => 'level']
            ],
            'query' => 'SELECT TOP(25) Charname16 AS CharName, CharID, RefObjID, CurLevel AS Level, ExpOffset AS Exp
                         FROM SILKROAD_R_SHARD.._Char
                         WHERE CharID > 0
                         ORDER BY CurLevel DESC, ExpOffset DESC',
        ],
        'gold' => [
            'enabled' => false,
            'name' => 'Gold Ranking',
            'image' => 'fa fa-users',
            'route' => [
                'name' => 'ranking.custom',
                'params' => ['type' => 'gold']
            ],
            'query' => 'SELECT TOP(25) Charname16 AS CharName, CharID, RefObjID, CurLevel AS Level, RemainGold AS Gold
                         FROM SILKROAD_R_SHARD.._Char
                         WHERE CharID > 0
                         ORDER BY CurLevel DESC, RemainGold DESC',
        ],
    ],
    'hidden' => [
        'characters' => [
            '[GM]Eva',
            '[GM]m1xawy',
        ],
        'guilds' => [
            'RigidStaff',
        ],
    ],
    'extra' => [
        'character_status' => false,
        'character_build' => false,
        'character_buff' => false,
        'character_job' => false,
        'advanced_unique_tracker' => false,
        'pvp_kill_logs' => false,
        'job_kill_logs' => false,
        'item_plus_logs' => false,
        'item_drop_logs' => false,
    ],
    'uniques' => [
        'MOB_CH_TIGERWOMAN' => [
            'id' => 1954,
            'name' => 'Tiger Girl',
            'image' => 'images/tw_icon_unique.png',
            'points' => 1
        ],
        'MOB_OA_URUCHI' => [
            'id' => 1982,
            'name' => 'Uruchi',
            'image' => 'images/tw_icon_unique.png',
            'points' => 2
        ],
        'MOB_KK_ISYUTARU' => [
            'id' => 2002,
            'name' => 'Isyutaru',
            'image' => 'images/tw_icon_unique.png',
            'points' => 3
        ],
        'MOB_TK_BONELORD' => [
            'id' => 3810,
            'name' => 'Lord Yarkan',
            'image' => 'images/tw_icon_unique.png',
            'points' => 4
        ],
        'MOB_RM_TAHOMET' => [
            'id' => 3875,
            'name' => 'Demon Shaitan',
            'image' => 'images/tw_icon_unique.png',
            'points' => 5
        ],
        'MOB_AM_IVY' => [
            'id' => 14778,
            'name' => 'Captain Ivy',
            'image' => 'images/tw_icon_unique.png',
            'points' => 2
        ],
        'MOB_EU_KERBEROS' => [
            'id' => 5871,
            'name' => 'Cerberus',
            'image' => 'images/tw_icon_unique.png',
            'points' => 1
        ],
        'MOB_RM_ROC' => [
            'id' => 3877,
            'name' => 'Roc',
            'image' => 'images/tw_icon_unique.png',
            'points' => 15
        ],
        'MOB_TQ_WHITESNAKE' => [
            'id' => 14839,
            'name' => 'Medusa',
            'image' => 'images/tw_icon_unique.png',
            'points' => 10
        ],
    ],
    'hwan_level' => [
        0 => [
            1 => 'Captain',
            2 => 'General',
            3 => 'Senior General',
            4 => 'Chief General',
            5 => 'Vice Lord',
            6 => 'General Lord',
        ],
        1 => [
            1 => 'Knight',
            2 => 'Baronet',
            3 => 'Baron',
            4 => 'Count',
            5 => 'Marquis',
            6 => 'Duke',
        ],
    ],
    'skill_mastery' => [
        257 => [
            "name" => "Blade",
            "image" => "images/sro/skillmastery/china/mastery_sword.png"
        ],
        258 => [
            "name" => "Glavie",
            "image" => "images/sro/skillmastery/china/mastery_spear.png"
        ],
        259 => [
            "name" => "Bow",
            "image" => "images/sro/skillmastery/china/mastery_bow.png"
        ],
        273 => [
            "name" => "Cold",
            "image" => "images/sro/skillmastery/china/mastery_cold.png"
        ],
        274 => [
            "name" => "Lightning",
            "image" => "images/sro/skillmastery/china/mastery_lightning.png"
        ],
        275 => [
            "name" => "Fire",
            "image" => "images/sro/skillmastery/china/mastery_fire.png"
        ],
        276 => [
            "name" => "Force",
            "image" => "images/sro/skillmastery/china/mastery_gigong.png"
        ],
        277 => [
            "name" => "Recovery",
            "image" => "images/sro/skillmastery/china/mastery_water.png"
        ],
        513 => [
            "name" => "Warrior",
            "image" => "images/sro/skillmastery/europe/eu_warrior.png"
        ],
        514 => [
            "name" => "Wizard",
            "image" => "images/sro/skillmastery/europe/eu_wizard.png"
        ],
        515 => [
            "name" => "Rogue",
            "image" => "images/sro/skillmastery/europe/eu_rog.png"
        ],
        516 => [
            "name" => "Warlock",
            "image" => "images/sro/skillmastery/europe/eu_warlock.png"
        ],
        517 => [
            "name" => "Bard",
            "image" => "images/sro/skillmastery/europe/eu_bard.png"
        ],
        518 => [
            "name" => "Cleric",
            "image" => "images/sro/skillmastery/europe/eu_cleric.png"
        ],
    ],
    'top_image' => [
        1 => 'images/rank1.png',
        2 => 'images/rank2.png',
        3 => 'images/rank3.png',
    ],
    'honor_level' => [
        1 => 'images/com_honor_level_1.png',
        2 => 'images/com_honor_level_2.png',
        3 => 'images/com_honor_level_3.png',
        4 => 'images/com_honor_level_4.png',
        5 => 'images/com_honor_level_5.png',
    ],
    'job_type' => [
        1 => [
            'name' => 'Hunter',
            'small_image' => 'images/com_job_hunter.png',
            'image' => 'images/job_hunter_icon.png',
        ],
        2 => [
            'name' => 'Thief',
            'small_image' => 'images/com_job_thief.png',
            'image' => 'images/job_teaf_icon.png',
        ],
        3 => [
            'name' => 'Trader',
            'small_image' => 'images/com_job_merchant.png',
            'image' => 'images/job_trader_icon.png',
        ],
    ],
    'job_type_vsro' => [
        3 => [
            'name' => 'Hunter',
            'small_image' => 'images/com_job_hunter.png',
            'image' => 'images/job_hunter_icon.png',
        ],
        2 => [
            'name' => 'Thief',
            'small_image' => 'images/com_job_thief.png',
            'image' => 'images/job_teaf_icon.png',
        ],
        1 => [
            'name' => 'Trader',
            'small_image' => 'images/com_job_merchant.png',
            'image' => 'images/job_trader_icon.png',
        ],
    ],
    'vip_level' => [
        "level_access" => 4,
        "level" => [
            0 => [
                'name' => "Normal",
                'image' => "",
            ],
            1 => [
                'name' => "Iron",
                'image' => "images/viplevel_1.jpg",
            ],
            2 => [
                'name' => "Bronze",
                'image' => "images/viplevel_2.jpg",
            ],
            3 => [
                'name' => "Silver",
                'image' => "images/viplevel_3.jpg",
            ],
            4 => [
                'name' => "Gold",
                'image' => "images/viplevel_4.jpg",
            ],
            5 => [
                'name' => "Platinum",
                'image' => "images/viplevel_5.jpg",
            ],
            6 => [
                'name' => "VIP",
                'image' => "images/viplevel_6.jpg",
            ],
        ],
        "type" => [
            0 => "General",
            1 => "VIP",
            2 => "New",
            3 => "Returne",
            4 => "Free"
        ]
    ],
    'guild_authority' => [
        1 => 'Leader',
        2 => 'Deputy Commander',
        4 => 'Fortress War Administrator',
        8 => 'Production Administrator',
        16 => 'Training Administrator',
        32 => 'Military Engineer',
    ],
    'character_race' => [
        0 => [
            'name' => 'Chinese',
            'image' => 'images/com_kindred_china.png',
        ],
        1 => [
            'name' => 'Europe',
            'image' => 'images/com_kindred_europe.png',
        ],
    ],
    'character_image' => [
        1907 => "char_ch_man1.png",
        1908 => "char_ch_man2.png",
        1909 => "char_ch_man3.png",
        1910 => "char_ch_man4.png",
        1911 => "char_ch_man5.png",
        1912 => "char_ch_man6.png",
        1913 => "char_ch_man7.png",
        1914 => "char_ch_man8.png",
        1915 => "char_ch_man9.png",
        1916 => "char_ch_man10.png",
        1917 => "char_ch_man11.png",
        1918 => "char_ch_man12.png",
        1919 => "char_ch_man13.png",
        1920 => "char_ch_woman1.png",
        1921 => "char_ch_woman2.png",
        1922 => "char_ch_woman3.png",
        1923 => "char_ch_woman4.png",
        1924 => "char_ch_woman5.png",
        1925 => "char_ch_woman6.png",
        1926 => "char_ch_woman7.png",
        1927 => "char_ch_woman8.png",
        1928 => "char_ch_woman9.png",
        1929 => "char_ch_woman10.png",
        1930 => "char_ch_woman11.png",
        1931 => "char_ch_woman12.png",
        1932 => "char_ch_woman13.png",
        14717 => "char_eu_man1.png",
        14718 => "char_eu_man2.png",
        14719 => "char_eu_man3.png",
        14720 => "char_eu_man4.png",
        14721 => "char_eu_man5.png",
        14722 => "char_eu_man6.png",
        14723 => "char_eu_man7.png",
        14724 => "char_eu_man8.png",
        14725 => "char_eu_man9.png",
        14726 => "char_eu_man10.png",
        14727 => "char_eu_man11.png",
        14728 => "char_eu_man12.png",
        14729 => "char_eu_man13.png",
        14730 => "char_eu_woman1.png",
        14731 => "char_eu_woman2.png",
        14732 => "char_eu_woman3.png",
        14733 => "char_eu_woman4.png",
        14734 => "char_eu_woman5.png",
        14735 => "char_eu_woman6.png",
        14736 => "char_eu_woman7.png",
        14737 => "char_eu_woman8.png",
        14738 => "char_eu_woman9.png",
        14739 => "char_eu_woman10.png",
        14740 => "char_eu_woman11.png",
        14741 => "char_eu_woman12.png",
        14742 => "char_eu_woman13.png",
    ],
    'character_image_vsro' => [
        1907 => "char_ch_man1.png",
        1908 => "char_ch_man2.png",
        1909 => "char_ch_man3.png",
        1910 => "char_ch_man4.png",
        1911 => "char_ch_man5.png",
        1912 => "char_ch_man6.png",
        1913 => "char_ch_man7.png",
        1914 => "char_ch_man8.png",
        1915 => "char_ch_man9.png",
        1916 => "char_ch_man10.png",
        1917 => "char_ch_man11.png",
        1918 => "char_ch_man12.png",
        1919 => "char_ch_man13.png",
        1920 => "char_ch_woman1.png",
        1921 => "char_ch_woman2.png",
        1922 => "char_ch_woman3.png",
        1923 => "char_ch_woman4.png",
        1924 => "char_ch_woman5.png",
        1925 => "char_ch_woman6.png",
        1926 => "char_ch_woman7.png",
        1927 => "char_ch_woman8.png",
        1928 => "char_ch_woman9.png",
        1929 => "char_ch_woman10.png",
        1930 => "char_ch_woman11.png",
        1931 => "char_ch_woman12.png",
        1932 => "char_ch_woman13.png",
        14875 => "char_eu_man1.png",
        14876 => "char_eu_man2.png",
        14877 => "char_eu_man3.png",
        14878 => "char_eu_man4.png",
        14879 => "char_eu_man5.png",
        14880 => "char_eu_man6.png",
        14881 => "char_eu_man7.png",
        14882 => "char_eu_man8.png",
        14883 => "char_eu_man9.png",
        14884 => "char_eu_man10.png",
        14885 => "char_eu_man11.png",
        14886 => "char_eu_man12.png",
        14887 => "char_eu_man13.png",
        14888 => "char_eu_woman1.png",
        14889 => "char_eu_woman2.png",
        14890 => "char_eu_woman3.png",
        14891 => "char_eu_woman4.png",
        14892 => "char_eu_woman5.png",
        14893 => "char_eu_woman6.png",
        14894 => "char_eu_woman7.png",
        14895 => "char_eu_woman8.png",
        14896 => "char_eu_woman9.png",
        14897 => "char_eu_woman10.png",
        14898 => "char_eu_woman11.png",
        14899 => "char_eu_woman12.png",
        14900 => "char_eu_woman13.png",
    ],
];
