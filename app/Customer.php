<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Laraveldaily\Quickadmin\Observers\UserActionsObserver;


use Illuminate\Database\Eloquent\SoftDeletes;

class Customer extends Model {

    use SoftDeletes;

    /**
    * The attributes that should be mutated to dates.
    *
    * @var array
    */
    protected $dates = ['deleted_at'];

    protected $table    = 'customer';
    
    protected $fillable = [
          // 'agent_id',
          'name',
          'tax_title',
          'tax_num',
          'zip_code',
          'address',
          'contact_id',
          // 'contact',
          // 'com_tel',
          // 'com_fax',
          // 'mobile',
          'note',
          'is_agent',
          'owner_user'
    ];
    

    public static function boot()
    {
        parent::boot();

        Customer::observe(new UserActionsObserver);
    }
    
    
    
    
}