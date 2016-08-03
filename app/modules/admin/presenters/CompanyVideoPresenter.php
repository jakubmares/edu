<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace App\Module\Admin\Presenters;

use App\Model\CompanyVideoManager,
	App\Forms\Form,
	App\Model\So,
	App\Components;

/**
 * Description of CompanyVideoPresenter
 *
 * @author jakubmares
 */
class CompanyVideoPresenter extends BasePresenter {

	/** @var CompanyVideoManager */
	private $videoMan;

	/** @var Components\IVideoFormFactory */
	private $videoFormFa;

	/** @var So\CompanyVideo */
	private $record;

	/** @var So\Company */
	private $company;

	public function __construct(CompanyVideoManager $videoMan, Components\IVideoFormFactory $videoFormFa) {
		parent::__construct();
		$this->videoMan = $videoMan;
		$this->videoFormFa = $videoFormFa;
	}

	private function setRecord($id) {
		$this->record = $this->videoMan->get($id);
		if (!$this->record) {
			throw new BadRequestException('Video nebylo nalezeno');
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
	 * @return Components\VideoForm
	 */
	public function getForm() {
		return $this['form'];
	}

	public function actionCreate($id) {
		$this->setCompany($id);
	}

	public function renderCreate() {
		$this->template->company = $this->company;
	}

	public function actionDelete($id) {
		$this->setRecord($id);
		$companyId = $this->record->getCompanyId();
		$this->videoMan->delete($id);
		$this->flashMessage('Obrazek byl smazan');
		$this->redirect('Company:company', $companyId);
	}

	public function actionEdit($id) {
		$this->setRecord($id);
		$this->setCompany($this->record->getCompanyId());
		$this->getForm()->setRecord($this->record);
	}

	public function renderEdit() {
		$this->template->company = $this->company;
	}

	protected function createComponentForm() {
		$form = $this->videoFormFa->create($this->company->getId());
		$form->onSave = function($control) {
			$this->flashMessage('Video bylo ulozeno');
			$this->redirect('Company:company', $this->company->getId());
		};
		return $form;
	}

}
