<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Laraveldaily\Quickadmin\Observers\UserActionsObserver;


use Illuminate\Database\Eloquent\SoftDeletes;

class Contact extends Model {

    use SoftDeletes;

    /**
    * The attributes that should be mutated to dates.
    *
    * @var array
    */
    protected $dates = ['deleted_at'];

    protected $table    = 'contact';
    
    protected $fillable = [
          'customer_id',
          'name',
          'zip_code',
          'address',
          'tel',
          'fax',
          'mobile',
          'email',
          'owner_user'
    ];
    

    public static function boot()
    {
        parent::boot();

        Contact::observe(new UserActionsObserver);
    }
    
    
    
    
}