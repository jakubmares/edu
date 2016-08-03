<?php

namespace App\Forms;

use Nette;
use App\Model;
use Nette\Application\UI\Form;
use Nette\Security\User;

class SignFormFactory extends FormFactory {

	/** @var User */
	private $user;
	
	/** @var Model\UserManager */
	private $userMan;

	public function __construct(User $user, Model\UserManager $userMan) {
		$this->user = $user;
		$this->userMan = $userMan;
	}

	/**
	 * @return Form
	 */
	public function create() {
		$form = parent::create();
		$form->addText('email', 'E-mail:')
				->addRule(Form::EMAIL)
				->setRequired('Prosím zadejte svůj email.');

		$form->addPassword('password', 'Heslo:')
				->setRequired('Prosím zadejte své heslo.');

		$form->addCheckbox('remember', 'Zůstat přihlášen');

		$form->addSubmit('send', 'PŘIHLÁSIT');
		$form->onSuccess[] = array($this, 'formSucceeded');
		return $form;
	}
			

	public function formSucceeded(Form $form, $values) {
		if ($values->remember) {
			$this->user->setExpiration('14 days', FALSE);
		} else {
			$this->user->setExpiration('20 minutes', TRUE);
		}

		try {
			$this->user->login($values->email, $values->password);
		} catch (Nette\Security\AuthenticationException $e) {
			$form->addError('Email, nebo heslo je neplatné.');
		}
	}

}
