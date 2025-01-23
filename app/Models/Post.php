<?php

namespace ReadmeDisplay\App\Models;

use ReadmeDisplay\App\Models\Model;

class Post extends Model
{   
    const CREATED_AT = 'post_date';
    
    const UPDATED_AT = null;

    protected $table = 'posts';
    
    protected $primaryKey = 'ID';

    public static function boot()
    {
        parent::boot();
        
        static::addGlobalScope('type', function($query) {
            return $query->where('post_type', 'post');
        });
    }

    public function comments()
    {
        return $this->hasMany(
            Comment::class, 'comment_post_ID', 'comment_ID'
        );
    }
}
