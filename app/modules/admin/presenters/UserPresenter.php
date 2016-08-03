<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace App\Module\Admin\Presenters;

use App\Forms\Form,
	App\Model\So,
	App\Model\RoleManager,
	App\Model\UserManager,
	Extensions,
	Grido;

/**
 * Description of UserPresenter
 *
 * @author jakubmares
 */
class UserPresenter extends BasePresenter {

	const FORM_ITEM_ROLES_ID = 'rolesId';

	/** @var RoleManager */
	private $roleMan;

	/** @var So\User */
	private $record;

	/** @persistent */
	public $backlink = '';

	public function __construct(RoleManager $roleMan) {
		parent::__construct();
		$this->roleMan = $roleMan;
	}

	private function setRecord($id) {
		$this->record = $this->userMan->get($id);
		if (!$this->record) {
			throw new BadRequestException('Uzivatel nebyl nalezen');
		}
	}

	public function renderList() {
		$this->template->records = $this->userMan->getAll();
	}

	public function renderDetail($id) {
		$this->setRecord($id);
		$this->template->record = $this->record;
		$this->backlink = $this->storeRequest();
	}

	public function renderNew() {
		
	}

	public function actionEdit($id) {
		$this->setRecord($id);
		$this['form']->offsetUnset(UserManager::COLUMN_PASSWORD_HASH);
		$defaults = $this->record->toArray();
		$defaults[self::FORM_ITEM_ROLES_ID] = $this->record->getRolesId();
		$this['form']->setDefaults($defaults);
	}

	public function renderEdit() {
		$this->template->record = $this->record;
	}

	protected function createComponentForm() {
		$form = new Form();
		$form->addText(UserManager::COLUMN_FIRSTNAME, 'Jmeno');
		$form->addText(UserManager::COLUMN_SURNAME, 'Prijmeni');
		$form->addText(UserManager::COLUMN_EMAIL, 'Email')
				->addCondition(Form::EMAIL);
		$form->addPassword(UserManager::COLUMN_PASSWORD_HASH, 'Heslo');
		$form->addCheckboxList(self::FORM_ITEM_ROLES_ID, 'Role', Extensions\ListHelper::fetchPair($this->roleMan->getAll(), 'id', 'role'));
		$form->addCheckbox(UserManager::COLUMN_ACTIVE, 'Aktivni');
		$form->addSubmit('save', 'Ulozit');
		$form->onSuccess[] = [$this, 'formSuccessed'];
		return $form;
	}

	public function formSuccessed(Form $form) {
		if ((int) $this->getParameter('id') && !$this->record) {
			throw new BadRequestException;
		}
		$values = $form->getValues();
		$rolesId = $values->offsetGet(self::FORM_ITEM_ROLES_ID);
		$values->offsetUnset(self::FORM_ITEM_ROLES_ID);
		if ($this->record) {
			$this->userMan->updateWithRoles($this->record->getId(), $values, $rolesId);
		} else {
			$this->userMan->insertWithRoles($values, $rolesId);
		}
		$this->flashMessage('Uzivatel byl ulozen.');
		$this->restoreRequest($this->backlink);
		$this->redirect('User:list');
	}

	protected function createComponentUserGrid($name) {
		$grid = new Grido\Grid($this, $name);
		$grid->setModel($this->userMan->getAllRows());
		$grid->filterRenderType = Grido\Components\Filters\Filter::RENDER_INNER;
		$grid->translator->lang = 'cs';
		$grid->defaultPerPage = 20;

		$grid->addColumnText(UserManager::COLUMN_FIRSTNAME, 'Jméno')->setFilterText();
		$grid->addColumnText(UserManager::COLUMN_SURNAME, 'Příjmení')->setFilterText();
		$grid->addColumnEmail(UserManager::COLUMN_EMAIL, 'E-mail')->setFilterText();

		$grid->addColumnText('role', 'Role')->setCustomRender(function(\Nette\Database\Table\ActiveRow $item) {
			$user = new So\User($item);
			return implode(' | ', $user->getRolesName());
		});

		$grid->addColumnText(UserManager::COLUMN_ACTIVE, 'Aktivní')
				->setReplacement([0 => 'Ne', 1 => 'Ano']);

		$grid->addFilterCheck(UserManager::COLUMN_ACTIVE, 'Pouze aktivní')
				->setCondition('= 1');



		$backlink = $this->storeRequest();



		$aEdit = \Nette\Utils\Html::el('a')->addAttributes(['style' => 'margin-right: 10px']);
		$aEdit->add('<span class="glyphicon glyphicon-edit" aria-hidden="true" data-toggle="tooltip" data-placement="bottom" title="Editovat"></span>');

		$grid->addActionHref('edit', 'Edit', 'User:edit', ['backlink' => $backlink])->setElementPrototype($aEdit);

		$aDet = \Nette\Utils\Html::el('a')->addAttributes(['style' => 'margin-right: 10px']);
		$aDet->add('<span class="glyphicon glyphicon-new-window" aria-hidden="true" data-toggle="tooltip" data-placement="bottom" title="Detail"></span>');

		$grid->addActionHref('detail', 'Detail', 'User:detail')->setElementPrototype($aDet);

		return $grid;
	}

}
