<?php

namespace App\Http\Controllers\Api\SalesFlow\Reporting;

use App\Models\SalesFlow\Lead\Lead;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use function PHPUnit\Framework\isEmpty;

class PowerRankingController
{

    private const POINTS_CONFIG = [
        'appointment' => 5,
        'sat' => [ 1 =>10, 2 => 15],
        'creditPass' => 5,
        'leadUploads' => [1 => 5, 2 => 3],
        'noShow' => [1 => -15, 2 => -60],
        'close' => 30,
    ];
    private const CREDIT_PASS_STATUS = [2, 5, 6];

    public function index(Request $request)
    {
        $year = $request->query('year');
        $month = $request->query('month');
        $positionId = $request->query('positionId');
        $key = "powerRank.year.$year.month.$month.positionId.$positionId.officeid.{$request->office_id}.regionId.{$request->regionId}";

        return Cache::remember($key, 60, function () use ($year, $month, $positionId, $request) {
            $startDate = Carbon::createFromDate($year, $month, 1, 'America/Los_Angeles')->startOfDay();
            $endDate = Carbon::createFromDate($year, $month, 1, 'America/Los_Angeles')->endOfMonth()->endOfDay();
            $between = [$startDate->timezone('UTC'), $endDate->timezone('UTC')];

            $leadsQuery = Lead::query()->whereBetween('created_at', $between);

            if ($officeId = $request->office_id) {
                $leadsQuery->hasOffice($officeId);
            } elseif ($regionId = $request->regionId) {
                $leadsQuery->hasRegion($regionId);
            } else {
                return 'ests';
            }

            $leads = $leadsQuery->with([
                'reps' => fn($q) => $q->where('position_id', $positionId)
                    ->whereNull('users.terminated')
                    ->whereNull('user_has_leads.deleted_at'),
                'leadUploads',
                'salesPacket',
                'customer',
                'statusName',
                'appointments' => fn($q) => $q->where('type_id', 6)
                    ->whereNull('deleted_at'),
            ])->get();

            $userList = [];

            foreach ($leads as $lead) {
                $this->processLead($lead, $userList, $positionId);
            }

            $this->processClosedLeads($request, $between, $userList, $positionId);

//            filter all of the zero point users out
           $userList =  array_filter($userList, fn($user) => $user['points'] > 1);
//            map the user list to an array summing the total points
        return    $userList = array_map(fn($user) => array_merge($user, ['points' => array_sum($user['totalPoints'])]), $userList);

        });
    }

    private function processLead($lead, &$userList, $positionId)
    {
        if (!count($lead->reps) || $lead->reps[0]->id === 3) return;

        $rep = $lead->reps[0];
        $repId = $rep->id;
        $points = 0;

        $userList[$repId] = $userList[$repId] ?? [
            'id' => $repId,
            'name' => "$rep->first_name $rep->last_name",
            'appointments' => 0,
            'sat' => 0,
            'ubill' => 0,
            'points' => 0,
            'creditPass' => 0,
            'close' => 0,
            'noShow' => 0,
            'totalPoints' => [
                'sat' => 0,
                'creditPass' => 0,
                'ubill' => 0,
                'noShow' => 0,
                'close' => 0,
                'appointment' => 0,
            ],
        ];

        if (count($lead->appointments)) {
            $points += self::POINTS_CONFIG['appointment'];
            $userList[$repId]['appointments']++;
            $userList[$repId]['appointmentLeads'][] = $lead->id;
            $userList[$repId]['totalPoints']['appointment'] += self::POINTS_CONFIG['appointment'];
            if ($lead->salesPacket->sat) {
                $points += self::POINTS_CONFIG['sat'][$positionId] ?? 0; // Use positionId as the index
                $userList[$repId]['sat']++;
                $userList[$repId]['satLeads'][] = $lead->id;
                $userList[$repId]['totalPoints']['sat'] += self::POINTS_CONFIG['sat'][$positionId] ?? 0;
            }
        }

        if (in_array($lead->credit_status_id, self::CREDIT_PASS_STATUS)) {
            $userList[$repId]['creditPass']++;
            $points += self::POINTS_CONFIG['creditPass'];
            $userList[$repId]['creditPassLeads'][] = $lead->id;
            $userList[$repId]['totalPoints']['creditPass'] += self::POINTS_CONFIG['creditPass'];
        }

        if (count($lead->leadUploads)) {
            $arrayList = $lead->leadUploads->where('type', 'bill')->toArray();
            $userIds = array_column($arrayList, 'user_id');
            if (in_array($repId, $userIds) || in_array(1, $userIds)) {
                $points += self::POINTS_CONFIG['leadUploads'][$positionId] ?? 0;
                $userList[$repId]['uploadLeads'][] = $lead->id;
                $userList[$repId]['ubill']++;
                $userList[$repId]['totalPoints']['ubill'] += self::POINTS_CONFIG['leadUploads'][$positionId] ?? 0;
            }
        }

        if ($lead->status_id === 20) {
            $points += self::POINTS_CONFIG['noShow'][$positionId] ?? 0; // Use positionId as the index
            $userList[$repId]['noShow']++;
            $userList[$repId]['noShowLeads'][] = $lead->id;
            $userList[$repId]['totalPoints']['noShow'] += self::POINTS_CONFIG['noShow'][$positionId] ?? 0;
        }

        $userList[$repId]['points'] += max($points, 0);
    }

    private function processClosedLeads($request, $between, &$userList, $positionId)
    {
        $closedQuery = Lead::query()->whereBetween('close_date', $between)
            ->with(['reps' => fn($q) => $q->where('position_id', $positionId)
                ->whereNull('users.terminated')
                ->whereNull('user_has_leads.deleted_at')]);

        if ($officeId = $request->office_id) {
            $closedQuery->hasOffice($officeId);
        } elseif ($regionId = $request->regionId) {
            $closedQuery->hasRegion($regionId);
        }

        $closedLeads = $closedQuery->get();

        foreach ($closedLeads as $close) {
            if (!count($close->reps) || $close->reps[0]->id === 3) continue;

            $repId = $close->reps[0]->id;

            if (!isset($userList[$repId])) {
                $userList[$repId] = [
                    'id' => $repId,
                    'name' => $close->reps[0]->first_name . ' ' . $close->reps[0]->last_name,
                    'appointments' => 0,
                    'sat' => 0,
                    'ubill' => 0,
                    'points' => 0,
                    'noShow' => 0,
                    'creditPass' => 0,
                    'close' => 0,
                ];
            }

            $userList[$repId]['points'] += 30;
            $userList[$repId]['closeLeads'][] = $close->id;
            if (isset($userList[$repId]['totalPoints']) && isset($userList[$repId]['totalPoints']['close'])) {
                $userList[$repId]['totalPoints']['close'] += 30;
            } else {
                $userList[$repId]['totalPoints']['close'] = 30;
            }
            $userList[$repId]['totalPoints']['close'] += 30;

            $userList[$repId]['close']++;
        }
    }

}
