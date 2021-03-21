<?php

namespace App\Policies;

use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class UserPolicy
{
    use HandlesAuthorization;

    /**
     * Create a new policy instance.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    public function viewAny()
    {
        return auth()->user()->role == 'admin';
    }

    public function view()
    {
        return auth()->user()->role == 'admin';
    }

    public function update()
    {
        return auth()->user()->role == 'admin';
    }

    public function delete()
    {
        return auth()->user()->role == 'admin';
    }

    public function create()
    {
        return auth()->user()->role == 'admin';
    }
}
