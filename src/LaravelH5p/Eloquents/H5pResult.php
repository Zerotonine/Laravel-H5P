<?php

namespace EscolaSoft\LaravelH5p\Eloquents;

use Illuminate\Database\Eloquent\Model;

class H5pResult extends Model
{
    public $timestamps = false;
    protected $primaryKey = 'id';
    protected $fillable = [
        'content_id',
        'user_id',
        'score',
        'max_score',
        'opened',
        'finished',
        'time',
        'container_id'
    ];

    public function bundle(){
        return $this->belongsTo(H5pContentContainer::class, 'container_id');
    }
}
