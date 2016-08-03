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
 * Description of FocusPresenter
 *
 * @author jakubmares
 */
class CourseFocusPresenter extends BasePresenter {

	/** @var So\Focus */
	private $record;

	/** @var So\Course */
	private $course;

	/** @var Model\CategoryManager */
	private $categoryMan;

	/** @var Model\FocusManager */
	private $focusMan;

	/** @var Model\CourseFocusManager */
	private $courseFocusMan;

	/** @var Model\CourseManager */
	private $courseMan;

	public function __construct(Model\CategoryManager $categoryMan, Model\FocusManager $focusMan, Model\CourseFocusManager $courseFocusMan,
			Model\CourseManager $courseMan) {
		$this->categoryMan = $categoryMan;
		$this->focusMan = $focusMan;
		$this->courseFocusMan = $courseFocusMan;
		$this->courseMan = $courseMan;
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

	public function actionEdit($id) {
		$this->setCourse($id);
		$this->template->headline = 'Zaměření kurzu';
		$this->template->course = $this->course;
		$company = $this->course->getCompany();
		$this->template->breadcrumbs = [
			$company->getName() => $this->link('Company:detail', $company->getId()),
			'Kurzy' => $this->link('Company:courses', $company->getId()),
			$this->course->getName() => $this->link('Course:detail', $this->course->getId()),
			'Zaměření kurzu' => null
		];
		$this->setView('form');
	}

	public function createComponentForm() {

		$form = new Form();
		foreach ($this->categoryMan->getActive() as $category) {
			$form->addGroup($category->getName());

			foreach ($category->getActiveFocuses() as $focus) {
				$checked = in_array($focus->getId(), $this->course->getFocusesId());
				$form->addCheckbox($focus->getId(), $focus->getName())->setDefaultValue($checked);
			}
		}
		$form->addGroup('Uložit');
		$form->onValidate[] = [$this, 'validateForm'];
		$form->onSuccess[] = [$this, 'formSucceeded'];
		$form->addSubmit('send', 'Uložit');
		return $form;
	}

	public function validateForm(Form $form) {

		$chacked = 0;

		foreach ($form->getValues() as $value) {
			if ($value) {
				$chacked++;
			}
		}

		if ($chacked > Model\CourseFocusManager::FOCUSES_PER_COURSE) {
			$form->addError('Maximalne je mozne ke kurzu priradit ke ' . Model\CourseFocusManager::FOCUSES_PER_COURSE . ' zamereni.');
		}
	}

	public function formSucceeded(Form $form) {


		if (!$this->course) {
			throw new \Nette\Application\BadRequestException;
		}
		$data = [];
		foreach ($form->getValues() as $focusId => $checked) {
			if ($checked) {
				$data[] = [
					Model\CourseFocusManager::COLUMN_COURSE_ID => $this->course->getId(),
					Model\CourseFocusManager::COLUMN_FOCUS_ID => $focusId
				];
			}
		}
		$this->courseFocusMan->deleteByCourseId($this->course->getId());
		$this->courseFocusMan->insert($data);
		$this->flashMessage('Zamereni bylo nastaveno');
		$this->redirect('Course:detail', $this->course->getId());
	}

}
