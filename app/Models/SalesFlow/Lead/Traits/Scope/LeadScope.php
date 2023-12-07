<?php

namespace App\Models\SalesFlow\Lead\Traits\Scope;

/**
 * Class LeadScope.
 */
trait LeadScope
{
    /**
     * @param $query
     * @param $officeId
     * @return mixed
     */
    public function scopeHasOffice($query, $officeId)
    {
        return $query->where('office_id', $officeId)
            ->orWhere('origin_office_id', $officeId);
    }
    public function scopeHasRegion($query, $region)
    {
        return $query->whereHas('office', function ($query) use ($region){
            $query->where('market_id', $region);
        });


//->whereHas('office', function ($q) use ($region){
//            $q->where('office.market_id', $region);
//        });

//            'office_id', $region)
//            ->orWhere('origin_office_id', $region);
    }

    /**
     * @param $query
     * @param array $roles
     *
     * @return mixed
     */
    public function scopeHasUser($query, $userId)
    {
        return $query->whereHas('reps', function ($query) use ($userId) {
            $query->where('user_has_leads.deleted_at', null);
            return $query->whereIn('users.id', [$userId]);
        });
    }

    public function scopeHasTeam($query, $teamId)
    {
        return $query->whereHas('user', function ($query) use ($teamId) {
           return $query->where('users.team_id', $teamId);
        });

    }

    public function scopeHasUserByPosition($query, $userId)
    {
        return $query->whereHas('reps', function ($query) use ($userId) {
            $query->where('user_has_leads.deleted_at', null);
            return $query->where('users.id', $userId)->where('position_id', 9);
        });
    }

    public function scopeIsClosed($query)
    {
//        $closedId = [7, 8, 9, 10, 11, 12, 13, 16];
        return $query->where('close_date', '!=', null);
    }

    public function scopeIsInstalled($query)
    {
        $closedId = [12, 13];
        return $query->whereIn('status_id', $closedId);
    }

    public function scopeIsInstalledDates($query, $start, $end)
    {
        $closedId = [12, 13];
        $query->whereIn('status_id', $closedId)
            ->whereHas('appointments', function ($q) use ($start, $end) {
                $q->where('type_id', 5);
                $q->whereBetween('start_time', [$start, $end]);
            });
        return $query;
//
    }


    public function scopeIsJobInJeopardy($query)
    {
        $closedId = [16];
        return $query->whereIn('status_id', $closedId);
    }

    public function scopeIsQualified($query)
    {

        $closedId = [2, 3, 5];
        return $query->whereIn('status_id', $closedId);
    }

    public function scopeIsCreditPass($query, $bool)
    {
        $creditPass = [2, 5, 6];
        if ($bool) {
            return $query->whereIn('credit_status_id', $creditPass);
        } else {
            return $query->whereNotIn('credit_status_id', $creditPass);
        }

    }
    public function scopeIsCreditPassByDate($query, $dates)
    {
        $creditPass = [2, 5, 6];

            return $query->whereIn('credit_status_id', $creditPass)->audit()->whereBetween('audit.created_at', $dates);




    }

    public function scopeIsLostOpportunity($query)
    {
        $lostId = [14, 15, 17, 19, 20, 23];
        return $query->whereIn('status_id', $lostId);
    }

    public function scopeIsCreditPassWithPending($query)
    {

        $creditPass = [2, 3, 5, 6, 9];
        return $query->whereIn('credit_status_id', $creditPass);
    }

    public function scopeIsLost($query)
    {
        $closedId = [14,15,16,17,18,19,20, 21, 23];

        return $query->where('close_date', '!=', null);
    }

    public function scopeIsClosedDates($query, $start, $end)
    {

//        $query->where(function ($q) use ($start, $end) {
//            $q->whereBetween('created_at', [$start, $end]);
//
//        });
        $query->whereHas('salesPacket', function ($q) use ($start, $end) {
            $q->whereBetween('cpuc_doc_signed', [$start, $end]);
        });
//        $query->isClosed();
        return $query;
//
    }

    public function scopeIsSatSearch($query, $bool)
    {
        return $query->whereHas('salesPacket', function ($q) use ($bool) {
            $q->where('sat', $bool);
        });
    }

    public function scopeIsSat($query, $bool, $between)
    {
        return $query->whereHas('salesPacket', function ($q) use ($bool) {
            $q->where('sat', $bool);
        })->whereHas('appointments', function ($q) use ($between) {
            $q->where('type_id', 6);
            $q->whereBetween('finish_time', $between);
        });
    }

    public function scopeHasCloseRange($query, $between){
        $query->whereHas('appointments', function ($q) use ($between) {
            $q->where('type_id', 6);
            $q->whereBetween('start_time', $between);
        });
    }
    public function scopeHasPosition($query, $positionId){
        $query->whereHas('user', function ($q) use ($positionId){
           $q->where('position_id', $positionId);
        });
    }


    /**
     * @param $query
     * @param bool $status
     *
     * @return mixed
     */
    public function scopeActive($query, $status = true)
    {
        return $query->where('active', $status);
    }
}
