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
 * Description of CourseVideoPresenter
 *
 * @author jakubmares
 */
class CourseVideoPresenter extends BasePresenter {

	/** @var So\CourseVideo */
	private $record;

	/** @var So\Course */
	private $course;

	/** @var Model\CourseVideoManager */
	private $videoMan;

	/** @var Model\CourseManager */
	private $courseMan;

	public function __construct(Model\CourseVideoManager $videoMan, Model\CourseManager $courseMan) {
		$this->videoMan = $videoMan;
		$this->courseMan = $courseMan;
	}

	private function setRecord($id) {
		$this->record = $this->videoMan->get($id);
		if (!$this->record) {
			throw new \Nette\Application\BadRequestException('Video nebylo nalezeno');
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

	public function actionCreate($courseId) {
		$this->setCourse($courseId);
		$this->template->headline = 'Nové video';
		$this->template->course = $this->course;
		$company = $this->course->getCompany();
		$this->template->breadcrumbs = [
			$company->getName() => $this->link('Company:detail', $company->getId()),
			'Kurzy' => $this->link('Company:courses', $company->getId()),
			$this->course->getName() => $this->link('Course:detail', $this->course->getId()),
			'Obrázky'=>$this->link('Course:videos', $this->course->getId()),
			'Nové video' => null
		];
		$this->setView('form');
	}

	public function actionEdit($id) {
		$this->setRecord($id);
		$this->setCourse($this->record->getCourseId());
		$data = $this->record->toArray();
		$data[Model\CourseVideoManager::COLUMN_VIDEO] = 'http://youtu.be/'.$this->record->getVideo();
		$this['form']->setDefaults($data);
		$this->template->headline = 'Editace videa';
		$this->template->course = $this->course;
		$company = $this->course->getCompany();
		$this->template->breadcrumbs = [
			$company->getName() => $this->link('Company:detail', $company->getId()),
			'Kurzy' => $this->link('Company:courses', $company->getId()),
			$this->course->getName() => $this->link('Course:detail', $this->course->getId()),
			'Obrázky'=>$this->link('Course:videos', $this->course->getId()),
			'Editace videa' => null
		];
		$this->setView('form');
	}

	public function actionDelete($id) {
		$this->setRecord($id);
		$courseId = $this->record->getCourseId();
		$this->videoMan->delete($id);
		$this->flashMessage('Video bylo smazáno');
		$this->redirect('Course:videos', $courseId);
	}

	public function createComponentForm() {
		$form = new Form();
		$form->addText(Model\CourseVideoManager::COLUMN_VIDEO, 'Url videa youtube:')
				->setAttribute("placeholder","https://www.youtube.com/watch?v=BYOu7N8e9PU");
		$form->addCheckbox(Model\CourseVideoManager::COLUMN_ACTIVE, 'Aktivní:')->setDefaultValue(true);
		$form->addHidden(Model\CourseVideoManager::COLUMN_COURSE_ID, $this->course->getId());
		$form->onSubmit[] = [$this, 'formSucceeded'];
		$form->addSubmit('send', 'Ulozit');
		return $form;
	}

	public function formSucceeded(Form $form) {

		if ((int) $this->getParameter('id') && !$this->record) {
			throw new \Nette\Application\BadRequestException;
		}

		if ($this->record) {
			$this->videoMan->update($this->record->getId(),  $form->getValues());
		} else {
			$this->videoMan->insertVideo( $form->getValues());
		}
		$this->flashMessage('Video bylo ulozeno');
		$this->redirect('Course:videos', $this->course->getId());
	}

}
