<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Laraveldaily\Quickadmin\Observers\UserActionsObserver;


use Illuminate\Database\Eloquent\SoftDeletes;

class Agent extends Model {

    use SoftDeletes;

    /**
    * The attributes that should be mutated to dates.
    *
    * @var array
    */
    protected $dates = ['deleted_at'];

    protected $table    = 'agent';
    
    protected $fillable = [
          'name',
          'tax_title',
          'tax_num',
          'zip_code',
          'address',
          'contact',
          'com_tel',
          'com_fax',
          'mobile',
          'note'
    ];
    

    public static function boot()
    {
        parent::boot();

        Agent::observe(new UserActionsObserver);
    }
    
    
    
    
}