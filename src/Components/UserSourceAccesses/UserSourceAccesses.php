<?php

namespace Crm\UsersModule\Components;

use Crm\ApiModule\Repository\UserSourceAccessesRepository;
use Crm\ApplicationModule\Widget\BaseLazyWidget;
use Crm\ApplicationModule\Widget\LazyWidgetManager;

/**
 * This widget fetches last user accesses from access repository
 * and renders bootstrap panel with last access date for each access type.
 *
 * @package Crm\UsersModule\Components
 */
class UserSourceAccesses extends BaseLazyWidget
{
    private $templateName = 'user_source_accesses.latte';

    private $userSourceAccessesRepository;

    public function __construct(
        LazyWidgetManager $lazyWidgetManager,
        UserSourceAccessesRepository $userSourceAccessesRepository
    ) {
        parent::__construct($lazyWidgetManager);
        $this->userSourceAccessesRepository = $userSourceAccessesRepository;
    }

    public function header($id = '')
    {
        return 'User Source Access';
    }

    public function identifier()
    {
        return 'usersourceaccess';
    }

    public function render($id)
    {
        $accesses = $this->userSourceAccessesRepository->getByUser($id);
        $this->template->accesses = $accesses;
        
        $this->template->setFile(__DIR__ . '/' . $this->templateName);
        $this->template->render();
    }
}
