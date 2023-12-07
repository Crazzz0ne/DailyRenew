<?php


namespace App\Models\SalesFlow\Lead;


use App\Models\SalesFlow\Lead\Traits\Relationship\LineRelationship;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Line extends Model
{
    use LineRelationship;
    use SoftDeletes;


    protected $table = 'queues';

    protected $guarded = [

    ];

    protected $casts = [
        'created_at' => 'datetime:Y-m-d h:m',
        'filled_time' => 'datetime:Y-m-d h:m',
    ];

    public function getPositionAttribute()
    {

        if ($this->type === 'sp1'){
            return Line::where('type', '=', $this->type)
                ->whereHas('lead', function ($q){
                    $q->where('office_id', '=', $this->lead->office_id);
                })
                ->where('filled_user_id', '=', null)
                ->where('created_at', '<', Carbon::parse($this->created_at)->toDateTimeString())
                ->count();

        }else {
            return Line::where('type', '=', $this->type)
                ->where('filled_user_id', '=', null)
                ->where('created_at', '<', Carbon::parse($this->created_at)->toDateTimeString())
                ->count();
        }

    }

}
