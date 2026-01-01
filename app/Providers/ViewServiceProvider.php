<?php

namespace App\Providers;

use App\Models\Pages;
use App\Models\SRO\Account\ShardCurrentUser;
use App\Models\SRO\Log\LogChatMessage;
use App\Models\SRO\Log\LogEventChar;
use App\Models\SRO\Log\LogEventItem;
use App\Models\SRO\Log\LogInstanceWorldInfo;
use App\Models\SRO\Shard\Char;
use App\Models\SRO\Shard\Guild;
use App\Models\SRO\Shard\SiegeFortress;
use App\Services\ScheduleService;
use Illuminate\Database\QueryException;
use Illuminate\Foundation\Support\Providers\RouteServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\View;

class ViewServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        try {
            View::composer(['layouts.header', 'layouts.navigation'], function ($view) {
                $view->with('pages', Pages::getPageNames());
            });

            $languages = config('global.languages');
            View::composer(['layouts.header', 'layouts.navigation'], function ($view) use ($languages) {
                $view->with('languages', $languages);
            });

            $sliders = config('global.sliders');
            View::composer(['partials.carousel'], function ($view) use ($sliders) {
                $view->with('sliders', $sliders);
            });

            $footer = config('global.footer');
            View::composer(['layouts.footer'], function ($view) use ($footer) {
                $view->with('footer', $footer);
            });

            View::composer(['partials.online-counter'], function ($view) {
                $view->with([
                    'onlinePlayer' => ShardCurrentUser::getOnlineCounter(),
                    'maxPlayer' => config('settings.max_player', 1000),
                    'fakePlayer' => config('settings.fake_player', 0)
                ]);
            });

            $discord = config('widgets.discord');
            if($discord['enabled']) {
                View::composer(['partials.discord'], function ($view) use ($discord) {
                    $view->with('discord', $discord);
                });
            }

            $serverInfo = config('widgets.server_info');
            if($serverInfo['enabled']) {
                View::composer(['partials.server-info'], function ($view) use ($serverInfo) {
                    $view->with('serverInfo', $serverInfo);
                });
            }

            $eventScheduleConfig = config('widgets.event_schedule');
            if($eventScheduleConfig['enabled']) {
                View::composer(['partials.event-schedule'], function ($view) use ($eventScheduleConfig) {
                    $view->with([
                        'eventSchedule' => ScheduleService::getEventSchedules(),
                        'eventScheduleConfig' => $eventScheduleConfig
                    ]);
                });
            }

            $fortressWarConfig = config('widgets.fortress_war');
            if($fortressWarConfig['enabled']) {
                View::composer(['partials.fortress-war'], function ($view) use ($fortressWarConfig) {
                    $view->with([
                        'fortressWar' => SiegeFortress::getFortressWar(),
                        'fortressWarConfig' => $fortressWarConfig
                    ]);
                });
            }

            $globalsHistoryConfig = config('widgets.globals_history');
            if($globalsHistoryConfig['enabled']) {
                View::composer(['partials.globals-history'], function ($view) use ($globalsHistoryConfig) {
                    $view->with([
                        'globalsHistory' => LogChatMessage::getGlobalsHistory($globalsHistoryConfig['limit']),
                        'globalsHistoryConfig' => $globalsHistoryConfig
                    ]);
                });
            }

            $uniqueHistoryConfig = config('widgets.unique_history');
            $uniquesList = config('ranking.uniques');
            if($uniqueHistoryConfig['enabled']) {
                View::composer(['partials.unique-history'], function ($view) use ($uniqueHistoryConfig, $uniquesList) {
                    $view->with([
                        'uniqueHistory' => LogInstanceWorldInfo::getUniquesKill($uniqueHistoryConfig['limit']),
                        'uniqueHistoryConfig' => $uniqueHistoryConfig,
                        'uniquesList' => $uniquesList
                    ]);
                });
            }

            $topPlayerConfig = config('widgets.top_player');
            $topImage = config('ranking.top_image');
            if($topPlayerConfig['enabled']) {
                View::composer(['partials.top-player'], function ($view) use ($topPlayerConfig, $topImage) {
                    $view->with([
                        'topPlayer' => Char::getPlayerRanking($topPlayerConfig['limit']),
                        'topPlayerConfig' => $topPlayerConfig,
                        'topImage' => $topImage
                    ]);
                });
            }

            $topGuildConfig = config('widgets.top_guild');
            $topImage = config('ranking.top_image');
            if($topGuildConfig['enabled']) {
                View::composer(['partials.top-guild'], function ($view) use ($topGuildConfig, $topImage) {
                    $view->with([
                        'topGuild' => Guild::getGuildRanking($topGuildConfig['limit']),
                        'topGuildConfig' => $topGuildConfig,
                        'topImage' => $topImage
                    ]);
                });
            }

            $soxPlusConfig = config('widgets.sox_plus');
            $soxPlusRankingConfig = config('ranking.extra.item_logs.plus');
            if($soxPlusConfig['enabled']) {
                View::composer(['partials.sox-plus'], function ($view) use ($soxPlusConfig, $soxPlusRankingConfig) {
                    $view->with([
                        'soxPlus' => LogEventItem::getLogEventItem('plus', $soxPlusRankingConfig['plus'], $soxPlusRankingConfig['degree'], $soxPlusRankingConfig['type'], null, $soxPlusConfig['limit']),
                        'soxPlusConfig' => $soxPlusConfig
                    ]);
                });
            }

            $soxDropConfig = config('widgets.sox_drop');
            $soxDropRankingConfig = config('ranking.extra.item_logs.drop');
            if($soxDropConfig['enabled']) {
                View::composer(['partials.sox-drop'], function ($view) use ($soxDropConfig, $soxDropRankingConfig) {
                    $view->with([
                        'soxDrop' => LogEventItem::getLogEventItem('drop', null, $soxDropRankingConfig['degree'], $soxDropRankingConfig['type'], null, $soxDropConfig['limit']),
                        'soxDropConfig' => $soxDropConfig
                    ]);
                });
            }

            $pvpKillsConfig = config('widgets.pvp_kills');
            if($pvpKillsConfig['enabled']) {
                View::composer(['partials.pvp-kills'], function ($view) use ($pvpKillsConfig) {
                    $view->with([
                        'pvpKills' => LogEventChar::getKillLogs('pvp', $pvpKillsConfig['limit']),
                        'pvpKillsConfig' => $pvpKillsConfig
                    ]);
                });
            }

            $jobKillsConfig = config('widgets.job_kills');
            if($jobKillsConfig['enabled']) {
                View::composer(['partials.job-kills'], function ($view) use ($jobKillsConfig) {
                    $view->with([
                        'jobKills' => LogEventChar::getKillLogs('job', $jobKillsConfig['limit']),
                        'jobKillsConfig' => $jobKillsConfig
                    ]);
                });
            }

            $widgetsConfig = config('widgets.custom', []);
            foreach ($widgetsConfig as $key => $widget) {
                if (!($widget['enabled'] ?? false)) {
                    continue;
                }

                View::composer($widget['template'], function ($view) use ($widget, $key) {
                    $params = $view->getData();
                    preg_match_all('/:([a-zA-Z_]+)/', $widget['query'], $matches);
                    $queryParams = array_unique($matches[1]);

                    $sqlParams = [];
                    foreach ($queryParams as $param) {
                        $sqlParams[$param] = $params[$param] ?? null;
                    }

                    $cacheKey = 'widget_' . $key . '_' . md5(json_encode($sqlParams));
                    $data = Cache::remember($cacheKey, 600, function () use ($widget, $sqlParams) {
                        return collect(
                            DB::connection('shard')->select(
                                $widget['query'],
                                $sqlParams
                            )
                        );
                    });

                    $view->with([
                        'key'    => $key,
                        'config' => $widget,
                        'data'   => $data,
                        'params'   => $params,
                    ]);
                });
            }

        } catch (QueryException $e) {
            // Error: Something Wrong.
        }
    }
}
