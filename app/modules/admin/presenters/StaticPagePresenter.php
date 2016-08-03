<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace App\Module\Admin\Presenters;
use App\Model,
	App\Model\So,
	App\Forms;

/**
 * Description of StaticPagePresenter
 *
 * @author jakubmares
 */
class StaticPagePresenter extends BasePresenter{
	
	/** @var Model\StaticPageManager */
	private $pageMan;
	
	/** @var So\StaticPage */
	private $record;
	
	public function __construct(Model\StaticPageManager $pageMan) {
		$this->pageMan = $pageMan;
	}
	
	private function setRecord($id){
		$this->record = $this->pageMan->get($id);
		if(!$this->record){
			throw new \Nette\Application\BadRequestException('Zaznam nenalezen');
		}
		
	}
	
	public function actionCreate(){
		$this->template->headline = 'Nová stránka';
		$this->setView('form');
	}
	
	public function actionEdit($id){
		$this->setRecord($id);
		$this['form']->setDefaults($this->record);
		$this->template->headline = sprintf('Editace stránky: %s',  $this->record->getIdLabel());
		$this->setView('form');
	}
	
	public function actionDetail($id){
		$this->setRecord($id);
		
	}
	
	public function renderDetail(){
		$this->template->record = $this->record;
	}
	
	public function renderList(){
		$this->template->list = $this->pageMan->getAll();
	}
	
	
	protected function createComponentForm() {
		$form = new Forms\Form();
		$form->addSelect(Model\StaticPageManager::COLUMN_ID, 'Stránka',  So\StaticPage::idLabels())
				->addRule(Forms\Form::REQUIRED)
				->setPrompt('Vyberte stránku');
		$form->addText(Model\StaticPageManager::COLUMN_TITLE,'Titulek')
				->addRule(Forms\Form::REQUIRED);
		$form->addTextArea(Model\StaticPageManager::COLUMN_CONTENT,'Obsah stránky')->setAttribute('class','mceEditor')
				->addRule(Forms\Form::REQUIRED);
		$form->addSubmit('sent','Uložit');
		$form->onSuccess[] = [$this,'formSucceeded'];
		return $form;
	}
	
	public function formSucceeded(Forms\Form $form){
		if ((int) $this->getParameter('id') && !$this->record) {
			throw new \Nette\Application\BadRequestException;
		}
		
		$values = $form->getValues();
		$values->offsetSet(Model\StaticPageManager::COLUMN_UPDATED_AT, new \Nette\Utils\DateTime());
		$values->offsetSet(Model\StaticPageManager::COLUMN_USER_ID, $this->user->getId());
		if($this->record){
			$this->pageMan->update($this->record->getId(), $values);
		}  else {
			$this->pageMan->insert($values);
		}
		
		$this->flashMessage('Stránka byla uložena');
		$this->redirect('StaticPage:list');
	}

}
