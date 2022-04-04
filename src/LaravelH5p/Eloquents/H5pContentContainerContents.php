<?php

namespace EscolaSoft\LaravelH5p\Eloquents;

use Illuminate\Database\Eloquent\Model;

class H5pContentContainerContents extends Model
{
    protected $primaryKey = ['container_id', 'content_id'];
    protected $fillable = [
        'container_id',
        'content_id'
    ];
}
