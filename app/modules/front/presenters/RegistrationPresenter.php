<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace App\Module\Front\Presenters;
use App\Model,
	App\Forms\Form;
/**
 * Description of RegistrationPresenter
 *
 * @author jakubmares
 */
class RegistrationPresenter extends BasePresenter{
	
	/** @var Model\UserManager */
	private $userMan;
	
	public function __construct(Model\UserManager $userMan) {
		$this->userMan = $userMan;
	}
	
	public function renderRegistration(){
		
	}
	
	public function createComponentRegistrationForm(){
		$form = new Form();
		
		
		
	}

}
