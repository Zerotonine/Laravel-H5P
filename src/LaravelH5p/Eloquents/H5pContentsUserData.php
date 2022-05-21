<?php

namespace EscolaSoft\LaravelH5p\Eloquents;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;

use App\Models\User;

class H5pContentsUserData extends Model
{
    public $timestamps = false;
    //public $incrementing = true;
    //protected $primaryKey = ['content_id', 'user_id', 'sub_content_id', 'data_id'];
    protected $primaryKey = 'id';
    protected $fillable = [
        'content_id',
        'user_id',
        'sub_content_id',
        'data_id',
        'data',
        'preload',
        'invalidate',
        'updated_at',
        'container_id'
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }

    // protected function setKeysForSaveQuery(Builder $query)
    // {
    //     return $query->where('content_id', $this->getAttribute('content_id'))
    //         ->where('user_id', $this->getAttribute('user_id'))
    //         ->where('sub_content_id', $this->getAttribute('sub_content_id'))
    //         ->where('data_id', $this->getAttribute('data_id'));
    // }

    /*protected function setKeysForSaveQuery($query)
    {
        return $query->where('content_id', $this->getAttribute('content_id'))
            ->where('user_id', $this->getAttribute('user_id'))
            ->where('sub_content_id', $this->getAttribute('sub_content_id'))
            ->where('data_id', $this->getAttribute('data_id'));
    }*/
}
