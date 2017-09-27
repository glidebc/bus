<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Laraveldaily\Quickadmin\Observers\UserActionsObserver;




class EntrustItem extends Model {

    

    

    protected $table    = 'entrust_item';
    
    protected $fillable = [
          'entrust_id',
          'no',
          'name',
          'cost',
          'cost_text'
    ];
    

    public static function boot()
    {
        parent::boot();

        EntrustItem::observe(new UserActionsObserver);
    }
    
    
    
    
}