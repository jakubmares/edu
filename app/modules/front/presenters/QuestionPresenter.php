<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace App\Module\Front\Presenters;

use App\Model,
	App\Model\So,
	App\Forms\Form,
	Nette\Mail;

/**
 * Description of QuestionPrasenter
 *
 * @author jakubmares
 */
class QuestionPresenter extends BasePresenter {

	/** @var So\Course */
	private $course;

	/** @var So\Focus */
	private $focus;

	/** @var So\Category */
	private $category;

	/** @var Model\QuestionManager */
	private $questionMan;

	/** @var Model\ContactManager */
	private $contactMan;

	public function __construct(Model\QuestionManager $questionMan, Model\ContactManager $contactMan) {
		$this->questionMan = $questionMan;
		$this->contactMan = $contactMan;
	}

	public function actionQuestion($seokey) {
		$this->course = $this->courseMan->getCourseBySeokey($seokey);
		$this->focus = $this->focusMan->getFocusByCourseId($this->course->getId());
		$this->category = $this->focus->getCategory();
	}

	public function renderQuestion() {

		$this->template->course = $this->course;
		$this->template->breadcrumbs = [
			'Úvod' => $this->link('Homepage:default'),
			$this->category->name => $this->link("Course:category", $this->category->getSeokey()),
			$this->focus->name => $this->link("Course:focus", $this->focus->getSeokey(), $this->focus->getSeokey()),
			$this->course->getName() => $this->link("Course:course", $this->course->getSeokey()),
			'Dotaz na kurz' => null
		];
	}

	public function createComponentForm() {
		$form = new Form();
		$form->addText(Model\QuestionManager::COLUMN_NAME, '* Vaše jméno:')
				->addRule(Form::FILLED);
		$form->addText(Model\QuestionManager::COLUMN_EMAIL, '* Váš e-mail:')
				->addRule(Form::FILLED)
				->addRule(Form::EMAIL);
		$form->addTextArea(Model\QuestionManager::COLUMN_QUESTION, '* Text dotazu:')
				->addRule(Form::FILLED);
		$form->addSubmit('send', 'ODESLAT');
		$form->onSuccess[] = [$this, 'formSucceeded'];
		return $form;
	}

	public function formSucceeded(Form $form) {
		if (!$this->course) {
			throw new \Nette\Application\BadRequestException();
		}
		$company = $this->course->getCompany();
		$contact = $this->contactMan->getContactQuestionByCompanyId($company->getId());

		$values = $form->getValues();
		$values->offsetSet(Model\QuestionManager::COLUMN_COMPANY_NAME, $company->getName());
		$values->offsetSet(Model\QuestionManager::COLUMN_COURSE_NAME, $this->course->getName());
		$values->offsetSet(Model\QuestionManager::COLUMN_COURSE_ID, $this->course->getId());
		$values->offsetSet(Model\QuestionManager::COLUMN_CREATED_AT, new \Nette\Utils\DateTime());
		$values->offsetSet(Model\QuestionManager::COLUMN_SENT_TO, $contact->getEmail());

		$template = $this->createTemplate();
		$template->setFile(dirname(__FILE__) . '/templates/mail/question.latte');
		$template->question = $values;
		$template->course = $this->course;
		$template->mailto = \App\Presenters\BasePresenter::EMAIL_INFO;
		$email = new Mail\Message;
		$email->setFrom(\App\Presenters\BasePresenter::EMAIL_NOREPLY,'evzdelavani.cz')
				->setSubject('evzdelavani.cz - dotaz na kurz')
				->setHtmlBody($template)
				->addTo($contact->getEmail());
		$this->questionMan->insertAndSendEmail($values,$email);
		
		$this->flashMessage("Váš dotaz byl odeslán.");
		$this->redirect('Course:course', $this->course->getSeokey());
	}



}
