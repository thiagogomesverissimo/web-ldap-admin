<?php

namespace App\Providers;

use Illuminate\Support\Facades\Gate;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Auth;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The policy mappings for the application.
     *
     * @var array
     */
    protected $policies = [
        'App\Model' => 'App\Policies\ModelPolicy',
    ];

    /**
     * Register any authentication / authorization services.
     *
     * @return void
     */
    public function boot()
    {
        $this->registerPolicies();

        Gate::resource('ldapusers', 'App\Policies\LdapUserPolicy');

        # admin 
        Gate::define('admin', function ($user) {
            $admins_senhaunica = explode(',', trim(env('SUPERADMINS_SENHAUNICA')));   
            return in_array(Auth::user()->username_senhaunica, $admins_senhaunica);

        });

        # logado 
        Gate::define('logado', function ($user) { 
            if($user){
                return true;            
            }
            else {
                return false;
            }
        });
    }
}
