<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace App\Module\Zone\Presenters;

use App\Forms\Form,
	App\Model,
	App\Model\So;

/**
 * Description of CoursePresenter
 *
 * @author jakubmares
 */
class CoursePresenter extends BasePresenter {

	/** @var Model\LanguageManager */
	private $languageMan;

	/** @var Model\CourseManager */
	private $courseMan;

	/** @var Model\TermManager */
	private $termMan;

	/** @var Model\CategoryManager */
	private $categoryMan;

	/** @var Model\FocusManager */
	private $focusMan;

	/** @var Model\LevelManager */
	private $levelMan;

	/** @var Model\CourseVideoManager */
	private $videoMan;

	/** @var Model\CourseImageManager */
	private $imageMan;

	/** @var So\Company */
	private $company;

	/** @var So\Course */
	private $record;

	/** @persistent */
	public $backlink = '';

	public function __construct(Model\LanguageManager $languageMan, Model\CourseManager $courseMan, Model\TermManager $termMan,
			Model\CategoryManager $categoryMan, Model\FocusManager $focusMan, Model\LevelManager $levelMan, Model\CourseVideoManager $videoMan,
			Model\CourseImageManager $imageMan) {
		$this->languageMan = $languageMan;
		$this->courseMan = $courseMan;
		$this->termMan = $termMan;
		$this->categoryMan = $categoryMan;
		$this->focusMan = $focusMan;
		$this->levelMan = $levelMan;
		$this->videoMan = $videoMan;
		$this->imageMan = $imageMan;
	}

	private function setRecord($id) {
		$this->record = $this->courseMan->get($id);
		if(!$this->companyMan->isAllow($this->record->getCompanyId(),  $this->user->getId()) && !$this->user->isInRole(Model\RoleManager::ROLE_ADMIN)){
			$this->flashMessage('K této operaci nemáte oprávnění');
			$this->redirect(':Front:Homepage:default');
		}
		if (!$this->record) {
			throw new BadRequestException('Kurz nebyl nalezen');
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
	 * @return Form
	 */
	private function getForm() {
		return $this['form'];
	}

	public function actionDetail($id) {
		$this->setRecord($id);
		$this->setCompany($this->record->getCompanyId());
	}

	public function renderDetail() {
		$this->template->course = $this->record;
		$this->template->company = $this->company;
		$catFoc = [];
		foreach ($this->focusMan->getFocusesByCourseId($this->record->getId()) as $focus) {
			$catFoc[$focus->getCategoryId()][] = $focus;
		}
		$this->template->catFoc = $catFoc;
		$this->template->levels = $this->levelMan->getByCourseId($this->record->getId());
		$this->template->breadcrumbs = [
			$this->company->getName() => $this->link('Company:detail', $this->company->getId()),
			'Kurzy' => $this->link('Company:courses', $this->company->getId()),
			$this->record->getName() => null
		];
	}

	public function actionEdit($id) {
		$this->setRecord($id);
		$this->setCompany($this->record->getCompanyId());
		$form = $this->getForm();
		$values = $this->record->toArray();
		$values['levelsId'] = $this->record->getLevelsId();
		$form->setDefaults($values);
	}

	public function renderEdit() {
		$this->template->headline = 'Editace kurzu';
		$this->template->breadcrumbs = [
			$this->company->getName() => $this->link('Company:detail', $this->company->getId()),
			'Kurzy' => $this->link('Company:courses', $this->company->getId()),
			$this->record->getName() => $this->link('Course:detail', $this->record->getId()),
			'Editace kurzu' => null
		];
	}

	public function actionCreate($companyId) {
		$this->setCompany($companyId);
		$this->getForm()->setDefaults([Model\CourseManager::COLUMN_COMPANY_ID => $companyId]);
	}

	public function renderCreate() {
		$this->template->headline = 'Nový kurz';
		$this->template->breadcrumbs = [
			$this->company->getName() => $this->link('Company:detail', $this->company->getId()),
			'Kurzy' => $this->link('Company:courses', $this->company->getId()),
			'Nový kurz' => null
		];
	}

	public function actionTerms($id) {
		$this->setRecord($id);
		$this->setCompany($this->record->getCompanyId());
	}

	public function renderTerms() {
		$this->template->course = $this->record;
		$this->template->company = $this->company;
		$this->template->terms = $this->termMan->getTermsByCourseId($this->record->getId());
		$this->template->breadcrumbs = [
			$this->company->getName() => $this->link('Company:detail', $this->company->getId()),
			'Kurzy' => $this->link('Company:courses', $this->company->getId()),
			$this->record->getName() => $this->link('Course:detail', $this->record->getId()),
			'Termíny kurzu' => null
		];
	}

	public function actionImages($id) {
		$this->setRecord($id);
		$this->setCompany($this->record->getCompanyId());
	}

	public function renderImages() {
		$this->template->course = $this->record;
		$this->template->company = $this->company;
		$this->template->images = $this->imageMan->getByCourseId($this->record->getId());
		$this->template->breadcrumbs = [
			$this->company->getName() => $this->link('Company:detail', $this->company->getId()),
			'Kurzy' => $this->link('Company:courses', $this->company->getId()),
			$this->record->getName() => $this->link('Course:detail', $this->record->getId()),
			'Obrázky kurzu' => null
		];
	}

	public function actionVideos($id) {
		$this->setRecord($id);
		$this->setCompany($this->record->getCompanyId());
	}

	public function renderVideos() {
		$this->template->course = $this->record;
		$this->template->company = $this->company;
		$this->template->videos = $this->videoMan->getActiveByCourseId($this->record->getId());
		$this->template->breadcrumbs = [
			$this->company->getName() => $this->link('Company:detail', $this->company->getId()),
			'Kurzy' => $this->link('Company:courses', $this->company->getId()),
			$this->record->getName() => $this->link('Course:detail', $this->record->getId()),
			'Videa kurzu' => null
		];
	}

	public function actionEditDescription($id) {
		$this->setRecord($id);
		$this->setCompany($this->record->getCompanyId());
	}

	public function renderEditDescription() {
		$this->template->headline = 'Editace popisu kurzu';
		$this->template->breadcrumbs = [
			$this->company->getName() => $this->link('Company:detail', $this->company->getId()),
			'Kurzy' => $this->link('Company:courses', $this->company->getId()),
			$this->record->getName() => $this->link('Course:detail', $this->record->getId()),
			'Editace popisu kurzu' => null
		];
	}

	public function createComponentDescriptionForm() {
		$form = new Form();
		$form->addTextArea(Model\CourseManager::COLUMN_DESCRIPTION, '* Popis:')
				->addRule(Form::FILLED,"Pole %label musí být vyplněno")
				->setAttribute('class', 'mceEditorBasic')
				->setDefaultValue($this->record->getDescription());
		$form->addSubmit('send', 'Ulozit');
		$form->onSuccess[] = [$this, 'descriptionFormSucceeded'];
		return $form;
	}

	protected function createComponentForm() {
		$form = new Form();
		$form->addText(Model\CourseManager::COLUMN_NAME, '* Název:')
				->addRule(Form::FILLED, 'Pole %label musi byt vyplneno');
		$form->addTextArea(Model\CourseManager::COLUMN_DESCRIPTION, '* Popis:')
				->addRule(Form::FILLED,"Pole Popis musí být vyplněno")
				->setAttribute('class', 'mceEditorBasic');
		$form->addCheckbox(Model\CourseManager::COLUMN_RETRAINING, 'Rekvalifikační kurz');
		$form->addHidden(Model\CourseManager::COLUMN_COMPANY_ID);
		$form->addSelect(Model\CourseManager::COLUMN_LANGUAGE_ID, 'Jazyk:',
				\Extensions\ListHelper::fetchPair($this->languageMan->getAll(), 'id', 'name'));
		$form->addCheckboxList('levelsId', 'Úroveň znalostí:', \Extensions\ListHelper::fetchPair($this->levelMan->getAll(), 'id', 'name'));
		$form->addText(Model\CourseManager::COLUMN_LINK_URL, 'Url kurzu na stránkaách poskytovatele:')->setAttribute("placeholder","http://");
		$form->addSubmit('send', 'Uložit');
		$form->onSuccess[] = [$this, 'formSucceeded'];
		return $form;
	}

	public function descriptionFormSucceeded(Form $form) {
		if (!$this->record) {
			throw new \Nette\Application\BadRequestException;
		}

		$this->courseMan->update($this->record->getId(), $form->getValues());

		$this->flashMessage('Popis byl ulozen');
		$this->redirect('Course:detail', $this->record->getId());
	}

	public function formSucceeded(Form $form) {
		if ((int) $this->getParameter('id') && !$this->record) {
			throw new \Nette\Application\BadRequestException;
		}

		$values = $form->getValues();
		$levelsId = $values->offsetGet('levelsId');
		$values->offsetUnset('levelsId');

		if ($this->record) {
			$this->courseMan->updateWithLevels($this->record->getId(), $values, $levelsId);
			$id = $this->record->getId();
		} else {
			$row = $this->courseMan->insertWithLevels($values, $levelsId);
			$id = $row->offsetGet(Model\CourseManager::COLUMN_ID);
		}
		$this->flashMessage('Kurz byl ulozen');
		$this->restoreRequest($this->backlink);
		$this->redirect('Course:detail', $id);
	}

}
