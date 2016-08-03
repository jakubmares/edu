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
 * Description of OrderPresenter
 *
 * @author jakubmares
 */
class OrderPresenter extends BasePresenter {

	/** @var So\Term */
	private $term;

	/** @var So\Course */
	private $course;

	/** @var So\Focus */
	private $focus;

	/** @var So\Category */
	private $category;

	/** @var Model\OrderManager */
	private $orderMan;

	/** @var Model\ContactManager */
	private $contactMan;

	/** @var Model\TermManager */
	private $termMan;

	public function __construct(Model\OrderManager $orderMan, Model\ContactManager $contactMan, Model\TermManager $termMan) {
		$this->orderMan = $orderMan;
		$this->contactMan = $contactMan;
		$this->termMan = $termMan;
	}

	public function actionOrder($id) {
		$this->term = $this->termMan->getActive($id);
		$this->course = $this->term->getCourse();
		if (!$this->term) {
			throw new \Nette\Application\BadRequestException();
		}
		
		$now = new \Nette\Utils\DateTime();
		
		if (($this->term->getFrom() < $now) || !$this->term->getActive() || !$this->course->getActive()) {
			$this->flashMessage('Tento termín není možno objednat.');
			$this->redirect('Course:course', $this->term->course->getSeokey());
		}
		$this->course = $this->term->getCourse();
		$this->focus = $this->focusMan->getFocusByCourseId($this->course->getId());
		$this->category = $this->focus->getCategory();
	}

	public function renderOrder() {

		$this->template->course = $this->course;
		$this->template->term = $this->term;
		$this->template->breadcrumbs = [
			'Úvod' => $this->link('Homepage:default'),
			$this->category->name => $this->link("Course:category", $this->category->getSeokey()),
			$this->focus->name => $this->link("Course:focus", $this->focus->getSeokey(), $this->focus->getSeokey()),
			$this->course->getName() => $this->link("Course:course", $this->course->getSeokey()),
			'Objednávka termínu' => null
		];
	}

	protected function createComponentForm() {
		$form = new Form();
		$form->addText(Model\OrderManager::COLUMN_NAME, '* Jméno:')
				->addRule(Form::FILLED, 'Zadejte vaše jméno!');

		$form->addText(Model\OrderManager::COLUMN_EMAIL, '* e-mail:')
				->setAttribute('placeholder', '@')
				->addRule(Form::FILLED, 'Zadejte e-mail!')
				->addRule(Form::EMAIL, 'Neplatný formát emailu!');

		$form->addText(Model\OrderManager::COLUMN_PHONE, '* telefon:');

		$form[Model\OrderManager::COLUMN_PHONE]
				->addConditionOn($form[Model\OrderManager::COLUMN_EMAIL], Form::BLANK)
				->addRule(Form::FILLED, 'Zadejte telefon!');

		$form->addTextArea(Model\OrderManager::COLUMN_BILLING_INFO, '* Fakturační adresa, IČO:')
				
				->addConditionOn($form[Model\OrderManager::COLUMN_EMAIL], Form::FILLED)
				->addRule(Form::FILLED, 'Zadejte adresu!');

		$form->addText(Model\OrderManager::COLUMN_MEMBER_COUNT, 'Počet účastníků:')
				->setDefaultValue(1)
				->setAttribute('type', 'number')
				->setAttribute('min', 1)
				->setAttribute('step', 1);
		$form->addTextArea(Model\OrderManager::COLUMN_NOTE, 'Poznámka:')
				->setAttribute('placeholder', 'jména účastníků, pokud jich je více, nebo jiný účastník, než objednatel');

		$form->addSubmit('send', 'Odeslat');
		$form->onSuccess[] = [$this, 'formSucceeded'];
		return $form;
	}

	public function formSucceeded(Form $form) {
		$values = $form->getValues();

		$contact = $this->contactMan->getContactOrderByCompanyId($this->course->getCompanyId());
		$values->offsetSet(Model\OrderManager::COLUMN_TERM_ID, $this->term->getId());
		$values->offsetSet(Model\OrderManager::COLUMN_CREATED_AT, new \Nette\Utils\DateTime());
		$values->offsetSet(Model\OrderManager::COLUMN_COURSE_NAME, $this->course->getName());
		$values->offsetSet(Model\OrderManager::COLUMN_COMPANY_ID, $this->course->getCompanyId());
		$values->offsetSet(Model\OrderManager::COLUMN_SENT_TO, $contact->getEmail());
		$values->offsetSet(Model\OrderManager::COLUMN_TERM_FROM, $this->term->getFrom());
		$values->offsetSet(Model\OrderManager::COLUMN_TERM_TO, $this->term->getTo());
		$userMessage = $this->createUserOrderMessage($values);
		$companyMessage = $this->createCompanyOrderMessage($values, $contact);
		$this->createAttachment($values);
		$this->orderMan->insertAndSendEmails($values, $userMessage, $companyMessage, $this->createAttachment($values));
		$this->flashMessage('Objednávka byla odeslána.');
		$this->redirect('Course:course', $this->course->getSeokey());
	}

	private function createUserOrderMessage(\Nette\Utils\ArrayHash $values) {
		$template = $this->createTemplate();
		$template->setFile(dirname(__FILE__) . '/templates/mail/orderUser.latte');
		$template->order = $values;
		$template->course = $this->course;
		$template->term = $this->term;
		$template->company = $this->course->getCompany();
		$template->mailto = \App\Presenters\BasePresenter::EMAIL_INFO;
		$email = new Mail\Message;

		$email->setFrom(\App\Presenters\BasePresenter::EMAIL_NOREPLY, 'evzdelavani.cz')
				->setSubject('evzdelavani.cz - objednávka kurzu')
				->setHtmlBody($template)
				->addTo($values->offsetGet(Model\OrderManager::COLUMN_EMAIL));
		return $email;
	}

	private function createCompanyOrderMessage(\Nette\Utils\ArrayHash $values, So\Contact $contact) {
		$template = $this->createTemplate();
		$template->setFile(dirname(__FILE__) . '/templates/mail/orderCompany.latte');
		$template->order = $values;
		$template->course = $this->course;
		$template->term = $this->term;
		$template->company = $this->course->getCompany();
		$template->mailto = \App\Presenters\BasePresenter::EMAIL_INFO;
		$email = new Mail\Message;
		$email->setFrom(\App\Presenters\BasePresenter::EMAIL_NOREPLY, 'evzdelavani.cz')
				->setSubject('evzdelavani.cz - objednávka kurzu')
				->setHtmlBody($template)
				->addTo($contact->getEmail());
		return $email;
	}

	private function createAttachment(\Nette\Utils\ArrayHash $order) {
		$template = $this->createTemplate();
		$company = $this->course->getCompany();
		$template->setFile(dirname(__FILE__) . '/templates/pdf/order.latte');
		$template->order = $order;
		$template->course = $this->course;
		$template->term = $this->term;
		$template->company = $company;
		$template->billingAddress = $company->getAddressBilling();

		$pdf = new \mPDF();
		$pdf->WriteHTML((string) $template);
		return $pdf;
	}

}
