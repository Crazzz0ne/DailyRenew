<?php


namespace App\Models\Auth;



use App\Models\Auth\Traits\Relationship\UserUploadRelationship;
use Illuminate\Database\Eloquent\Model;

class UserUpload extends Model
{
    use UserUploadRelationship;

    protected $fillable = [
        'user_id',
        'type',
        'path',
        'size',

    ];

}
