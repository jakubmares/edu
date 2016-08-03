<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace App\Module\Admin\Presenters;

use App\Model\PartnerManager,
	App\Forms\Form;

/**
 * Description of PartnerPresenter
 *
 * @author jakubmares
 */
class PartnerPresenter extends BasePresenter {

	/** @var PartnerManager */
	private $partnerMan;
	
	public function __construct(PartnerManager $partnerMan) {
		parent::__construct();
		$this->partnerMan = $partnerMan;
	}

	
	public function renderPartners(){
		$this->template->partners = $this->partnerMan->getPartners();
	}
	
	public function renderNewPartner(){
		
	}
	
	public function actionEditPartner($id){
		$partner = $this->partnerMan->get($id);
		$this['partnerForm']->setDefaults($partner->toArray());
	}
	
	
	protected function createComponentPartnerForm(){
		$form = new Form();
		$form->addHidden('id');
		$form->addText(PartnerManager::COLUMN_NAME,'Nazev parnera');
		$form->addText(PartnerManager::COLUMN_URL,'Url odkazu');
		$form->addUpload(PartnerManager::COLUMN_IMAGE,'Logo');
		$form->addText(PartnerManager::COLUMN_POSITION,'Pozice')->setType('number');
		$form->addCheckbox(PartnerManager::COLUMN_ACTIVE,'Aktivni')->setDefaultValue(true);
		$form->addSubmit('send', 'Ulozit');
		$form->onSuccess[] = [$this,'partnerFormSucceeded'];
		return $form;
	}
	
	public function partnerFormSucceeded(Form $form){
		$values = $form->getValues();
		$image = $values->offsetGet('image');
		if ($image->isImage()) {
			$values->offsetSet('image', $this->partnerMan->saveFileUpload($image));
		} else {
			$values->offsetUnset('image');
		}
		
		if($values->id){
			$this->partnerMan->update($this->id, $values);
		}  else {
			$this->partnerMan->insert($values);
		}
		$this->flashMessage('Parnter ulozen');
		$this->redirect('partners');
	}
	

}
