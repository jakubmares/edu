<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace App\Components;

use Nette\Application\UI,
	App\Model,
	App\Model\So,
	App\Forms\Form;

/**
 * Description of AddressForm
 *
 * @author jakubmares
 */
class AddressForm extends UI\Control {

	/** @var So/Address */
	private $record;

	/** @var Model\AddressManager */
	private $addressMan;
	
	/** @var Model\CountryManager */
	private $countryMan;

	/** @var int */
	private $companyId;
	private $type;
	public $onSave;

	public function __construct($companyId, Model\AddressManager $addressMan, Model\CountryManager $countryMan) {
		$this->addressMan = $addressMan;
		$this->countryMan = $countryMan;
		$this->companyId = $companyId;
	}

	public function setRecord(So\Address $record) {
		$this->record = $record;
		$this['form']->setDefaults($record);
	}

	public function setType($type) {
		if (!array_key_exists($type, So\Address::types())) {
			throw new \Exception('Nepovoleny typ kontaktu.');
		}
		$this->type = $type;
	}

	protected function createComponentForm() {
		$form = new Form();
		if(!$this->type){
		$form->addSelect(Model\AddressManager::COLUMN_TYPE, 'Typ:', So\Address::types());
		}  else {
			$form->addHidden(Model\AddressManager::COLUMN_TYPE, $this->type);
		}
		$form->addText(Model\AddressManager::COLUMN_STREET, 'Ulice:');
		$form->addText(Model\AddressManager::COLUMN_REGISTRY_NUMBER, 'Číslo popisné:');
		$form->addText(Model\AddressManager::COLUMN_HOUSE_NUMBER, 'Číslo orientční:');
		$form->addText(Model\AddressManager::COLUMN_CITY, 'Město:');
		$form->addText(Model\AddressManager::COLUMN_ZIP, 'PSČ:');
		$form->addSelect(Model\AddressManager::COLUMN_COUNTRY_KEY, 'Stát:',
						\Extensions\ListHelper::fetchPair($this->countryMan->getAll(), Model\CountryManager::TABLE_COLUMN_KEY,
								Model\CountryManager::TABLE_COLUMN_COUNTRY))
				->setDefaultValue($this->countryMan->getDefaultCountryKey());
		$form->addText(Model\AddressManager::COLUMN_NOTE, 'Poznámka:');
//		$form->addText(AddressManager::TABLE_COLUMN_LATITUDE, 'Zeměpisná šířka:');
//		$form->addText(AddressManager::TABLE_COLUMN_LONGITUDE, 'Zeměpisná délka:');
		$form->addHidden(Model\AddressManager::COLUMN_COMPANY_ID,$this->companyId);
		$form->addSubmit('send', 'Uložit');
		$form->onSuccess[] = [$this, 'processForm'];
		return $form;
	}

	public function processForm(Form $form) {
		if ((int) $this->getParameter('id') && !$this->address) {
			throw new BadRequestException;
		}

		if ($this->record) {
			$this->addressMan->update($this->record->getId(), $form->getValues());
		} else {
			$this->addressMan->insert($form->getValues());
		}
		$this->onSave($this);
	}

	public function render() {
		$template = $this->template;
		$template->setFile(__DIR__ . '/templates/addressForm.latte');
		$template->render();
	}

}
