<?php

namespace App\Policies;

use Illuminate\Auth\Access\HandlesAuthorization;
use App\User;
use App\Page;

class PagePolicy
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

    public function preview(User $user, Page $page){
        return $user->id == $page->user_id;
    }
    public function edit(User $user, Page $page){
        $userIsAdmin = $user->isAdminOfThePage($page);
        return ($user->id == $page->user_id || $userIsAdmin);
    }
    public function destroy(User $user, Page $page){
        return $user->id == $page->user_id;
    }
    public function invite(User $user, Page $page){
        $userIsAdmin = $user->isAdminOfThePage($page);
        return ($user->id == $page->user_id || $userIsAdmin);
    }
}
