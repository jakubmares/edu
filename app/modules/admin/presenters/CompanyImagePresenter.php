<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace App\Module\Admin\Presenters;

use App\Model,
	App\Components,
	App\Model\CompanyImageManager,
	App\Forms\Form;

/**
 * Description of CompanyImagePresenter
 *
 * @author jakubmares
 */
class CompanyImagePresenter extends BasePresenter {

	/** @var CompanyImageManager */
	private $imageMan;

	/** @var App\Components\IImageFormFactory */
	private $imageFormFa;
	
	/** @var Model\So\CompanyImage */
	private $record;
	
	/** @var Model\So\Company */
	private $company;

	public function __construct(CompanyImageManager $imageMan, Components\IImageFormFactory $imageFormFa) {
		$this->imageMan = $imageMan;
		$this->imageFormFa = $imageFormFa;
	}
	
	private function setRecord($id) {
		$this->record = $this->imageMan->get($id);
		if (!$this->record) {
			throw new BadRequestException('Adresa nebyla nalezena');
		}
	}

	private function setCompany($id) {
		$this->company = $this->companyMan->get($id);
		if (!$this->company) {
			throw new BadRequestException('Spolecnost nebyla nalezena');
		}
	}
	
	/**
	 * 
	 * @return Components\ImageForm
	 */
	public function getForm(){
		return $this['form'];
	}

	public function actionCreate($id) {
		$this->setCompany($id);
	}
	
	public function renderCreate(){
		$this->template->company = $this->company;
	}
	

	public function actionEdit($id) {
		$this->setRecord($id);
		$this->setCompany($this->record->getCompanyId());
		$this->getForm()->setRecord($this->record);
	}
	
	public function renderEdit(){
		$this->template->company = $this->company;
	}

	public function actionDelete($id) {
		$record = $this->imageMan->getRow($id);
		$comapnyId = $record->offsetGet(CompanyImageManager::COLUMN_COMPANY_ID);
		$this->imageMan->delete($id);
		$this->flashMessage('Obrazek byl smazan');
		$this->redirect('Company:company', $comapnyId);
	}

	public function createComponentForm() {
		$form = $this->imageFormFa->create($this->company->getId());
		$form->onSave = function($control) {
			$this->flashMessage('Obrázek byl uložen');
			$this->redirect('Company:company', $this->company->getId());
		};
		return $form;
	}

}
