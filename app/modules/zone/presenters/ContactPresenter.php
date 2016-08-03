<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace App\Module\Zone\Presenters;

use App\Model,
	App\Model\So,
	App\Components;

/**
 * Description of ContactPresenter
 *
 * @author jakubmares
 */
class ContactPresenter extends BasePresenter {

	/** @var So\Address */
	private $record;

	/** @var So\Company */
	private $company;

	/** @var Model\ContactManager */
	private $contactMan;

	/** @var Components\IContactFormFactory */
	private $contactFormFa;

	public function __construct(Model\ContactManager $contactMan, Components\IContactFormFactory $contactFormFa) {
		$this->contactMan = $contactMan;
		$this->contactFormFa = $contactFormFa;
	}

	public function setRecord($id) {
		$this->record = $this->contactMan->get($id);
		if (!$this->record) {
			throw new BadRequestException('Kontakt nebyl nalezen');
		}
	}

	public function setCompany($id) {
		if(!$this->companyMan->isAllow($id,  $this->user->getId()) && !$this->user->isInRole(Model\RoleManager::ROLE_ADMIN)){
			$this->flashMessage('K této operaci nemáte oprávnění');
			$this->redirect(':Front:Homepage:default');
		}
		$this->company = $this->companyMan->get($id);
		if (!$this->company) {
			throw new BadRequestException('Společnost nebyla nalezena');
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
	
	public function actionDelete($id){
		$this->setRecord($id);
		$companyId = $this->record->getCompanyId();
		$this->contactMan->delete($id);
		$this->redirect('Company:detail',$companyId);
	}

	public function renderEdit() {
		$this->template->company = $this->company;
	}

	public function createComponentForm() {
		$form = $this->contactFormFa->create($this->company->getId());
		$form->onSave = function(Components\ContactForm $control, $id) {
			$this->flashMessage('Kontakt společnosti byl upraven');
			$this->redirect('Company:detail', $this->company->getId());
		};
		return $form;
	}

	/**
	 * @return Components\ContactForm 
	 */
	private function getForm() {
		return $this['form'];
	}

}
