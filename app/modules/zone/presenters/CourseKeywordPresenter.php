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
 * Description of CourseKeywordPresenter
 *
 * @author jakubmares
 */
class CourseKeywordPresenter extends BasePresenter {

	/** @var So\Course */
	private $course;

	/** @var Model\CourseManager */
	private $courseMan;

	/** @var Model\CourseKeywordManager */
	private $keywordMan;

	public function __construct(Model\CourseManager $courseMan, Model\CourseKeywordManager $keywordMan) {
		$this->courseMan = $courseMan;
		$this->keywordMan = $keywordMan;
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
	
	public function actionCreate($courseId){
		$this->setCourse($courseId);
	}
	public function renderCreate(){
		$company = $this->course->getCompany();
		$this->template->course = $this->course;
		$this->template->headline = 'Nové klíčové slovo';
		$this->template->breadcrumbs = [
			$company->getName() => $this->link('Company:detail', $company->getId()),
			'Kurzy' => $this->link('Company:courses', $company->getId()),
			$this->course->getName() => $this->link('Course:detail', $this->course->getId()),
			'Nové klíčové slovo' => null
		];
	}
	
	public function actionDelete($id){
		$keyword = $this->keywordMan->get($id);
		$this->setCourse($keyword->getCourseId());
		$this->keywordMan->delete($id);
		$this->flashMessage('Klíčové slovo bylo smazáno');
		$this->redirect('Course:detail',  $this->course->getId());
	}
	
	protected function createComponentForm(){
		$form = new Form();
		$form->addText(Model\CourseKeywordManager::COLUMN_KEYWORD,'* Klíčové slovo:')
				->addRule(Form::FILLED,"Klíčové slovo musí být vyplněno");
		$form->addHidden(Model\CourseKeywordManager::COLUMN_COURSE_ID)->setDefaultValue($this->course->getId());
		$form->addSubmit('send', 'Ulozit');
		$form->onSuccess[] = [$this,'formSucceeded'];
		return $form;
	}
	
	public function formSucceeded(Form $form){
		if(!$this->course){
			throw new \Nette\Application\BadRequestException();
		}
		
		$this->keywordMan->insert($form->getValues());
		$this->flashMessage('Klíčové slovo bylo uloženo');
		$this->redirect('Course:detail',  $this->course->getId());
	}

}
