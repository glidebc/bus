<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Laraveldaily\Quickadmin\Observers\UserActionsObserver;




class Dept extends Model {

    
    public $timestamps = false;
    

    protected $table    = 'dept';
    
    protected $fillable = [
          'name',
          'status'
    ];
    

    public static function boot()
    {
        parent::boot();

        Publishuser::observe(new UserActionsObserver);
    }
    
    
    
    
}