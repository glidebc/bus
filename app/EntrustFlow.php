<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Laraveldaily\Quickadmin\Observers\UserActionsObserver;




class EntrustFlow extends Model {

    
    public $timestamps = false;
    

    protected $table    = 'entrust_flow';
    
    protected $fillable = [
          'entrust_id',
          'status'
    ];
    

    public static function boot()
    {
        parent::boot();

        Publishuser::observe(new UserActionsObserver);
    }
    
    
    
    
}