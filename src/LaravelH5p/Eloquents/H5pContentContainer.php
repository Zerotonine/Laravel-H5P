<?php

namespace EscolaSoft\LaravelH5p\Eloquents;

use DB;
use Illuminate\Database\Eloquent\Model;


class H5pContentContainer extends Model
{
    protected $primaryKey = 'id';
    protected $fillable = [
        'title',
        'user_id'
    ];

}
