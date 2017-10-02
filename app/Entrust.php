<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Laraveldaily\Quickadmin\Observers\UserActionsObserver;


use Illuminate\Database\Eloquent\SoftDeletes;

class Entrust extends Model {

    use SoftDeletes;

    /**
    * The attributes that should be mutated to dates.
    *
    * @var array
    */
    protected $dates = ['deleted_at'];

    protected $table    = 'entrust';
    
    protected $fillable = [
          'customer_id',
          'contact_id',
          'name',
          'start_date',
          'end_date',
          'publish_kind',
          'pay',
          'pay_status',
          'invoice_date',
          'invoice_num',
          'note',
          'owner_user',
          'status',
          'reject_text',
          'enum'
    ];
    

    public static function boot()
    {
        parent::boot();

        Entrust::observe(new UserActionsObserver);
    }
    
    
    
    
}