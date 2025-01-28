<?php

namespace ReadmeDisplay\App\Models;

use ReadmeDisplay\App\Models\Model;

class Plugin extends Model
{
    const CREATED_AT = 'created_at';

    const UPDATED_AT = 'updated_at';

    protected $table = 'rd_plugins';

    protected $fillable = [
        'name',
        'slug',
    ];
}
