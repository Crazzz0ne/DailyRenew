<?php

namespace App\Helpers\Appointments;

use App\Models\SalesFlow\Customer\Customer;
use Carbon\Carbon;
use GoogleOAuth2;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;

class AppointmentHelper
{
    public static function processSlotCollection($slotCollection, $users, $lead, $remote, $rebookId = false)
    {

        foreach ($slotCollection as $slotIndex => $slot) {
            $slotStart = Carbon::parse($slot['start_time']);
            if ($remote) {
                $slotEnd = Carbon::parse($slot['start_time'])->addHours(1);
            } else {
                $slotEnd = Carbon::parse($slot['start_time'])->addHours(2);
            }

            foreach ($users as $user) {
                $availability = $user->availability;

                if (!is_array($user->languages)) {
                    \Log::critical('user who errors' . $user->id);
                    continue;
                }

                if (!in_array($lead->customer->language, $user->languages)) {
                    continue;

                }
                if (count($availability)) {

                    $currentAppointments = self::getOutsideAppointments($user, $slotStart, $rebookId);

                    foreach ($availability as $available) {

                        if (self::slotIsInAvailableRange($slotStart, $slotEnd, $available)) {
                            array_push($slotCollection[$slotIndex]['userList'], $user->id);
                            if (!key_exists('count', $slotCollection[$slotIndex])) {
                                $slotCollection[$slotIndex]['count'] = 1;
                            } else {
                                $slotCollection[$slotIndex]['count']++;
                            }
                            if (count($currentAppointments)) {
                                foreach ($currentAppointments as $appointment) {
                                    $currentAppointmentStart = Carbon::parse($appointment['start_time']);
                                    $currentAppointmentEnd = Carbon::parse($appointment['finish_time']);
                                    if (!$remote) {
                                        $distanceInMeters = LocationHelper::computeDistance(
                                            $appointment['lead']['customer']['lat'],
                                            $appointment['lead']['customer']['lng'],
                                            $lead->customer->lat,
                                            $lead->customer->lng
                                        );
                                        if ($distanceInMeters > 160934 && !$remote) {
                                            if (($key = array_search($user->id, $slotCollection[$slotIndex]['userList'])) !== false) {
                                                unset($slotCollection[$slotIndex]['userList'][$key]);
                                            }
                                            $slotCollection[$slotIndex]['count']--;
                                            continue;
                                        }
                                    }
                                    if ($currentAppointmentStart->between($slotStart, $slotEnd) ||
                                        $currentAppointmentEnd->between($slotStart, $slotEnd)) {

                                        if (($key = array_search($user->id, $slotCollection[$slotIndex]['userList'])) !== false) {
                                            unset($slotCollection[$slotIndex]['userList'][$key]);
                                        }
                                        $slotCollection[$slotIndex]['count']--;

                                    } else {
                                        if ($remote) {
                                            $canAssign = true;
                                        } else {
                                            $canAssign = self::checkForOtherAppointment($user->appointments, $lead['customer'], $slotIndex, $slotCollection, $user->id);
                                        }
                                        if ($canAssign) {
                                            if (!key_exists('userId', $slotCollection[$slotIndex])) {

                                                if ($slotCollection[$slotIndex]['count'] > 0) {
                                                    $slotCollection[$slotIndex]['userId'] = $user->id;
                                                }
                                            } else {
                                                if ($slotCollection[$slotIndex]['userId'] != $user->id) {
                                                    $slotCollection[$slotIndex]['backUpUserId'] = $user->id;
                                                }
                                            }
                                        }
                                    }
                                }
                            } else {

                                if (!key_exists('userId', $slotCollection[$slotIndex])) {

                                    if ($slotCollection[$slotIndex]['count'] > 0) {
                                        $slotCollection[$slotIndex]['userId'] = $user->id;
                                    }
                                } else {
                                    $slotCollection[$slotIndex]['backUpUserId'] = $user->id;
                                }
                            }
                        }
                    }
                }
            }

//            if ($slotIndex > 150) {
//                die('fin');
//            }
        }
        return $slotCollection;
    }



    private static function slotIsInAppointmentRange($slotStart, $slotEnd, $appointmentStart, $appointmentEnd): bool
    {


        return
            $slotStart->between($appointmentStart, $appointmentEnd)
            ||
            $slotEnd->between($appointmentStart, $appointmentEnd);
    }

    public static function checkForOtherAppointment($appointments, $customer, $slotID, &$array, $userId): bool
    {
        $slotStart = Carbon::parse($array[$slotID]['start_time']);
        foreach ($appointments as $key => $currentAppointment) {
            $distanceInMeters = LocationHelper::computeDistance(
                $currentAppointment['lead']['customer']['lat'],
                $currentAppointment['lead']['customer']['lng'],
                $customer['lat'],
                $customer['lng']
            );
            $appointmentStart = Carbon::parse($currentAppointment->start_time);
            $appointmentEnd = Carbon::parse($currentAppointment->finish_time);


            $i = 4;
//            Moving forward in time
            $mileMultiplier = 38000;
            while ($i > 0) {
                $minuteBlock = ($i * 30) + 120;
                $slotEnd = Carbon::parse($array[$slotID]['start_time'])->addMinutes($minuteBlock);
                $slotStartClone = $slotStart->clone()->subMinutes(30);
                if (self::slotIsInAppointmentRange($slotStartClone, $slotEnd, $appointmentStart, $appointmentEnd)) {
                    $distanceInMetersTimeBased = $i * $mileMultiplier;
                    if ($distanceInMeters > $distanceInMetersTimeBased) {
                        if (($key = array_search($userId, $array[$slotID]['userList'])) !== false) {
                            unset($array[$slotID]['userList'][$key]);
                        }
                        return false;
                    }
                }
                $i--;
            }

            $i = 0;
            while ($i < 0) {
                $minuteBlock = ($i * 30) + 120;
                $slotEnd = Carbon::parse($array[$slotID]['start_time'])->subMinutes($minuteBlock);
                $slotStartClone = $slotStart->clone()->subMinutes(1);
                if (self::slotIsInAppointmentRange($slotStartClone, $slotEnd, $appointmentStart, $appointmentEnd)) {
                    $distanceInMetersTimeBased = $i * $mileMultiplier;
                    if ($distanceInMeters > $distanceInMetersTimeBased) {
                        if (($key = array_search($userId, $array[$slotID]['userList'])) !== false) {
                            unset($array[$slotID]['userList'][$key]);
                        }
                        return false;
                    }
                }
                $i++;
            }
        }
        return true;
    }

    private static function checkForOverLap($slotIndex, $customer, &$slotCollection, $user, $available, $currentAppointments, $appointment, $distanceInMeters, $direction)
    {

        //Check Previous Slots, up to 6 slots out.
        $loopCount = 1;

        if ($direction) {
            $newIndex = $slotIndex + 7;


            while ($slotIndex < $newIndex) {

                $maxDistance = $loopCount * 1500;
                $maxTime = $loopCount * 30;


                self::checkOtherSlot($newIndex, $slotCollection, $customer, $user, $appointment, $direction, $distanceInMeters, $maxDistance, $maxTime);


                $newIndex--;
                $loopCount++;
                if ($loopCount > 12) {
                    die('you fool');
                }
            }
        } else {
            $newIndex = $slotIndex - 7;
            while ($slotIndex > $newIndex) {

                $maxTime = $loopCount * 30;
                $maxDistance = $loopCount * 1500;
                if ($distanceInMeters > $maxDistance) {
                    $loopCount++;
                    if ($loopCount > 12) {
                        die('you fool');
                    }
                    break;
                }

                self::checkOtherSlot($newIndex, $slotCollection, $customer, $user, $appointment, $direction, $distanceInMeters, $maxDistance, $maxTime);
                $newIndex++;
                $loopCount++;
            }
        }


    }

    public static function checkOtherSlot($slotID, &$array, $customer, $user, $appointment, $direction, $distance, $maxDistance, $maxTime = 30)
    {
        if (!isset($array[$slotID])) {
            return 0;
        }
        $slotStart = Carbon::parse($array[$slotID]['start_time']);
        $slotEnd = Carbon::parse($array[$slotID]['start_time'])->addHours(2);
        $isAvailable = false;
        foreach ($user->availability as $available) {
            if (!self::slotIsInAvailableRange($slotStart, $slotEnd, $available)) {
                return 0;
            }
        }


        $currentAppointmentStart = Carbon::parse($appointment['start_time']);
        $timeFromCurrent = Carbon::parse($appointment['start_time']);
        if ($direction) {
            $timeFromCurrent->subMinutes(60);
        } else {
            $timeFromCurrent->addMinutes(60);
        }

        $pastEnd = Carbon::parse($array[$slotID]['start_time'])->addHours(2);

        if ($pastEnd->between($timeFromCurrent, $currentAppointmentStart)) {


            if (key_exists('userId', $array[$slotID])) {

                if ($array[$slotID]['userId'] == $user->id) {
                    $array[$slotID]['preferred'] = 1;
                }
            } else {

                $array[$slotID]['userId'] = $user->id;
                $array[$slotID]['preferred'] = 1;
            }

        } else {

            foreach ($user->appointments as $key => $currentAppointment) {

                if ($direction) {
                    $maxFromEnd = Carbon::parse($currentAppointment['finish_time'])->addMinutes($maxTime);
                } else {
                    $maxFromStart = Carbon::parse($currentAppointment['start_time'])->subMinutes($maxTime);
                }

                $currentAppointmentStart = Carbon::parse($currentAppointment['start_time']);
                $currentAppointmentEnd = Carbon::parse($currentAppointment['finish_time']);

                $slotStart = Carbon::parse($array[$slotID]['start_time']);
                if ($direction) {
                    $operator = $slotStart->between($currentAppointmentStart, $maxFromEnd) && $slotEnd->between($currentAppointmentStart, $maxFromEnd)
                        && !$currentAppointmentStart->between($slotStart, $slotEnd) && !$currentAppointmentEnd->between($slotStart, $slotEnd);

                } else {
                    $operator = $slotStart->between($maxFromStart, $currentAppointmentEnd) && $slotEnd->between($maxFromStart, $currentAppointmentEnd)
                        && !$currentAppointmentStart->between($slotStart, $slotEnd) && !$currentAppointmentEnd->between($slotStart, $slotEnd);
                }
//                if ($slotID == 36 && $maxTime > 30 && $direction && $key > 0) {
//
//                    dump($currentAppointment['finish_time']);
//                    dump($maxTime);
//                    dump($maxFromEnd->toDateTimeString());
//
//                    echo '<pre>';
//                    echo $currentAppointmentStart->toDateTimeString() . ' < ' . $slotStart->toDateTimeString()  . ' > ' . $maxFromEnd->toDateTimeString() .'  <br>';
//                    echo  $currentAppointmentStart->toDateTimeString() . ' < ' . $slotEnd->toDateTimeString() . ' > ' . $maxFromEnd->toDateTimeString(). '  <br>' ;
//                    echo  $slotStart->toDateTimeString() . ' <= ' . $currentAppointmentStart->toDateTimeString() . ' >= ' . $slotEnd->toDateTimeString(). '  <br>' ;
//                    echo  $slotStart->toDateTimeString() . ' <= ' . $currentAppointmentEnd->toDateTimeString() . ' >= ' . $slotEnd->toDateTimeString(). '  <br>' ;
//                    echo '</pre>';
//                    die();
//
//                }

                if ($direction && $operator && ($user->id == 111)) {

                }

                if ($operator) {

                    $distance = LocationHelper::computeDistance(
                        $appointment['lead']['customer']['lat'],
                        $appointment['lead']['customer']['lng'],
                        $customer['lat'],
                        $customer['lng']
                    );


                    if ($distance < $maxDistance) {


                        if (key_exists('userId', $array[$slotID])) {


                            if ($array[$slotID]['userId'] == $user->id) {

                                $array[$slotID]['preferred'] = 1;
                                $array[$slotID]['ding'] = 1;
                            }
                        } else {

                            $array[$slotID]['userId'] = $user->id;
                            $array[$slotID]['preferred'] = 1;
                            $array[$slotID]['ding'] = 1;
                        }
                    }

                }

            }
        }
    }

    public static function checkNextSlot($slotID, &$array, $user, $currentAppointments, $appointment, $available, $distance, $maxDistance, $maxTime)
    {
        $futureAppointmentStart = Carbon::parse($array[$slotID]['start_time']);
        $futureAppointmentEnd = Carbon::parse($array[$slotID]['end_time']);


        $timeFromCurrent = Carbon::parse($appointment['finish_time'])->copy()->addMinutes(60);

        $currentAppointments = self::getOutsideAppointments($user);


        if ($futureAppointmentStart->between(Carbon::parse($appointment['finish_time']), $timeFromCurrent)) {

            if ($distance < $maxDistance) {
                if (isset($array[$slotID])) {
                    if ($array[$slotID] == $user->id) {
                        $array[$slotID]['userId'] = $user->id;
                    }
                } else {
                    if (!key_exists('backupUser', $array[$slotID])) {
                        $array[$slotID]['backupUser'] = $user->id;
                    }
                }
                $array[$slotID]['preferred'] = 1;
            }
//            if (!self::slotHasUserId($array[$slotID])) {
//                if ($futureAppointmentStart->between(Carbon::parse($available->start), Carbon::parse($available->end)) &&
//                    $futureAppointmentEnd->between(Carbon::parse($available->start), Carbon::parse($available->end))
//                ) {
//                    foreach ($currentAppointments as $appointmentDouble) {
//                        $currentAppointmentStartDouble = Carbon::parse($appointmentDouble['start_time']);
//                        $currentAppointmentEndDouble = Carbon::parse($appointmentDouble['finish_time']);
//
//                        if (!$currentAppointmentEndDouble->between($futureAppointmentStart, $futureAppointmentEnd) &&
//                            !$currentAppointmentStartDouble->between($futureAppointmentStart, $futureAppointmentEnd)) {
//
//                        }
//                    }
//                }
//            }
        }

    }

    public static function getOutsideAppointments($user, $date, $reBookId): array
    {
        $userEmail = $user->email;

//        $payload = Cache::remember('outside.google.appointments.' . $userEmail, 600, function () use ($userEmail) {
//            return self::checkGoogleCalendar($userEmail);
//        });

        $currentAppointments = $user->appointments->toArray();
//        $currentAppointments = array_merge($payload, $currentAppointments);
        $currentAppointments = collect($currentAppointments);
if ($user->appointments->count() > 0) {
    $currentAppointments = $currentAppointments->sortBy('start_time');
    return $currentAppointments->values()->all();
        }
return [];
        $date = Carbon::parse($date);
        $dayStart = $date->copy()->startOfDay();
        $dayEnd = $date->endOfDay();
        if ($reBookId) {
            $currentAppointments = $currentAppointments->filter(function ($value, $key) use ($reBookId, $dayStart, $dayEnd) {
                if ($value['id'] !== $reBookId &&
                    ($dayStart->lessThan(Carbon::parse($value['start_time']))
                        && $dayEnd->greaterThan(Carbon::parse($value['start_time'])))) {
                    return true;
                }
            });

        } else {
            $currentAppointments = $currentAppointments->filter(function ($value, $key) use ($reBookId, $dayStart, $dayEnd) {
                if ($dayStart->lessThan(Carbon::parse($value['start_time'])) && $dayEnd->greaterThan(Carbon::parse($value['start_time']))) {
                    dd('space and time');
                    return true;
                }
            });
        }



    }

    private static function checkGoogleCalendar($userEmail): array
    {
        $googleAuth = new GoogleOAuth2();
        $client = $googleAuth->getClient($userEmail);

        $events = $googleAuth->getFutureCalendar($client, $userEmail);
        $outsideAppointments = array();
        $i = 0;
        foreach ($events->getItems() as $event) {
            if ($event->start->dateTime) {
                $outsideAppointments[$i]['start_time'] = Carbon::parse($event->start->dateTime)->toDateTimeString();
                $outsideAppointments[$i]['finish_time'] = Carbon::parse($event->start->dateTime)->toDateTimeString();
                $outsideAppointments[$i]['type_id'] = 6;
                $i++;
            }
        }
        return $outsideAppointments;
    }

    private static function slotIsInAvailableRange($slotStart, $slotEnd, $available): bool
    {

        return
            $slotStart->between(Carbon::parse($available->start)->subMinutes(15), Carbon::parse($available->end)->addMinutes(15))
            &&
            $slotEnd->between(Carbon::parse($available->start)->subMinutes(15), Carbon::parse($available->end)->addMinutes(15));
    }

    private static function slotHasUserId($slot): bool
    {
        return key_exists('userId', $slot);
    }

    private static function slotUserMatchesCurrentUser($slot, $user): bool
    {
        return $slot['userId'] === $user->id;
    }
}
