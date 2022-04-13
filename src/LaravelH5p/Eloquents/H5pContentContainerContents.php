<?php

namespace EscolaSoft\LaravelH5p\Eloquents;

use Illuminate\Database\Eloquent\Model;
use EscolaSoft\LaravelH5p\Traits\CombinedPrimaryKeysTrait;

class H5pContentContainerContents extends Model
{
    use CombinedPrimaryKeysTrait;

    public $timestamps = false;
    public $incrementing = false;

    protected $primaryKey = ['container_id', 'content_id'];

    protected $fillable = [
        'container_id',
        'content_id'
    ];
}
