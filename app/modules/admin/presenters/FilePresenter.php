<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace App\Module\Admin\Presenters;

use App\Model,
	App\Model\So,
	App\Forms\Form;

/**
 * Description of FilePresenter
 *
 * @author jakubmares
 */
class FilePresenter extends BasePresenter {
	/** @var Model\FileManager */
	private $fileMan;
	
	/** @var So\File */
	private $record;
	
	public function __construct(Model\FileManager $fileMan) {
		$this->fileMan = $fileMan;
	}
	
	private function setRecord($id){
		$this->record = $this->fileMan->get($id);
		if(!$this->record){
			throw new \Nette\Application\BadRequestException('Pozadovany soubor nenalezen');
		}
	}
	
	public function actionCreate(){
		$this->template->headline = 'Nový soubor';
		$this->setView('form');
	}
	
	public function actionEdit($id){
		$this->setRecord($id);
		$this['form']->setDefaults($this->record);
		$this->template->headline = 'Editace souboru';
		$this->setView('form');
	}
	
	public function actionDelete($id){
		$this->fileMan->delete($id);
		$this->redirect('list');
	}
	
	public function renderList(){
		$this->template->list = $this->fileMan->getAll();
	}

	protected function createComponentForm(){
		$form = new Form();
		$form->addSelect(Model\FileManager::COLUMN_TYPE,'Typ',  So\File::typeLabels())
				->addRule(Form::FILLED);
		$form->addText(Model\FileManager::COLUMN_TITLE,'Titulek')
				->addRule(Form::FILLED);
		$form->addUpload(Model\FileManager::COLUMN_PATH, 'Soubor')
				->addRule(Form::FILLED);
		$form->addSubmit('sent','Uložit');
		$form->onSuccess[] = [$this,'formSucceeded'];
		return $form;
	}
	
	public function formSucceeded(Form $form){
		if ((int) $this->getParameter('id') && !$this->record) {
			throw new \Nette\Application\BadRequestException;
		}
		
		$values = $form->getValues();
		
		/* @var $file \Nette\Http\FileUpload */
		$file = $values->offsetGet(Model\FileManager::COLUMN_PATH);
		$path = sprintf('%s/%s/%s','storage',$values->offsetGet(Model\FileManager::COLUMN_TYPE),$file->getName());
		$file->move($path);
		$values->offsetSet(Model\FileManager::COLUMN_PATH, $path);
		$values->offsetSet(Model\FileManager::COLUMN_UPDATED_AT, new \Nette\Utils\DateTime());
		$values->offsetSet(Model\FileManager::COLUMN_USER_ID, $this->user->getId());
		
		if($this->record){
			$this->fileMan->update($this->record->getId(), $values);
		}  else {
			$this->fileMan->insert($values);
		}
		$this->flashMessage('Soubor byl uložen');
		$this->redirect('File:list');
	}

}
