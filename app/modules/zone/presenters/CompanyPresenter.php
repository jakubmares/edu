<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace App\Module\Zone\Presenters;

use App\Components,
	App\Model\So,
	App\Forms\Form,
	App\Model,
	App\Model\CompanyManager,
	App\Model\CourseManager,
	App\Model\ContactManager,
	App\Model\AddressManager,
	Grido;

/**
 * Description of CompanyPresenter
 *
 * @author jakubmares
 */
class CompanyPresenter extends BasePresenter {

	/** @var CourseManager */
	private $courseMan;

	/** @var AddressManager */
	private $addressMan;

	/** @var ContactManager */
	private $contactMan;

	/** @var So\Company */
	private $company;

	/** @var Components\ICompanyFormFactory @inject */
	private $companyFormFa;

	/** @var Model\CompanyImageManager */
	private $imageMan;

	/** @var Model\CompanyVideoManager */
	private $videoMan;

	/** @var Model\LanguageManager */
	private $languageMan;

	/** @persistent */
	public $backlink = '';

	public function __construct(CourseManager $courseMan, AddressManager $addressMan, ContactManager $contactMan,
			Components\ICompanyFormFactory $companyFormFa, Model\CompanyImageManager $imageMan, Model\CompanyVideoManager $videoMan,
			Model\LanguageManager $languageMan) {
		$this->courseMan = $courseMan;
		$this->addressMan = $addressMan;
		$this->contactMan = $contactMan;
		$this->companyFormFa = $companyFormFa;
		$this->imageMan = $imageMan;
		$this->videoMan = $videoMan;
		$this->languageMan = $languageMan;
	}

	public function renderDetail($id) {
		$this->template->company = $this->companyMan->get($id);
		$this->template->contacts = $this->contactMan->getByCompanyIdAndTypes($id,[So\Contact::TYPE_ORDER,So\Contact::TYPE_QUESTION,So\Contact::TYPE_SHOW]);
		$this->template->courses = $this->courseMan->getCoursesByCompanyId($id);
		$this->template->addresses = $this->addressMan->getByCompanyId($id);
	}

	public function renderDescription($id) {
		$this->template->company = $this->companyMan->get($id);
	}

	public function actionEdit($id) {
		$this->setCompany($id);
		$form = $this->getForm();
		$form->setCompany($this->company);
	}

	public function renderEdit() {
		$this->template->company = $this->company;
	}

	public function actionEditLogo($id) {
		$this->setCompany($id);
	}

	public function actionEditDescription($id) {
		$this->setCompany($id);
		$this['descriptionForm']->setDefaults($this->company);
	}

	public function renderEditDescription() {
		$this->template->company = $this->company;
	}

	public function handleDeleteLogo($id) {
		$this->companyMan->deleteLogo($id);
		$this->flashMessage('Logo bylo smazano');
		$this->redirect('this');
	}

	public function renderEditLogo() {
		$this->template->company = $this->company;
	}

	public function renderImages($id) {
		$this->setCompany($id);
		$this->template->company = $this->company;
		$this->template->images = $this->imageMan->getByCompanyId($id);
	}

	public function renderVideos($id) {
		$this->setCompany($id);
		$this->template->company = $this->company;
		$this->template->videos = $this->videoMan->getByCompanyId($id);
	}

	public function renderCourses($id) {
		$this->setCompany($id);
		$this->template->company = $this->company;
		$this->template->courses = $this->courseMan->getCoursesByCompanyId($id);
	}

	protected function createComponentForm() {
		$control = $this->companyFormFa->create();
		$control->onSave[] = function(Components\CompanyForm $control, $id) {
			$this->flashMessage('Detail společnosti byl upraven');
			$this->redirect('detail', ['id' => $id]);
		};
		return $control;
	}

	protected function createComponentLogoForm() {
		$form = new Form();
		$form->addUpload(CompanyManager::COLUMN_LOGO);
		$form->addSubmit('send', 'Uložit');
		$form->onSuccess[] = [$this, 'logoFormSucceeded'];
		return $form;
	}

	public function logoFormSucceeded(Form $form) {
		if ((int) $this->getParameter('id') && !$this->company) {
			throw new BadRequestException;
		}
		$values = $form->getValues();
		$logo = $values->offsetGet(Model\CompanyManager::COLUMN_LOGO);

		if ($logo->isImage()) {
			$values->offsetSet(Model\CompanyManager::COLUMN_LOGO, $this->companyMan->saveFileUpload($logo));
			$row = $this->companyMan->getRow($this->company->getId());
			$row->update($values);
			$this->flashMessage('Logo bylo uloženo');
		} else {
			$this->flashMessage('Logo nelze uložit');
		}

		$this->restoreRequest($this->backlink);
		$this->redirect('detail', $this->company->getId());
	}

	public function createComponentGridCourses($name) {
		$id = $this->getParameter('id');
		$grid = new Grido\Grid($this, $name);
		$grid->setModel($this->courseMan->getSelectionByCompanyId($id));
		$grid->filterRenderType = Grido\Components\Filters\Filter::RENDER_INNER;
		$grid->translator->lang = 'cs';
		$grid->defaultPerPage = 20;

		$grid->addColumnText(CourseManager::COLUMN_NAME, 'Název')
				->setSortable()
				->setFilterText(CourseManager::COLUMN_NAME, 'Název');
		$grid->addColumnText('language.name', 'Jazyk');


		$grid->addColumnText(CourseManager::COLUMN_ACTIVE, 'Aktivní')->setCustomRender(function($item) {
			$ret = \Nette\Utils\Html::el('span');
			$ret->addAttributes(['aria-hidden' => "true"]);
			if ($item->offsetGet(CourseManager::COLUMN_ACTIVE)) {
				$ret->addAttributes(['class' => 'glyphicon glyphicon-ok']);
			} else {
				$ret->addAttributes(['class' => 'glyphicon glyphicon-remove']);
			}
			return $ret;
		});
		$list = ['all' => 'Vše', 'active' => 'Aktivní', 'nonactive' => 'Neaktivní'];
		$grid->addFilterSelect(CourseManager::COLUMN_ACTIVE, 'Pouze aktivní', $list)
				->setCondition([
					'all' => [CourseManager::COLUMN_ID, '<>', 0],
					'active' => [CourseManager::COLUMN_ACTIVE, '= ?', true],
					'nonactive' => [CourseManager::COLUMN_ACTIVE, '= ?', false],
		]);

		$link = \Nette\Utils\Html::el('a');
		$link->addAttributes(['style' => 'margin-right: 10px']);
		$editIco = \Nette\Utils\Html::el('span');
		$editIco->addAttributes(['class' => "glyphicon glyphicon-edit", 'aria-hidden' => "true"]);
		$link->add($editIco);
		$link->addText(' Editovat');
		$dLink = \Nette\Utils\Html::el('a');
		$detIco = \Nette\Utils\Html::el('span');
		$detIco->addAttributes(['class' => "glyphicon glyphicon-new-window", 'aria-hidden' => "true"]);
		$dLink->add($detIco);
		$dLink->addText(' Detail');
		$grid->addActionHref('edit', 'Edit', 'Course:edit', ['backlink' => $this->storeRequest()])->setElementPrototype($link);
		$grid->addActionHref('detail', 'Detail', 'Course:detail')->setElementPrototype($dLink);

		return $grid;
	}

	public function createComponentDescriptionForm() {
		$form = new Form();
		$form->addTextArea(Model\CompanyManager::COLUMN_DESCRIPTION, '* Popis:')
				->addRule(Form::FILLED,"Pole %label musí být vyplněno")
				->setAttribute('class', 'mceEditor');
		$form->addSubmit('send', 'Uložit');
		$form->onSuccess[] = [$this, 'descriptionFormSucceeded'];
		return $form;
	}

	public function descriptionFormSucceeded(Form $form) {
		if ((int) $this->getParameter('id') && !$this->company) {
			throw new BadRequestException;
		}

		$row = $this->companyMan->getRow($this->company->getId());
		$row->update($form->getValues());
		$this->flashMessage('Popis byl uložen');
		$this->restoreRequest($this->backlink);
		$this->redirect('description', $this->company->getId());
	}

	/**
	 * 
	 * @return Components\CompanyForm
	 */
	private function getForm() {
		return $this['form'];
	}

	private function setCompany($id) {
		if (!$this->companyMan->isAllow($id, $this->user->getId()) && !$this->user->isInRole(Model\RoleManager::ROLE_ADMIN)) {
			$this->flashMessage('K této operaci nemáte oprávnění');
			$this->redirect(':Front:Homepage:default');
		}
		$this->company = $this->companyMan->get($id);
		if (!$this->company) {
			throw new BadRequestException('Spoločnost nebyla nalezena');
		}
	}

}
