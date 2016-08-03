<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace App\Components;

use App\Model,
	App\Model\So,
	App\Forms\Form,
	Nette\Application\UI,
	Nette\Security;

/**
 * Description of ContactForm
 *
 * @author jakubmares
 */
class ContactForm extends UI\Control {

	/** @var So\Contact */
	private $record;

	/** @var string */
	private $type;

	/** @var int */
	private $companyId;

	/** @var Model\ContactManager */
	private $contactMan;

	/** @var Security\User */
	private $user;
	public $onSave;

	public function __construct($companyId, Model\ContactManager $contactMan, Security\User $user) {
		$this->companyId = $companyId;
		$this->contactMan = $contactMan;
		$this->user = $user;
	}

	public function setRecord(So\Contact $record) {
		$this->record = $record;
		$this['form']->setDefaults($record);
	}

	public function setType($type) {
		if (!array_key_exists($type, So\Contact::types())) {
			throw new \Exception('Nepovoleny typ kontaktu.');
		}
		$this->type = $type;
	}

	protected function createComponentForm() {
		$types = So\Contact::types();
		if(!$this->user->isAllowed(Model\PermissionManager::RESOURCE_ADMIN)){
			unset($types[So\Contact::TYPE_CONTACT_PERSON]);
		}
		
		$form = new Form();
		$form->addText(Model\ContactManager::COLUMN_EMAIL, '* e-mail:')
				->addRule(Form::FILLED,"Pole e-mail musí být vyplněno")
				->addCondition(Form::EMAIL);
		$form->addText(Model\ContactManager::COLUMN_PHONE, 'telefon:');
		if (!$this->type) {
			$form->addRadioList(Model\ContactManager::COLUMN_TYPE, '* Typ kontaktu:', $types)
					->addRule(Form::FILLED,"Je potřeba vybrat typkontaktu");
		} else {
			$form->addHidden(Model\ContactManager::COLUMN_TYPE, $this->type);
		}
		$form->addText(Model\ContactManager::COLUMN_NAME, 'Popis kontaktu:');
		if ($this->user->isAllowed(Model\PermissionManager::RESOURCE_ADMIN)) {
			$form->addText(Model\ContactManager::COLUMN_FUNCTION, 'Funkce');
		}
		$form->addHidden(Model\ContactManager::COLUMN_COMPANY_ID, $this->companyId);
		$form->addSubmit('send', 'Uložit');
		$form->onSuccess[] = [$this, 'processForm'];
		return $form;
	}

	public function processForm($form) {
		if ((int) $this->getParameter('id') && !$this->record) {
			throw new \Nette\Application\BadRequestException;
		}

		if ($this->record) {
			$this->contactMan->update($this->record->getId(), $form->getValues());
			$id = $this->record->getId();
		} else {
			$res = $this->contactMan->insert($form->getValues());
			$id = $res->offsetGet(Model\ContactManager::COLUMN_ID);
		}
		$this->onSave($this, $id);
	}

	public function render() {
		$template = $this->template;
		$template->setFile(__DIR__ . '/templates/contactForm.latte');
		$template->render();
	}

}
