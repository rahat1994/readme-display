<?php

namespace ReadmeDisplay\App\Models;

use ReadmeDisplay\Framework\Database\Orm\Model as BaseModel;

class Model extends BaseModel
{   
    protected $guarded = ['id', 'ID'];

    /**
     * Prepare a date for array / JSON serialization.
     *
     * @param  \DateTimeInterface  $date
     * @return string
     */
    protected function serializeDate(\DateTimeInterface $date)
    {
        return $date->format($this->getDateFormat());
    }
}