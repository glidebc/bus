<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Laraveldaily\Quickadmin\Observers\UserActionsObserver;


use Illuminate\Database\Eloquent\SoftDeletes;

class CustomerType extends Model {

    use SoftDeletes;

    /**
    * The attributes that should be mutated to dates.
    *
    * @var array
    */
    protected $dates = ['deleted_at'];

    protected $table    = 'customer_type';
    
    protected $fillable = ['name'];
    

    public static function boot()
    {
        parent::boot();

        CustomerType::observe(new UserActionsObserver);
    }
    
    
    
    
}