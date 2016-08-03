<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace App\Module\Zone\Presenters;

use App\Model,
	App\Model\So,
	App\Forms\Form;

/**
 * Description of TermPresenter
 *
 * @author jakubmares
 */
class TermPresenter extends BasePresenter {

	/** @var Model\So\Term */
	private $record;

	/** @var Model\So\Course */
	private $course;

	/** @var Model\TermManager */
	private $termMan;

	/** @var Model\CourseManager */
	private $courseMan;

	/** @var Model\CurrencyManager */
	private $currencyMan;

	/** @var Model\CountryManager */
	private $countryMan;
	
	/** @persistent */
    public $backlink = '';

	public function __construct(Model\TermManager $termMan, Model\CourseManager $courseMan, Model\CurrencyManager $currencyMan,
			Model\CountryManager $countryMan) {
		$this->termMan = $termMan;
		$this->courseMan = $courseMan;
		$this->currencyMan = $currencyMan;
		$this->countryMan = $countryMan;
	}

	private function setRecord($id) {
		$this->record = $this->termMan->get($id);
		if (!$this->record) {
			throw new \Nette\Application\BadRequestException('Termin nebyl nalezen');
		}
	}

	private function setCourse($id) {
		$this->course = $this->courseMan->get($id);
		if(!$this->companyMan->isAllow($this->course->getCompanyId(),  $this->user->getId()) && !$this->user->isInRole(Model\RoleManager::ROLE_ADMIN)){
			$this->flashMessage('K této operaci nemáte oprávnění');
			$this->redirect(':Front:Homepage:default');
		}
		if (!$this->course) {
			throw new \Nette\Application\BadRequestException('Kurz nebyl nalezen');
		}
	}

	public function actionDetail($id) {
		$this->setRecord($id);
		$this->setCourse($this->record->getCourseId());
	}

	public function renderDetail() {
		$this->backlink = $this->storeRequest();
		$this->template->course = $this->course;
		$this->template->term = $this->record;
		$company = $this->course->getCompany();
		$this->template->breadcrumbs = [
			$company->getName() => $this->link('Company:detail', $company->getId()),
			'Kurzy' => $this->link('Company:courses', $company->getId()),
			$this->course->getName() => $this->link('Course:detail', $this->course->getId()),
			'Termíny kurzu' => $this->link('Course:terms', $this->course->getId()),
			'Termín' => null
		];
	}

	public function actionCreate($courseId) {
		$this->setCourse($courseId);
		$this['form']->setDefaults([Model\TermManager::COLUMN_COURSE_ID => $courseId]);
	}

	public function renderCreate() {
		$this->template->course = $this->record;
		$this->template->headline = 'Nový termínu';
		$company = $this->course->getCompany();
		$this->template->breadcrumbs = [
			$company->getName() => $this->link('Company:detail', $company->getId()),
			'Kurzy' => $this->link('Company:courses', $company->getId()),
			$this->course->getName() => $this->link('Course:detail', $this->course->getId()),
			'Termíny kurzu' => $this->link('Course:terms', $this->course->getId()),
			'Nový termín' => null
		];
	}

	public function actionCopy($copyId) {
		$this->setRecord($copyId);
		$this->setCourse($this->record->getCourseId());
		$this['form']->setDefaults($this->record);
		$this->template->headline = 'Kopie termínu';
		$company = $this->course->getCompany();
		$this->template->breadcrumbs = [
			$company->getName() => $this->link('Company:detail', $company->getId()),
			'Kurzy' => $this->link('Company:courses', $company->getId()),
			$this->course->getName() => $this->link('Course:detail', $this->course->getId()),
			'Termíny kurzu' => $this->link('Course:terms', $this->course->getId()),
			'Termín' => $this->link('Term:detail',$this->record->getId()),
			'Kopie termínu' => null
		];
		$this->setView('edit');
	}

	public function actionEdit($id) {
		$this->setRecord($id);
		$this['form']->setDefaults($this->record);
		$this->setCourse($this->record->getCourseId());
		$this->template->action = 'editace terminu';
		$this->template->term = $this->record;
		$this->template->course = $this->course;
		$this->template->headline = 'Editace termínu';
		$company = $this->course->getCompany();
		$this->template->breadcrumbs = [
			$company->getName() => $this->link('Company:detail', $company->getId()),
			'Kurzy' => $this->link('Company:courses', $company->getId()),
			$this->course->getName() => $this->link('Course:detail', $this->course->getId()),
			'Termíny kurzu' => $this->link('Course:terms', $this->course->getId()),
			'Termín' => $this->link('Term:detail',$this->record->getId()),
			'Editace termínu' => null
		];
		$this->setView('edit');
	}


	public function actionDelete($id) {
		$this->setRecord($id);
		$this->setCourse($this->record->getCourseId());
		$this->termMan->delete($id);
		$this->flashMessage('Termin byl smazan.');
		$this->restoreRequest($this->backlink);
		$this->redirect('Course:terms', $this->course->getId());
	}

	public function createComponentForm() {
		$form = new Form();

//		$form->addGroup('Termín');
		$form->addHidden(Model\TermManager::COLUMN_COURSE_ID);

		$form->addDate(Model\TermManager::COLUMN_FROM, 'Začátek:');
		$form->addDate(Model\TermManager::COLUMN_TO, 'Konec:');
		$form->addCheckbox(Model\TermManager::COLUMN_NOTERM, 'Bez termínu');
		$form->addCheckbox(Model\TermManager::COLUMN_ACTIVE, 'Aktivní')->setDefaultValue(true);
//		$form->addGroup('Cena');
		$form->addSelect(Model\TermManager::COLUMN_PRICE_FLAG, 'Typ ceny:', So\Term::flagsPrice());
		$form->addText(Model\TermManager::COLUMN_PRICE, 'Cena:')->setType('number');
		$form->addText(Model\TermManager::COLUMN_VAT, 'Daň [%]:')->setType('number');
		$form->addText(Model\TermManager::COLUMN_DISCOUNT, 'Sleva:')->setType('number');
		$form->addSelect(Model\TermManager::COLUMN_CURRENCY, 'Měna:', $this->createList($this->currencyMan->getAll(), 'currency', 'currency'));
//		$form->addGroup('Adresa');
		$form->addSelect(Model\TermManager::COLUMN_ADDRESS_FLAG, 'Typ kurzu:', So\Term::flagsAddress());
		$form->addText(Model\TermManager::COLUMN_CITY, 'Město:');
		$form->addText(Model\TermManager::COLUMN_STREET, 'Ulice:');
		$form->addText(Model\TermManager::COLUMN_REGISTRY_NUMBER, 'Číslo popisné:');
		$form->addText(Model\TermManager::COLUMN_HOUSE_NUMBER, 'Číslo orientační:');
		$form->addText(Model\TermManager::COLUMN_ZIP, 'PSČ:');
		$form->addSelect(Model\TermManager::COLUMN_COUNTRY_KEY, 'Stát:',
						\Extensions\ListHelper::fetchPair($this->countryMan->getAll(), 'key', 'country'))
				->setDefaultValue($this->countryMan->getDefaultCountryKey());
		$form->addText(Model\TermManager::COLUMN_ADDRESS_NOTE, 'Poznámka k adrese:')->setAttribute("placeholder","Upřesňující poznámka (např.: kurz se koná v prvním patře)");
//		$form->addGroup('Lektor');
		$form->addText(Model\TermManager::COLUMN_LECTOR_DEGREES_BEFORE, 'Tituly před jménem:');
		$form->addText(Model\TermManager::COLUMN_LECTOR_FIRSTNAME, 'Jméno lektora:');
		$form->addText(Model\TermManager::COLUMN_LECTOR_SURNAME, 'Příjmení lektora:');
		$form->addText(Model\TermManager::COLUMN_LECTOR_DEGREES_AFTER, 'Tituly za jménem:');
		$form->addTextArea(Model\TermManager::COLUMN_LECTOR_DESCRIPTION, 'Popis lektora:')
				->setAttribute('class', 'mceEditorBasic');
		$form->addTextArea(Model\TermManager::COLUMN_LECTOR_SKILLS, 'Dovednosti lektora:')
				->setAttribute('class', 'mceEditorBasic');
		$form->addUpload(Model\TermManager::COLUMN_LECTOR_IMAGE, 'Obrázek lektora:');

		$form->addSubmit('save', 'Uložit');
		$form->onSuccess[] = [$this, 'formSucceeded'];
		return $form;
	}

	public function formSucceeded(Form $form) {

		if ((int) $this->getParameter('id') && !$this->record) {
			throw new \Nette\Application\BadRequestException;
		}

		$values = $form->getValues();

		$logo = $values->offsetGet(Model\TermManager::COLUMN_LECTOR_IMAGE);

		if ($logo->isImage()) {
			$values->offsetSet(Model\TermManager::COLUMN_LECTOR_IMAGE, $this->termMan->saveFileUpload($logo));
		} else {
			$values->offsetUnset(Model\TermManager::COLUMN_LECTOR_IMAGE);
		}
		
		if($this->getParameter('copyId') && $this->record) {
			$this->termMan->copy($this->record->getId(), $values);
		} elseif ($this->getParameter('id') && $this->record) {
			$this->termMan->update($this->record->getId(), $values);
		} else {
			$this->termMan->insert($values);
		}
		$this->flashMessage('Termin byl ulozen.');
		$this->restoreRequest($this->backlink);
		$this->redirect('Course:terms', $this->course->getId());
	}

}
