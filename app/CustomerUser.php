<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Laraveldaily\Quickadmin\Observers\UserActionsObserver;




class CustomerUser extends Model {

    
    public $timestamps = false;
    

    protected $table    = 'customer_user';
    
    protected $fillable = [
          'customer_id',
          'user_id'
    ];
    

    public static function boot()
    {
        parent::boot();

        Publishuser::observe(new UserActionsObserver);
    }
    
    
    
    
}