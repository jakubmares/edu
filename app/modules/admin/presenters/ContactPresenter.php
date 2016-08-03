<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace App\Module\Admin\Presenters;

use App\Model\So,
	App\Components,
	App\Model\ContactManager;

/**
 * Description of ContactPresenter
 *
 * @author jakubmares
 */
class ContactPresenter extends BasePresenter {

	/** @var ContactManager */
	private $contactMan;

	/** @var Components\IContactFormFactory */
	private $contactFormFa;

	/** @var So\Contact */
	private $contact;

	/** @var So\Company */
	private $company;
	
	/** @persistent */
    public $backlink = '';

	public function __construct(ContactManager $contactMan, Components\IContactFormFactory $contactFormFa) {
		$this->contactMan = $contactMan;
		$this->contactFormFa = $contactFormFa;
	}

	private function setCompany($companyId) {
		$this->company = $this->companyMan->get($companyId);
		if (!$this->company) {
			throw new BadRequestException('Firma nebyla nalezena');
		}
	}

	private function setContact($id) {
		$this->contact = $this->contactMan->get($id);
		if (!$this->contact) {
			throw new BadRequestException('Firma nebyla nalezena');
		}
	}

	public function actionCreate($companyId) {
		$this->setCompany($companyId);
	}

	public function renderCreate() {
		$this->template->company = $this->company;
	}

	public function actionEdit($id) {
		$this->setContact($id);
		$this->setCompany($this->contact->getCompanyId());
		$form = $this->getForm();
		$form->setRecord($this->contact);
	}

	public function renderEdit() {
		$this->template->company = $this->company;
	}
	
	public function actionCreateContact($companyId){
		$this->setCompany($companyId);
		$form = $this->getForm();
		$form->setType(So\Contact::TYPE_CONTACT_PERSON);
		$this->setView('create');
	}
	
	public function actionEditContact($id){
		$this->setContact($id);
		$this->setCompany($this->contact->getCompanyId());
		$form = $this->getForm();
		$form->setRecord($this->contact);
		$form->setType(So\Contact::TYPE_CONTACT_PERSON);
		$this->setView('edit');
	}

	public function actionDelete($id) {
		$this->setContact($id);
		$companyId = $this->contact->getCompanyId();
		$this->contactMan->delete($id);
		$this->flashMessage('Kontakt byl smazan.');
		$this->redirect('Company:company', $companyId);
	}

	protected function createComponentForm() {
		$form = $this->contactFormFa->create($this->company->getId());
		$form->onSave = function(Components\ContactForm $control, $id) {
			$this->flashMessage('Kontakt spolecnosti byl upraven');
			$this->restoreRequest($this->backlink);
			$this->redirect('Company:detail', $this->company->getId());
		};
		
		return $form;
	}
	
	/**
	 * 
	 * @return Components\ContactForm
	 */
	public function getForm(){
		return $this['form'];
	}

}
