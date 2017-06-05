<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Laraveldaily\Quickadmin\Observers\UserActionsObserver;


use Illuminate\Database\Eloquent\SoftDeletes;

class CustomerAgent extends Model {

    use SoftDeletes;

    /**
    * The attributes that should be mutated to dates.
    *
    * @var array
    */
    protected $dates = ['deleted_at'];

    protected $table    = 'customer_agent';
    
    protected $fillable = [
          'customer_id',
          'agent_id',
          'status'
    ];
    

    public static function boot()
    {
        parent::boot();

        CustomerAgent::observe(new UserActionsObserver);
    }
    
    
    
    
}