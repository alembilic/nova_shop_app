<?php

namespace App\Policies;

use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class StorePolicy
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

    public function view()
    {
        return true;
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
