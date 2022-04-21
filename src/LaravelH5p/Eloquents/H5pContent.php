<?php

namespace EscolaSoft\LaravelH5p\Eloquents;

use App\User;
use DB;
use Illuminate\Database\Eloquent\Model;

//use App\Models\User;

class H5pContent extends Model
{
    protected $primaryKey = 'id';
    protected $fillable = [
        'user_id',
        'title',
        'library_id',
        'parameters',
        'filtered',
        'slug',
        'embed_type',
        'disable',
        'content_type',
        'author',
        'source',
        'year_from',
        'year_to',
        'license',
        'license_version',
        'license_extras',
        'author_comments',
        'changes',
        'default_languge',
        'keywords',
        'description',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function get_user()
    {
        return (object) DB::table('users')->where('id', $this->user_id)->first();
    }

    public function bundles(){
        // return $this->belongsToMany(H5pContentContainer::class, 'h5p_content_container_contents', 'container_id', 'content_id');
        return $this->belongsToMany(H5pContentContainer::class, 'h5p_content_container_contents', 'content_id', 'container_id');
    }

    public function library(){
        return $this->belongsTo(H5pLibrary::class);
    }
}
