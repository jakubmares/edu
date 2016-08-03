<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace App\Components;

use Nette\Application\UI,
	App\Model\So,
	App\Forms\Form,
	App\Model,
	Extensions,
	Nette\Security;

/**
 * Description of CompanyForm
 *
 * @author jakubmares
 */
class CompanyForm extends UI\Control {

	/** @var Model\UserManager */
	private $userMan;

	/** @var Model\CompanyManager */
	private $companyMan;

	/** @var So\Company */
	private $company;

	/** @var Security\User */
	private $user;
	public $onSave;

	public function __construct(Model\UserManager $userMan, Model\CompanyManager $companyMan, Security\User $user) {
		parent::__construct();
		$this->userMan = $userMan;
		$this->companyMan = $companyMan;
		$this->user = $user;
	}

	protected function createComponentForm() {
		$form = new Form();
		$form->addText(Model\CompanyManager::COLUMN_NAME, '* Jméno:')
				->addRule(Form::FILLED, 'Pole %label musí být vyplněno');
		$form->addText(Model\CompanyManager::COLUMN_IC, '* IČO:')
				->addRule(Form::LENGTH, 'IČO musí mít délku 8 číslic', 8)
				->addRule(Form::INTEGER, 'IČO musí být celé čislo')
				->addRule(Form::FILLED, 'Pole IC musí být vyplněno');
		$form->addText(Model\CompanyManager::COLUMN_DIC, 'DIČ:');
		$form->addTextArea(Model\CompanyManager::COLUMN_DESCRIPTION, 'Popis:')
				->setAttribute('class', 'mceEditor');
		$form->addText(Model\CompanyManager::COLUMN_WEB, 'Adresa webových stránek:');
		$form->addUpload(Model\CompanyManager::COLUMN_LOGO, 'Logo:');
		$form->addText(Model\CompanyManager::COLUMN_BANK_ACCOUNT,'Bankovní spojení:');

		if ($this->user->isAllowed(Model\PermissionManager::RESOURCE_ADMIN)) {
			$form->addSelect(Model\CompanyManager::COLUMN_POTENCIAL, 'Potenciál:', So\Company::potencials());
			$form->addSelect(Model\CompanyManager::COLUMN_TYPE, 'Typ', So\Company::types());
			$form->addSelect(Model\CompanyManager::COLUMN_STATUS, 'Status', So\Company::statuses());
			$form->addCheckbox(Model\CompanyManager::COLUMN_ACTIVE, 'Aktivní')->setDefaultValue(true);
			$form->addCheckbox(Model\CompanyManager::COLUMN_PARTNER, 'Partner portálu');
			$form->addCheckbox(Model\CompanyManager::COLUMN_TOP, 'Top firma');
			$form->addSelect(Model\CompanyManager::COLUMN_USER_ID, 'Správce',
							Extensions\ListHelper::fetchPair($this->userMan->getActive(), 'id', 'name'))
					->addRule(Form::FILLED, 'Musí být vybrán správce firmy');
			$form->addSelect(Model\CompanyManager::COLUMN_DEALER_ID, 'Dealer',
							Extensions\ListHelper::fetchPair($this->userMan->getDealers(), 'id', 'name'))
					->addRule(Form::FILLED, 'Musí být vybrán dealer firmy');
		}
		$form->addSubmit('send', 'Uložit');
		$form->onSuccess[] = [$this, 'processForm'];

		return $form;
	}

	public function processForm($form) {
		if ((int) $this->getParameter('id') && !$this->company) {
			throw new BadRequestException;
		}
		$values = $form->getValues();

		$logo = $values->offsetGet(Model\CompanyManager::COLUMN_LOGO);

		if ($logo->isImage()) {
			$values->offsetSet(Model\CompanyManager::COLUMN_LOGO, $this->companyMan->saveFileUpload($logo));
		} else {
			$values->offsetUnset(Model\CompanyManager::COLUMN_LOGO);
		}

		if ($this->company) {
			$this->companyMan->update($this->company->getId(), $values);
			$id = $this->company->getId();
		} else {
			$res = $this->companyMan->insert($values);
			$id = $res->offsetGet(Model\CompanyManager::COLUMN_ID);
		}
		$this->onSave($this, $id);
	}

	public function render() {
		$template = $this->template;
		$template->setFile(__DIR__ . '/templates/companyForm.latte');
		$template->render();
	}

	public function setCompany(So\Company $company) {
		$this->company = $company;
		$this['form']->setDefaults($company);
	}

}
