<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace App\Module\Admin\Presenters;

use App\Model\AdviceManager,
	App\Forms\Form;
/**
 * Description of Advice
 *
 * @author jakubmares
 */
class AdvicePresenter extends BasePresenter{
	
	/** @var AdviceManager */
	private $adviceMan;
	
	
	public function __construct(AdviceManager $adviceMan) {
		parent::__construct();
		$this->adviceMan = $adviceMan;
	}


	public function renderAdvices(){
		$this->template->advices = $this->adviceMan->getAll();
	}
	
	public function renderNewAdvice(){
		
	}
	
	public function renderAdvice($id){
		$this->template->advice = $this->adviceMan->get($id);
	}
	
	public function actionEditAdvice($id){
		$advice = $this->adviceMan->get($id);
		$this['adviceForm']->setDefaults($advice->toArray());
	}
	
	protected function createComponentAdviceForm() {
		$form = new Form();
		$form->addHidden(AdviceManager::COLUMN_ID);
		$form->addText('header','Hlavicka')->addRule(Form::FILLED);
	
		
		$form->addSelect(AdviceManager::COLUMN_COMPANY_ID, 'Firma',  $this->createList($this->companyMan->getAll(), 'id', 'name') )
				->setPrompt('Firma neprirazena');
		$form->addTextArea(AdviceManager::COLUMN_CONTENT, 'Obsah')->addRule(Form::FILLED)
				->setAttribute('class','mceEditorLink');
		$form->addDate(AdviceManager::COLUMN_VALID_FROM,'Platne od')->addRule(Form::FILLED);
		$form->addDate(AdviceManager::COLUMN_VALID_TO,'Platne do')->addRule(Form::FILLED);
		$form->addText(AdviceManager::COLUMN_POSITION,'Poradi')
				->setType('number')
				->addCondition(Form::MAX_LENGTH, 1)
				->addRule(Form::INTEGER);
		$form->addCheckbox(AdviceManager::COLUMN_VALID,'Platne')->setDefaultValue(true);
		$form->addSubmit('send', 'Ulozit');
		$form->onSuccess[] = [$this,'adviceFormSucceeded'];
		return $form;
	}
	
	public function adviceFormSucceeded(Form $form){
		$values = $form->getValues();
		$values->offsetSet(AdviceManager::COLUMN_USER_ID, $this->getUser()->getIdentity()->getId());
		if($values->id){
			$this->adviceMan->update($values->id, $values);
		}  else {
			$this->adviceMan->insert($values);
		}
		$this->flashMessage('Doporuceni bylo ulozeno');
		$this->redirect('advices');
	}
	
	
	
}
