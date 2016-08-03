<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace App\Module\Front\Presenters;

use App\Model,
	App\Model\So;

/**
 * Description of StaticPagePresenter
 *
 * @author jakubmares
 */
class StaticPagePresenter extends BasePresenter {

	/**  @var So\StaticPage */
	private $record;

	/** @var Model\StaticPageManager */
	private $pageManager;

	public function __construct(Model\StaticPageManager $pageManager) {
		$this->pageManager = $pageManager;
	}
	
	private function setRecord($id){
		$this->record = $this->pageManager->get($id);
		if(!$this->record){
			throw new \Nette\Application\BadRequestException('Stranka nenalezena');
		}
	}
	
	public function actionContacts(){
		$this->setRecord(So\StaticPage::KEY_KONTAKTY);
		$this->setView('page');
	}
	
	public function actionCodex(){
		$this->setRecord(So\StaticPage::KEY_KODEX);
		$this->setView('page');
	}
	
	public function actionConditions(){
		$this->setRecord(So\StaticPage::KEY_PODMINKY);
		$this->setView('page');
	}


	public function renderPage(){
		if(!$this->record){
			throw new \Nette\Application\BadRequestException('Stranka nenalezena');
		}
		$this->template->breadcrumbs = [
			'Úvod' => $this->link('Homepage:default'),
			$this->record->getTitle() => false
		];
		$this->template->record = $this->record;
	}

}
