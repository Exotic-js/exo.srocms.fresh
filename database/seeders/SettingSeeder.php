<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class SettingSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $settings = [

            // ── General ───────────────────────────────────────────────────────
            'site_title'        => 'Silkroad Online',
            'site_desc'         => "Silkroad Online is a World's first blockbuster Free to play MMORPG. Silkroad Online puts players deep into ancient Chinese, Islamic, and European civilization. Enjoy Silkroad's hardcore PvP, personal dungeon system, never ending fortress war and be the top of the highest heroes!",
            'site_url'          => 'https://localhost',
            'site_favicon'      => 'images/favicon.ico',
            'site_logo'         => 'images/logo.png',
            'hero_background'   => 'images/bg.jpg',
            'max_level'         => 140,
            'max_player'        => 3500,
            'fake_player'       => 0,
            'dark_mode'         => 'switch',
            'default_locale'    => 'switch',
            'locale'            => 'en',
            'theme'             => 'default',
            'timezone'          => 'Africa/Cairo',
            'update_type'       => 'standard',
            'disable_register'  => 0,
            'register_confirm'  => 0,
            'duplicate_email'   => 0,
            'agree_terms'       => 0,

            // ── Mail ──────────────────────────────────────────────────────────
            'mail' => json_encode([
                'MAIL_MAILER'       => 'log',
                'MAIL_HOST'         => '127.0.0.1',
                'MAIL_PORT'         => '2525',
                'MAIL_SCHEME'       => 'null',
                'MAIL_USERNAME'     => 'null',
                'MAIL_PASSWORD'     => 'null',
                'MAIL_FROM_ADDRESS' => 'hello@example.com',
                'MAIL_FROM_NAME'    => 'Silkroad Online',
            ]),

        ];

        foreach ($settings as $key => $value) {
            DB::table('settings')->updateOrInsert(
                ['key'   => $key],
                ['value' => $value]
            );
        }
    }
}
