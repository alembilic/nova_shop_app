<?php

namespace App\Policies;

use App\Models\UserStoresPivot;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class UserStorePolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view any models.
     *
     * @param  \App\Models\User  $user
     * @return mixed
     */

    /**
     * Determine whether the user can view the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\=UserStoresPivot  $=UserStoresPivot
     * @return mixed
     */
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
