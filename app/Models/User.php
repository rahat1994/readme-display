<?php

namespace ReadmeDisplay\App\Models;

use ReadmeDisplay\App\Models\Model;

class User extends Model
{   
    public $timestamps = false;

    protected $table = 'users';
    
    protected $primaryKey = 'ID';

    public function posts()
    {
        return $this->hasMany(Post::class, 'post_author', 'ID');
    }
}
