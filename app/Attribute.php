<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Attribute extends Model
{
    /**
     * Get all of the owning attributable page models.
     */
    public function pages()
    {
        return $this->morphedByMany('App\Page', 'attributable');
    }

    /**
     * Get all of the owning attributable event models.
     */
    public function events()
    {
        return $this->morphedByMany('App\Event', 'attributable');
    }

    public function children(){
        return $this->hasMany('App\Attribute', 'parent_id');
    }
}
