<?php

namespace Crm\UsersModule\Locale;

use Contributte\Translation\LocalesResolvers\ResolverInterface;
use Contributte\Translation\Translator;
use Crm\UsersModule\User\UserData;
use Nette\Http\IRequest;

class UserDataLocaleResolver implements ResolverInterface
{
    public function __construct(
        private UserData $userData,
        private IRequest $request
    ) {
    }

    public function resolve(Translator $translator): ?string
    {
        $userData = $this->userData->getCurrentUserData($this->request);
        if ($userData) {
            return $userData->basic->locale ?? null;
        }

        return null;
    }
}
