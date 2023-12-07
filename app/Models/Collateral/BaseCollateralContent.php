<?php


namespace App\Models\Collateral;


use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Support\Facades\Storage;
use OwenIt\Auditing\Auditable;
use OwenIt\Auditing\Contracts\Auditable as AuditableInterface;

class BaseCollateralContent extends Authenticatable implements AuditableInterface
{
	use Auditable,
		SoftDeletes;

	/* @array $appends */
	public $appends = ['url', 'uploaded_time', 'size_in_kb'];
	protected $table = 'collateral_contents';
	protected
		$fillable = [
		'name',
		'description',
		'path',
		'user_id',
		'size',
	];

	public function getUrlAttribute()
	{
		return Storage::disk('s3')->url($this->path);
	}

	public function getUploadedTimeAttribute()
	{
		return $this->created_at->diffForHumans();
	}

	public function getSizeInKbAttribute()
	{
		return round($this->size / 1024, 2);
	}
//    public static function boot()
//    {
//        dd(auth()->user());
//        parent::boot();
//        static::creating(function ($collateralContent) {
//            $collateralContent->user_id = auth()->user()->id;
//        });
//    }
}
