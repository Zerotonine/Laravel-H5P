<?php

namespace EscolaSoft\LaravelH5p\Eloquents;

use DB;
use Illuminate\Database\Eloquent\Model;


class H5pContentContainer extends Model
{
    protected $table = 'h5p_content_container';
    protected $primaryKey = 'id';
    protected $fillable = [
        'title',
        'user_id',
        'background_path',
        'watermark_path',
        'watermark_opacity',
        'multipliers'
    ];

    public function get_user()
    {
        return (object) DB::table('users')->where('id', $this->user_id)->first();
    }

    public function get_content_count(){
        return $this->contents->count();
    }

    public function contents(){
        // return $this->belongsToMany(H5pContent::class, 'h5p_content_container_contents', 'content_id', 'container_id');
        return $this->belongsToMany(H5pContent::class, 'h5p_content_container_contents', 'container_id', 'content_id');
    }

    public function results(){
        return $this->hasMany(H5pResult::class, 'container_id');
    }
}
