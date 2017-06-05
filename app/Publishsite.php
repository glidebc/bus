<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Laraveldaily\Quickadmin\Observers\UserActionsObserver;


use Illuminate\Database\Eloquent\SoftDeletes;

class Publishsite extends Model {

    use SoftDeletes;

    /**
    * The attributes that should be mutated to dates.
    *
    * @var array
    */
    protected $dates = ['deleted_at'];

    protected $table    = 'publish_site';
    
    protected $fillable = ['name'];
    

    public static function boot()
    {
        parent::boot();

        Publishsite::observe(new UserActionsObserver);
    }
    
    
    
    
}