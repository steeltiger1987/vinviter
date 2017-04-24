<?php

namespace App\Policies;

use Illuminate\Auth\Access\HandlesAuthorization;
use App\User;
use App\Event;

class EventPolicy
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

    public function preview(User $user, Event $event){
        return $user->id == $event->user_id;
    }

    public function edit(User $user, Event $event){
        $userIsAdmin = $user->isAdminOfTheEvent($event);
        return ($user->id == $event->user_id || $userIsAdmin);
    }

    public function invite(User $user, Event $event){
        $userIsAdmin = $user->isAdminOfTheEvent($event);
        return ($user->id == $event->user_id || $userIsAdmin);
    }

    public function historyPhoto(User $user, Event $event){
        $userIsAdmin = $user->isAdminOfTheEvent($event);
        return ($user->id == $event->user_id || $userIsAdmin);
    }

    public function destroy(User $user, Event $event){
        return $user->id == $event->user_id;
    }

    public function publicPrivateAccess(User $user, Event $event){
        if($event->is_private){
            if($user->hasBasicAccessToEvent($event)){
                return true;
            }
            else{
                return false;
            }
        }
        else{
            return true;
        }
    }
}
