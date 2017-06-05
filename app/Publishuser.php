<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Laraveldaily\Quickadmin\Observers\UserActionsObserver;




class Publishuser extends Model {

    

    

    protected $table    = 'publish_user';
    
    protected $fillable = [
          'user_id',
          'color_name',
          'dept'
    ];
    

    public static function boot()
    {
        parent::boot();

        Publishuser::observe(new UserActionsObserver);
    }
    
    
    
    
}