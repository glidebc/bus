<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Laraveldaily\Quickadmin\Observers\UserActionsObserver;


use Illuminate\Database\Eloquent\SoftDeletes;

class Publishposition extends Model {

    use SoftDeletes;

    /**
    * The attributes that should be mutated to dates.
    *
    * @var array
    */
    protected $dates = ['deleted_at'];

    protected $table    = 'publish_position';
    
    protected $fillable = [
          'site_id',
          'name',
          'turns_count'
    ];
    

    public static function boot()
    {
        parent::boot();

        Publishposition::observe(new UserActionsObserver);
    }
    
    
    
    
}