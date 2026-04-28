<?php

namespace App\Services;

use App\Models\SRO\Shard\Schedule;
use Carbon\Carbon;
use Illuminate\Support\Collection;

class ScheduleService
{
    public static function getEventSchedules(): Collection
    {
        $now = Carbon::now();
        $names = config('widgets.event_schedule.names', []);
        $customEvents = config('widgets.event_schedule.custom', []);

        $data = Schedule::getSchedules(array_keys($names))->groupBy('ScheduleDefineIdx');

        $result = collect();

        foreach ($data as $ScheduleDefineIdx => $schedules) {
            $soonestEvent = null;

            foreach ($schedules as $schedule) {
                $nextStart = null;

                // Daily
                if ((int) $schedule->MainInterval_Type === 1) {
                    $nextStart = $now->copy()->setTime(
                        (int) $schedule->SubInterval_StartTimeHour,
                        (int) $schedule->SubInterval_StartTimeMinute,
                        (int) $schedule->SubInterval_StartTimeSecond
                    );

                    $nextEnd = $nextStart->copy()->addSeconds((int) $schedule->SubInterval_DurationSecond);

                    if ($now->gte($nextEnd)) {
                        $nextStart->addDay();
                        $nextEnd = $nextStart->copy()->addSeconds((int) $schedule->SubInterval_DurationSecond);
                    }
                }

                // Weekly
                elseif ((int) $schedule->MainInterval_Type === 3) {
                    $targetDay = (int) $schedule->SubInterval_DayOfWeek - 1;

                    $nextStart = $now->copy()
                        ->startOfDay()
                        ->setTime(
                            (int) $schedule->SubInterval_StartTimeHour,
                            (int) $schedule->SubInterval_StartTimeMinute,
                            (int) $schedule->SubInterval_StartTimeSecond
                        );

                    if ($now->dayOfWeek !== $targetDay) {
                        $nextStart->next($targetDay);
                    } elseif ($now->gte(
                        $nextStart->copy()->addSeconds((int) $schedule->SubInterval_DurationSecond)
                    )) {
                        $nextStart->addWeek();
                    }
                }

                if (!$nextStart) {
                    continue;
                }

                $nextEnd = $nextStart->copy()->addSeconds((int) $schedule->SubInterval_DurationSecond);
                $status = $now->between($nextStart, $nextEnd);

                $shouldReplace = !$soonestEvent || $status || (!$soonestEvent->status && $nextStart->lt($soonestEvent->start));

                if ($shouldReplace) {
                    $soonestEvent = (object) [
                        'start' => $nextStart,
                        'end' => $nextEnd,
                        'status' => $status,
                    ];

                    $result->put($ScheduleDefineIdx, (object) [
                        'idx' => $ScheduleDefineIdx,
                        'name' => $names[$ScheduleDefineIdx] ?? $schedule->Description,
                        'timestamp' => $nextStart->timestamp,
                        'duration' => (int) $schedule->SubInterval_DurationSecond,
                        'status' => $status,
                        'start' => $nextStart,
                        'end' => $nextEnd,
                    ]);
                }
            }
        }

        foreach ($customEvents as $idx => $event) {
            if (empty($event['enabled']) || empty($event['days']) || !isset($event['hour'], $event['min'])) {
                continue;
            }

            $duration = (int) ($event['duration'] ?? 0);

            // hour and min may be a single value (legacy) or an array of times (e.g. [8, 12, 20])
            $hours = is_array($event['hour']) ? $event['hour'] : [$event['hour']];
            $mins  = is_array($event['min'])  ? $event['min']  : [$event['min']];

            $candidates = collect();

            foreach ($event['days'] as $day) {
                foreach ($hours as $ti => $hour) {
                    $min  = $mins[$ti] ?? $mins[0] ?? 0;
                    $date = $now->copy()
                        ->startOfDay()
                        ->next(strtolower($day))
                        ->setTime((int) $hour, (int) $min, 0);

                    // If this slot is already in the past (or currently running), push it a week ahead
                    // so we always get the *next* upcoming occurrence
                    if ($date->lte($now)) {
                        $date->addWeek();
                    }

                    $candidates->push($date);
                }
            }

            $nextStart = $candidates->sort()->first();

            if (!$nextStart) {
                continue;
            }

            $nextEnd = $duration > 0 ? $nextStart->copy()->addSeconds($duration) : null;

            $status = $nextEnd ? $now->between($nextStart, $nextEnd) : false;

            $result->put($idx, (object) [
                'idx'       => $idx,
                'name'      => $event['name'],
                'timestamp' => $nextStart->timestamp,
                'duration'  => $duration,
                'status'    => $status,
                'start'     => $nextStart,
                'end'       => $nextEnd,
            ]);
        }

        return $result->sortBy('idx')->values();
    }
}
