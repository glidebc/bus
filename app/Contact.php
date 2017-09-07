<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Laraveldaily\Quickadmin\Observers\UserActionsObserver;




class Contact extends Model {

    

    

    protected $table    = 'contact';
    
    protected $fillable = ['name'];
    

    public static function boot()
    {
        parent::boot();

        Contact::observe(new UserActionsObserver);
    }
    
    
    
    
}