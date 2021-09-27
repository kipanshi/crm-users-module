<?php

namespace Crm\UsersModule\Scenarios;

use Crm\ApplicationModule\Criteria\ScenarioParams\StringLabeledArrayParam;
use Crm\ApplicationModule\Criteria\ScenariosCriteriaInterface;
use Crm\UsersModule\Repository\UsersRepository;
use Nette\Database\Table\IRow;
use Nette\Database\Table\Selection;
use Nette\Localization\ITranslator;

class UserSourceCriteria implements ScenariosCriteriaInterface
{
    private $usersRepository;

    private $translator;

    public function __construct(UsersRepository $usersRepository, ITranslator $translator)
    {
        $this->usersRepository = $usersRepository;
        $this->translator = $translator;
    }

    public function params(): array
    {
        $sources = $this->usersRepository->getUserSources();

        // Allow entering arbitrary values (free-solo mode)
        return [
            new StringLabeledArrayParam('source', 'Source', $sources, 'or', true),
        ];
    }

    public function addConditions(Selection $selection, array $paramValues, IRow $criterionItemRow): bool
    {
        $values = $paramValues['source'];
        $selection->where('users.source IN (?)', $values->selection);

        return true;
    }

    public function label(): string
    {
        return $this->translator->translate('users.admin.scenarios.source.label');
    }
}
