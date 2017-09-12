<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Laraveldaily\Quickadmin\Observers\UserActionsObserver;


use Illuminate\Database\Eloquent\SoftDeletes;

class Publishuser extends Model {

    use SoftDeletes;

    /**
    * The attributes that should be mutated to dates.
    *
    * @var array
    */
    protected $dates = ['deleted_at'];
    
    protected $table    = 'publish_user';
    
    protected $fillable = [
          'user_id',
          'user_name',
          'color_name',
          'dept_id',
          'team_id'
    ];
    

    public static function boot()
    {
        parent::boot();

        Publishuser::observe(new UserActionsObserver);
    }
    
    
    
    
}