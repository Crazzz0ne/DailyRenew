<?php

namespace App\Helpers\Appointments;

use App\Models\Auth\User;
use App\Models\Office\Office;
use App\Models\RoundRobin\RoundRobin;
use Carbon\Carbon;
use Carbon\CarbonPeriod;
use Exception;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;

class NewAppointmentHelper
{
    public static function getRRUserIds($lead): array
    {
        if ($lead->originOffice->feed_global) {
            $officeId = 1;
            $officeRoundRobin = RoundRobin::where('office_id', $officeId)
                ->where('type', 'Call Center Offices')
                ->first();
        } else {
            $officeId = $lead->originOffice->id;
            $officeRoundRobin = RoundRobin::where('office_id', $officeId)
                ->where('type', 'Call Center Appointments')
                ->first();
        }

        $userList = RoundRobin::where('office_id', $officeId)
            ->where('type', 'Call Center Appointments')
            ->first();

        if (isset($officeRoundRobin) && !empty($officeRoundRobin->list)) {
            $rrArray = $officeRoundRobin->list;

//            if (in_array(60, $rrArray)) {
//                if (!self::goodProduction(Auth::user()->id)) {
//                    $rrArray = array_diff($officeRoundRobin->list, [60]);
//                    $rrArray = array_values($rrArray);
//                }
//            }
            $officeUserIds = RoundRobin::whereIn('office_id', $rrArray)
                ->get();
            $i = 0;
            $userIds = [];
            foreach ($officeUserIds as $officeUserId) {
                $userIds[$i]['id'] = $officeUserId->office_id;
                $userIds[$i]['list'] = $officeUserId->list;
                $i++;
            }
            $userIds = collect($userIds);


            $userIds   = self::reorganizeBykey($userIds, $rrArray);


            return $userIds;
        }

        return $userList->list;
    }


    public static function reorganizeByUsers($objects, $keys)
    {
        $results = array();
        foreach ($keys as $key) {
            $i = 0;
            foreach ($objects as $object) {

                if ($object['id'] == $key) {

                    $results[$i] = $object;
                }
                $i++;
            }
        }
        return collect($results);
    }

    public static function reorganizeBykey($objects, $keys): array
    {
        $results = array();

        foreach ($keys as $key) {
            foreach ($objects as $object) {
                if ($object['id'] == $key) {
                    $results[] = $object['list'];
                }
            }
        }
        $results = array_merge(...$results);
        return $results;
    }

    public static function goodProduction($userId)
    {
        Cache::remember('goodProduction', 600, function () use ($userId) {
            $start = Carbon::now()->subDays(60);
            $end = Carbon::now()->subDays(7);
            $user = User::where('id', $userId)->with(['leads' => function ($q) use ($start, $end) {
                $q->whereBetween('leads.created_at', [$start->toDateTimeString(), $end->toDateTimeString()]);
            }])->first();
            $userLeads = $user->leads->count();
            if ($userLeads > 0) {
                $closed = $user->leads->filter(function ($lead) {
                    return $lead->close_date != null;
                });
                $closed = $closed->count();
                if ($closed > 0) {
                    $average = $closed / $userLeads;
                    if ($average > .08) {
                        return true;
                    }
                }
            }
            return false;
        });

    }

    public static function getUsers($userList, $city, $currentTime, $endTime, $remote, $customer, $source)
    {
        $users = User::query();
        if (($remote || $source !== 'D2D') && Auth::user()->hasRole('sp2') &&
            in_array($customer->language, Auth::user()->languages) &&
            (!Auth::user()->hasRole('executive') && !Auth::user()->hasRole('administrator'))) {
            $users->where('id', Auth::user()->id);
        } else {
            $users->whereIn('id', $userList);
            $users->where('id', '!=', Auth::user()->id);
            if ($remote) {
                $users->where('remote_option', $remote);
                $users->with(['availability' => function ($q) use ($currentTime, $endTime) {
                    $q->whereBetween('start', [$currentTime->toDateTimeString(), $endTime->toDateTimeString()]);
                    $q->where('type', 'virtual');
                },
                    'appointments' => function ($q) use ($currentTime, $endTime) {
                        $q->whereBetween('start_time', [$currentTime->toDateTimeString(), $endTime->toDateTimeString()]);
                        $q->where('type_id', 6);
                    }, 'appointments.lead.customer']);
            } else {
                $users->with(['availability' => function ($q) use ($currentTime, $endTime) {
                    $q->whereBetween('start', [$currentTime->toDateTimeString(), $endTime->toDateTimeString()]);
                    $q->where('type', 'in-person');
                },
                    'appointments' => function ($q) use ($currentTime, $endTime) {
                        $q->whereBetween('start_time', [$currentTime->toDateTimeString(), $endTime->toDateTimeString()]);
                        $q->where('type_id', 6);
                    }, 'appointments.lead.customer']);
                $users->withAllTagsOfAnyType(ucfirst($city));
            }
        }


        return $users->where('terminated', null)->get();

    }

    public static function setSlots($date, $endTime, $remote): array
    {

        if (!$remote) {
            $addHours = 2;
        } else {
            $addHours = 1;
        }
        $slots = [];
        $i = 0;
        $period = new CarbonPeriod($date, '30 minutes', $endTime);
        foreach ($period as $slot) {
            $newTime = clone $slot;
            $slots[$i]['start_time'] = $slot->utc();
            $slots[$i]['end_time'] = $newTime->addHours($addHours)->utc();
            $slots[$i]['userList'] = array();
            $slots[$i]['count'] = 0;
            $slots[$i]['distance'] = [];

            $i++;
        }
        return $slots;

    }

    public static function removeUsersByLanguage($customer, $users)
    {
        $language = $customer->language;
        $users = $users->filter(function ($user) use ($language) {
            if (in_array($language, $user->languages)) {
                return $user;
            }
        });
        return $users;
    }

    public static function checkAvailability($slots, $users)
    {
//        $users = [ 'id' => 1, 'name' => 'John Doe', appointments => [ 'start_time' => '2020-01-01 08:00:00', 'end_time' => '2020-01-01 10:00:00' ] ]
        foreach ($users as $user) {
            foreach ($user->availability as $available) {
                foreach ($slots as $key => $slot) {
                    if (self::slotIsWithin($slot, $available->start, $available->end)) {
                        if (!in_array($user->id, $slot['userList'])) {
                            $slots[$key]['userList'][] = $user->id;
                            $slots[$key]['count']++;
                        }
                    }
                }
            }
        }


        return $slots;
    }

    public static function checkAppointments($slots, $users)
    {
        foreach ($users as $user) {
            foreach ($user->appointments as $appointment) {
                $slots = self::removeUsersWithConflictingAppointments($user, $appointment, $slots);
            }
        }
        return $slots;
    }

    private static function removeUsersWithConflictingAppointments($user, $appointment, $slots): array
    {
        foreach ($slots as $key => $slot) {
            $slotStartTime = Carbon::parse($slot['start_time']);
            $slotEndTime = Carbon::parse($slot['end_time']);
            $appointmentStartTime = Carbon::parse($appointment->start_time);
            $appointmentEndTime = Carbon::parse($appointment->finish_time);


            if (in_array($user->id, $slot['userList']) &&
                (self::isTimeBetweenOrEqual($slotStartTime, $appointmentStartTime, $appointmentEndTime) ||
                    self::isTimeBetweenOrEqual($slotEndTime, $appointmentStartTime, $appointmentEndTime) ||
                    self::isAppointmentOverlappingSlot($appointmentStartTime, $appointmentEndTime, $slotStartTime, $slotEndTime))) {

                $slots[$key]['removed'][$user->id] = ['name' => $user->name, 'appointment_id' => $appointment->id, 'reason' => 'Conflicting'];
                $slots[$key]['userList'] = array_diff($slot['userList'], [$user->id]);
                $slots[$key]['count'] = count($slots[$key]['userList']);
            }
        }
        return $slots;
    }

    private static function isTimeBetweenOrEqual($time, $startTime, $endTime): bool
    {
        return $time >= $startTime && $time <= $endTime;
    }

    private static function isAppointmentOverlappingSlot($appointmentStartTime, $appointmentEndTime, $slotStartTime, $slotEndTime): bool
    {
        return $appointmentStartTime->lte($slotStartTime) && $appointmentEndTime->gte($slotEndTime);
    }



    public static function checkDistanceAndTime($slots, $users, $customer)
    {
        $distanceInterval = 9000;

        foreach ($users as $user) {
            // Remove users with appointments that are too far away
            $userRemoved = self::removeUsersWithDistantAppointments($user, $slots, $customer);

            if (!$userRemoved) {
                foreach ($user->appointments as $appointment) {
                    foreach ($slots as $key => $slot) {
                        if (!in_array($user->id, $slot['userList'])) {
                            continue;
                        }

                        if (!self::slotWithin($slot, $appointment->start_time, $appointment->end_time)) {
                            continue;
                        }
//return $slots;
                        // Check previous and future slots for distance constraints

                        $slots = self::updateSlotsForPreviousAndFutureAppointments($slots, $user, $key, $distanceInterval, $customer, $appointment);
                    }
                }
            }
        }

        return $slots;
    }

    private static function removeUsersWithDistantAppointments($user, &$slots, $customer)
    {
        $removed = false;

        foreach ($user->appointments as $appointment) {
            $distance = self::distance($customer->lat, $customer->lng, $appointment->lead->customer->lat, $appointment->lead->customer->lng);
            if ($distance > 100000) {
                $removed = true;
                foreach ($slots as $key => $slot) {
                    if (in_array($user->id, $slot['userList'])) {
                        $slots[$key]['removed'][$user->id] = ['name' => $user->name, 'appointment_id' => $appointment->id, 'reason' => 'Max Distance Reached', 'distance' => self::metersToMiles($distance)];
                        $slots[$key]['userList'] = array_diff($slot['userList'], [$user->id]);
                        $slots[$key]['count'] = count($slots[$key]['userList']);
                    }
                }
            }
        }
        return $removed;
    }

    private static function updateSlotsForPreviousAndFutureAppointments($slots, $user, $currentKey, $distanceInterval, $customer, $appointment): array
    {
        $keysToCheck = [
            'previous' => range(-40, -1, 1),
            'future' => range(40, 1, 1)
        ];

        foreach ($keysToCheck as $direction => $keyOffsets) {
            foreach ($keyOffsets as $key => $keyOffset) {
                $keyToCheck = $currentKey + $keyOffset;

                if ($keyToCheck < 0 || !isset($slots[$keyToCheck])) {
                    continue;
                }

                $distance = self::distance($customer->lat, $customer->lng, $appointment->lead->customer->lat, $appointment->lead->customer->lng);

                $slotStart = Carbon::parse($slots[$keyToCheck]["start_time"]);
                $slotEndTime = Carbon::parse($slots[$keyToCheck]["end_time"]);
                $appointmentStartTime = Carbon::parse($appointment->start_time);
                $appointmentEndTime = Carbon::parse($appointment->finish_time);

                if ($slotEndTime->lessThanOrEqualTo($appointmentStartTime)) {

                    $minutesBetween = $appointmentStartTime->diffInMinutes($slotEndTime);
                } elseif ($slotStart->greaterThanOrEqualTo($appointmentEndTime)) {

                    $minutesBetween = $slotStart->diffInMinutes($appointmentEndTime);
                } else {
                    $minutesBetween = 0;
                }


                if (self::isTravelDistanceValid($distance, $minutesBetween)) {
                    $slots[$keyToCheck]['distance'][(int)$appointment->id] = [
                        'user_id' => $user->id,
                        'distance' => self::metersToMiles($distance),
                        'appointment_id' => $appointment->id,
                        'start_time' => Carbon::parse($slots[$keyToCheck]["start_time"])->setTimezone('America/Los_Angeles')->toDateTimeString(),
                        'end_time' => Carbon::parse($slots[$keyToCheck]["end_time"])->setTimezone('America/Los_Angeles')->toDateTimeString(),
                        'appointment_time' => Carbon::parse($appointment->start_time)->setTimezone('America/Los_Angeles')->toDateTimeString(),
                        'hoursBetween' => $minutesBetween / 60,
                        'distance_in_M' => $distance,
                    ];
                } else {
                    $reason = '';
                    if ($minutesBetween > 0) {
                        $reason = 'Time and Distance';
                    } else {
                        $reason = 'Conflicting';
                    }
                    $slots[$keyToCheck]['removed'][] = ['name' => $user->name, 'appointment_id' => $appointment->id,
                        'reason' => $reason, 'distance' => self::metersToMiles($distance), 'hours' => $minutesBetween, 'distance_in_M' => $distance,];
                    $slots[$keyToCheck]['userList'] = array_diff($slots[$keyToCheck]['userList'], [$user->id]);
                    $slots[$keyToCheck]['count'] = count($slots[$keyToCheck]['userList']);
                }
            }
        }
        return $slots;
    }

    public static function castDistanceToArray($slots)
    {
        foreach ($slots as $key => $slot) {
            if (isset($slot['distance'])) {
                $slots[$key]['distance'] = array_values($slot['distance']);
            }
        }

        return $slots;
    }

    public static function isTravelDistanceValid($distanceInMeters, $timeInMinutes)
    {
        // Define the maximum allowed distance for a 30-minute interval
        $maxDistanceInMeters = 15000;
        $allowedTimeInMinutes = 30;

        // Define the power function exponent (e.g., 1.2 for 20% increase in travel rate for each unit increase in distance)
        $powerExponent = 1.8;

        // Calculate the proportion of the given time interval compared to the allowed time
        $timeProportion = pow($timeInMinutes / $allowedTimeInMinutes, $powerExponent);

        // Calculate the maximum allowed distance for the given time interval
        $maxAllowedDistanceForGivenTime = $maxDistanceInMeters * $timeProportion;
//        echo 'time '.$timeInMinutes.' distance '.$distanceInMeters.' max '.$maxAllowedDistanceForGivenTime."\n";
        if ($maxAllowedDistanceForGivenTime > 100000) {
            $maxAllowedDistanceForGivenTime = 100000;
        }
        // Return true if the given distance is within the allowed range, false otherwise
        return $distanceInMeters <= $maxAllowedDistanceForGivenTime;
    }

    private static function metersToMiles($distanceMeters)
    {
        return $distanceMeters / 1609.344;
    }

    private static function slotWithin($slot, $start, $end): bool
    {
        $startTime = Carbon::parse($slot['start_time']);
        $endTime = Carbon::parse($slot['end_time']);

        return ($startTime->equalTo($start) || $endTime->equalTo($end) ||
            $startTime->between($start, $end) || $endTime->between($start, $end));
    }


    private static function slotIsWithin($slot, $start, $end): bool
    {
        if (Carbon::parse($slot['start_time'])->between(Carbon::parse($start), Carbon::parse($end)) &&
            Carbon::parse($slot['end_time'])->between(Carbon::parse($start), Carbon::parse($end))) {
            return true;
        } else {
            return false;
        }
    }

    private
    static function appointmentWithin($appointment, $start, $end): bool
    {
        if (Carbon::parse($appointment->start_time)->between(Carbon::parse($start), Carbon::parse($end)) ||
            Carbon::parse($appointment->end_time)->between(Carbon::parse($start), Carbon::parse($end))) {
            return true;
        }
        return false;
    }

    private
    static function distance($lat1, $lng1, $lat2, $lng2)
    {
        return Cache::remember("distance.$lat1.$lat2.$lng1.$lng2", 60, function () use ($lat1, $lng1, $lat2, $lng2) {
            $radius = 6378137;
            $lat1 = deg2rad($lat1);
            $lng1 = deg2rad($lng1);
            $lat2 = deg2rad($lat2);
            $lng2 = deg2rad($lng2);
            $dlat = $lat2 - $lat1;
            $dlng = $lng2 - $lng1;
            $a = sin($dlat / 2) * sin($dlat / 2) + cos($lat1) * cos($lat2) * sin($dlng / 2) * sin($dlng / 2);
            $c = 2 * atan2(sqrt($a), sqrt(1 - $a));
            $d = $radius * $c;
            return $d;
        });

    }



    public static function removeAppointment($slots): array
    {
        $slotsFiltered = [];
        foreach ($slots as $key => $slot) {
            try {
                if ($slot['count'] > 0) {
                    $slotsFiltered[] = $slot;
                }
            } catch (\Exception $e) {
                dd($slot);
            }

        }
        return $slotsFiltered;
    }

    public static function removeUserFromDistance($slotCollection)
    {
//       find the first user in the userlist, then remove all other users from the distance array
        foreach ($slotCollection as $key => $slot) {
            if (count($slot['userList']) > 0) {
                if (!isset($slot['distance'])) {
                    continue;
                }
//                dump($slot['userList']);
//                echo count($slot['userList']) . " users in slot $key\n";

                $firstUser = reset($slot['userList']);

                foreach ($slot['distance'] as $key2 => $distance) {
                    if ($distance['user_id'] != $firstUser) {
                        unset($slotCollection[$key]['distance'][$key2]);
                    }
                }
            }
        }
        return $slotCollection;
    }
}
