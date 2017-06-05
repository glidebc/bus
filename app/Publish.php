<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Laraveldaily\Quickadmin\Observers\UserActionsObserver;


use Illuminate\Database\Eloquent\SoftDeletes;

class Publish extends Model {

    use SoftDeletes;

    /**
    * The attributes that should be mutated to dates.
    *
    * @var array
    */
    protected $dates = ['deleted_at'];

    protected $table    = 'publish';
    
    protected $fillable = [
          'entrust_id',
          'date',
          'publish_position_id',
          'status'
    ];
    

    public static function boot()
    {
        parent::boot();

        Publish::observe(new UserActionsObserver);
    }
    
    
    
    
}