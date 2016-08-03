<?php

namespace App\Module\Front\Presenters;

use Nette;
use App\Forms\SignFormFactory,
	App\Forms\Form,
	Nette\Mail,
	App\Model;

class SignPresenter extends BasePresenter {

	/** @var SignFormFactory */
	private $factory;

	/** @var Model\UserManager */
	private $userMan;

	/** @var Model\MailService */
	private $mailer;

	public function __construct(SignFormFactory $factory, Model\UserManager $userMan, Model\MailService $mailer) {
		$this->factory = $factory;
		$this->userMan = $userMan;
		$this->mailer = $mailer;
	}

	protected function createComponentRenewPassForm() {
		$form = new Form();
		$form->addText('email', 'E-mail:')->addRule(Form::EMAIL);
		$form->addSubmit('send', 'ODESLAT');
		$form->onValidate[] = [$this, 'renewPassFormValidate'];
		$form->onSuccess[] = [$this, 'renewPassFormSucceeded'];

		return $form;
	}

	public function renewPassFormValidate(Form $form) {
		$values = $form->getValues();
		if (!$this->userMan->isEmailExist($values->email)) {
			$form->addError('Pro tento email neexistuje účet');
		}
	}

	public function renewPassFormSucceeded(Form $form) {
		$values = $form->getValues();
		if ($this->userMan->isEmailExist($values->email)) {
			$pass = $this->userMan->renewPass($values->email);
			$template = $this->createTemplate();
			$template->setFile(dirname(__FILE__) . '/templates/mail/renewPass.latte');
			$template->pass = $pass;
			$template->email = $values->email;
			$template->mailto = \App\Presenters\BasePresenter::EMAIL_INFO;
			$email = new Mail\Message;
			$email->setFrom(\App\Presenters\BasePresenter::EMAIL_NOREPLY)
					->setSubject('evzdelavani.cz - obnova hesla')
					->setHtmlBody($template)
					->addTo($values->email);
			
			$this->mailer->send($email);
		}
	}

	/**
	 * Sign-in form factory.
	 * @return Nette\Application\UI\Form
	 */
	protected function createComponentSignInForm() {
		$form = $this->factory->create();
		$form->onSuccess[] = function ($form) {
			$form->getPresenter()->redirect('Homepage:');
		};
		return $form;
	}
	
	public function actionChangePass(){
		if(!$this->user->isLoggedIn()){
			$this->flashMessage('Pro tuto operaci je nutné přihlášení');
			$this->redirect('Sign:in');
		}
	}
	
	public function renderChangePass(){
		$this->template->breadcrumbs = [
			'Úvod' => $this->link('Homepage:default'),
			'Změna hesla' => null
		];
	}


	protected function createComponentChangePassForm(){
		$form = new Form();
		$form->addPassword('pass','* Původni heslo:')->addRule(Form::FILLED);
		$form->addPassword('pass1', '* Nové heslo:')
				->addRule(Form::FILLED)
				->addCondition(Form::MAX_LENGTH, 5)
				->addRule(Form::PATTERN, 'Musí obsahovat číslici', '.*[0-9].*');
		$form->addPassword('pass2', '* Kontrola nového hesla:')->addRule(Form::FILLED);
		$form->onValidate[] = [$this,'changePassFormValidate'];
		$form->onSuccess[] = [$this,'changePassFormSucceeded'];
		$form->addSubmit('send', 'Uložit');
		return $form;
	}
	
	public function changePassFormValidate(Form $form){
		$values = $form->getValues();
		if(!$this->userMan->isPass($this->user->getId(), $values->pass)){
			$form->addError('Nesprávné původní heslo');
		}
		
		if($values->offsetGet('pass1') != $values->offsetGet('pass2')){
			$form->addError('Kontrola nového hesla neodpovídá');
		}
	}
	
	public function changePassFormSucceeded(Form $form){
		$this->userMan->updatePass($this->user->getId(), $form->values->offsetGet('pass1'));
		$this->flashMessage('Heslo bylo změněno.');
		$this->redirect('Homepage:default');
	}

	public function renderIn() {
		$this->template->breadcrumbs = [
			'Úvod' => $this->link('Homepage:default'),
			'Přihlášení' => null
		];
	}

	public function actionOut() {
		$this->getUser()->logout();
		$this->redirect('Homepage:');
	}

}
