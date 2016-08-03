<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace App\Module\Zone\Presenters;

use App\Model\So,
	App\Model,
	App\Components;

/**
 * Description of AddressPresenter
 *
 * @author jakubmares
 */
class AddressPresenter extends BasePresenter {

	/** @var So\Address */
	private $record;

	/** @var So\Company */
	private $company;

	/** @var Model\AddressManager */
	private $addressMan;

	/** @var Components\IAddressFormFactory */
	private $addressFormFa;

	public function __construct(Model\AddressManager $addressMan, Components\IAddressFormFactory $addressFormFa) {
		$this->addressMan = $addressMan;
		$this->addressFormFa = $addressFormFa;
	}

	private function setRecord($id) {
		$this->record = $this->addressMan->get($id);
		if (!$this->record) {
			throw new BadRequestException('Adresa nebyla nalezena');
		}
	}

	private function setCompany($id) {
		if (!$this->companyMan->isAllow($id, $this->user->getId()) && !$this->user->isInRole(Model\RoleManager::ROLE_ADMIN)) {
			$this->flashMessage('K této operaci nemáte oprávnění');
			$this->redirect(':Front:Homepage:default');
		}
		$this->company = $this->companyMan->get($id);
		if (!$this->company) {
			throw new BadRequestException('Společnost nebyla nalezena');
		}
	}

	/**
	 * 
	 * @return Components\AddressForm
	 */
	private function getForm() {
		return $this['form'];
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

	public function actionCreateBilling($companyId) {
		$this->setCompany($companyId);
		$form = $this->getForm();
		$form->setType(So\Address::TYPE_BILLING);
		$this->setView('create');
	}

	public function actionEditBilling($id) {
		$this->setRecord($id);
		$this->setCompany($this->record->getCompanyId());
		$form = $this->getForm();
		$form->setRecord($this->record);
		$form->setType(So\Address::TYPE_BILLING);
		$this->setView('edit');
	}

	public function actionCreateBase($companyId) {
		$this->setCompany($companyId);
		$form = $this->getForm();
		$form->setType(So\Address::TYPE_BASE);
		$this->setView('create');
	}

	public function actionEditBase($id) {
		$this->setRecord($id);
		$this->setCompany($this->record->getCompanyId());
		$form = $this->getForm();
		$form->setRecord($this->record);
		$form->setType(So\Address::TYPE_BASE);
		$this->setView('edit');
	}

	public function actionDelete($id) {
		$this->setRecord($id);
		$companyId = $this->record->getCompanyId();
		$this->addressMan->delete($id);
		$this->flashMessage('Adresa byla smazána');
		$this->redirect('Company:detail', $companyId);
	}

	protected function createComponentForm() {
		$form = $this->addressFormFa->create($this->company->getId());
		$form->onSave = function($control) {
			$this->flashMessage('Adresa uložena');
			$this->redirect('Company:detail', $this->company->getId());
		};
		return $form;
	}

}
