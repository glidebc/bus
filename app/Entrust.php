<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Laraveldaily\Quickadmin\Observers\UserActionsObserver;


use Illuminate\Database\Eloquent\SoftDeletes;

class Entrust extends Model {

    use SoftDeletes;

    /**
    * The attributes that should be mutated to dates.
    *
    * @var array
    */
    protected $dates = ['deleted_at'];

    protected $table    = 'entrust';
    
    protected $fillable = [
          'customer_id',
          'name',
          'owner_user',
          'status'
    ];
    

    public static function boot()
    {
        parent::boot();

        Entrust::observe(new UserActionsObserver);
    }
    
    
    
    
}