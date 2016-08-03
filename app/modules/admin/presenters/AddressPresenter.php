<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace App\Module\Admin\Presenters;

use App\Model\So,
	App\Forms\Form,
	App\Model\AddressManager,
	App\Model\CountryManager,
	Nette\Application\BadRequestException,
	App\Components;

/**
 * Description of AddressPresenter
 *
 * @author jakubmares
 */
class AddressPresenter extends BasePresenter {

	/** @var AddressManager */
	private $addressMan;

	/** @var CountryManager */
	private $countryMan;

	/** @var Components\IAddressFormFactory */
	private $addressFormFa;

	/** @var So\Company */
	private $company;

	/** @var So\Address */
	private $record;

	public function __construct(AddressManager $addressMan, CountryManager $countryMan, Components\IAddressFormFactory $addressFormFa) {
		$this->addressMan = $addressMan;
		$this->countryMan = $countryMan;
		$this->addressFormFa = $addressFormFa;
	}

	private function setCompany($companyId) {
		$this->company = $this->companyMan->get($companyId);
		if (!$this->company) {
			throw new BadRequestException('Firma nebyla nalezena');
		}
	}

	private function setRecord($id) {
		$this->record = $this->addressMan->get($id);
		if (!$this->record) {
			throw new BadRequestException('Adresa nebyla nalezena');
		}
	}

	public function actionCreate($companyId) {
		$this->setCompany($companyId);
	}

	public function renderCreate() {
		$this->template->company = $this->company;
	}

	public function actionEdit($id) {
		$this->setRecord($id);
		$this->setCompany($this->record->getCompanyId());
		$form = $this->getForm();
		$form->setRecord($this->record);
	}

	public function renderEdit() {
		$this->template->company = $this->company;
	}

	public function actionDelete($id) {
		$this->setRecord($id);
		$companyId = $this->record->getCompanyId();
		$this->addressMan->delete($id);
		$this->flashMessage('Adresa byla smazana');
		$this->redirect('Company:company', $companyId);
	}

	protected function createComponentForm() {
		$form = $this->addressFormFa->create($this->company->getId());
		$form->onSave = function($control) {
			$this->flashMessage('Adresa byla uložena');
			$this->redirect('Company:company', $this->company->getId());
		};
		return $form;
	}

	/**
	 * 
	 * @return Components\AddressForm
	 */
	public function getForm(){
		return $this['form'];
	}

}
