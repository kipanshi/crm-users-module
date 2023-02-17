<?php

namespace Crm\UsersModule\Events;

use League\Event\AbstractEvent;
use Nette\Database\Table\ActiveRow;

/**
 * NewUserEvent is emitted every time new user is created. Event is emitted even when user is not formally registered -
 * for example when unclaimed (backend only) user is created.
 *
 * States the user can go through: NEW (this) - registered - disabled - deleted.
 *
 * Use this event for actions which you want to execute for every new user.
 * Do not use this event for actions tied to the sign up process (e.g. sending welcome email).
 *
 * Unless you're really sure you want this, you probably want to use Crm\UsersModule\Events\UserRegisteredEvent instead.
 */
class NewUserEvent extends AbstractEvent implements UserEventInterface
{
    public function __construct(private ActiveRow $user)
    {
    }

    public function getUser(): ActiveRow
    {
        return $this->user;
    }
}
