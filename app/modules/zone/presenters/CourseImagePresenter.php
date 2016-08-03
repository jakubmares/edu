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
 * Description of CourseImagePresenter
 *
 * @author jakubmares
 */
class CourseImagePresenter extends BasePresenter {

	/** @var So\CourseImage */
	private $record;

	/** @var So\Course */
	private $course;

	/** @var Model\CourseImageManager */
	private $imageMan;

	/** @var Model\CourseManager */
	private $courseMan;

	public function __construct(Model\CourseImageManager $imageMan, Model\CourseManager $courseMan) {
		parent::__construct();
		$this->imageMan = $imageMan;
		$this->courseMan = $courseMan;
	}

	private function setRecord($id) {
		$this->record = $this->imageMan->get($id);
		if (!$this->record) {
			throw new \Nette\Application\BadRequestException('Obrazek nebyl nalezen');
		}
		$this->template->image = $this->record;
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

	public function actionCreate($courseId) {
		$this->setCourse($courseId);
		$this->template->headline = 'Nový obrázek';
		$this->template->course = $this->course;
		$company = $this->course->getCompany();
		$this->template->breadcrumbs = [
			$company->getName() => $this->link('Company:detail', $company->getId()),
			'Kurzy' => $this->link('Company:courses', $company->getId()),
			$this->course->getName() => $this->link('Course:images', $this->course->getId()),
			'Obrázky'=>$this->link('Course:images', $this->course->getId()),
			'Nový obrázek' => null
		];
		$this->setView('form');
	}

	public function actionEdit($id) {
		$this->setRecord($id);
		$this->setCourse($this->record->getCourseId());
		$this['form']->setDefaults($this->record);
		$this->template->headline = 'Editace obrázku';
		$this->template->course = $this->course;
		$company = $this->course->getCompany();
		$this->template->breadcrumbs = [
			$company->getName() => $this->link('Company:detail', $company->getId()),
			'Kurzy' => $this->link('Company:courses', $company->getId()),
			$this->course->getName() => $this->link('Course:detail', $this->course->getId()),
			'Obrázky'=>$this->link('Course:images', $this->course->getId()),
			'Editace obrázku' => null
		];
		$this->setView('form');
	}

	public function actionDelete($id) {
		$this->setRecord($id);
		$courseId = $this->record->getCourseId();
		$this->imageMan->delete($id);
		$this->flashMessage('Obrázek byl smazán');
		$this->redirect('Course:images', $courseId);
	}

	public function createComponentForm() {
		$form = new Form();
		$form->addUpload(Model\CourseImageManager::COLUMN_IMG, 'Obrázek:');
		$form->addCheckbox(Model\CourseImageManager::COLUMN_ACTIVE, 'Aktivní')->setDefaultValue(true);
		$form->addHidden(Model\CourseImageManager::COLUMN_COURSE_ID, $this->course->getId());
		$form->onSubmit[] = [$this, 'formSucceeded'];
		$form->addSubmit('send', 'Uložit');
		return $form;
	}

	public function formSucceeded(Form $form) {

		if ((int) $this->getParameter('id') && !$this->record) {
			throw new \Nette\Application\BadRequestException;
		}

		$values = $form->getValues();
		$image = $values->offsetGet(Model\CompanyImageManager::COLUMN_IMG);

		if ($image->isImage()) {
			$values->offsetSet(Model\CompanyImageManager::COLUMN_IMG, $this->imageMan->saveFileUpload($image));
		} else {
			$values->offsetUnset(Model\CompanyImageManager::COLUMN_IMG);
		}

		if ($this->record) {
			$this->imageMan->update($this->record->getId(), $values);
		} else {
			$this->imageMan->insert($values);
		}
		$this->flashMessage('Obrazek byl ulozen');
		$this->redirect('Course:images', $this->course->getId());
	}

}
