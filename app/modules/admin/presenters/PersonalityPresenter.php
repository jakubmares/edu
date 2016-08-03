<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace App\Module\Admin\Presenters;

use App\Model\PersonalityManager,
	App\Forms\Form;

/**
 * Description of PersonalityPresenter
 *
 * @author jakubmares
 */
class PersonalityPresenter extends BasePresenter {

	/** @var PersonalityManager */
	private $personalityMan;

	public function injectPersonalityManager(PersonalityManager $personalityMan) {
		$this->personalityMan = $personalityMan;
	}

	public function renderPersonalities() {
		$this->template->personalities = $this->personalityMan->getAll();
	}

	public function renderPersonality($id) {
		$this->template->personality = $this->personalityMan->get($id);
	}

	public function renderNewPersonality() {
		
	}

	public function actionEditPersonality($id) {
		$personality = $this->personalityMan->get($id);
		$this->template->personality = $personality;
		$this['personalityForm']->setDefaults($personality->toArray());
	}

	public function createComponentPersonalityForm() {
		$form = new Form();
		$form->addHidden('id');
		$form->addText(PersonalityManager::COLUMN_DEGREES_BEFORE, 'Titul pred');
		$form->addText(PersonalityManager::COLUMN_FRIRSTNAME, 'Jmeno');
		$form->addText(PersonalityManager::COLUMN_SURNAME, 'Prijmeni');
		$form->addText(PersonalityManager::COLUMN_DEGREES_AFTER, 'Titul za');
		$form->addTextArea(PersonalityManager::COLUMN_DESCRIPTION, 'Popis')->setAttribute('class', 'mceEditorBasic');
		$form->addUpload(PersonalityManager::COLUMN_IMAGE, 'Obrazek');
		$form->addCheckbox(PersonalityManager::COLUMN_ACTIVE, 'Aktivni')->setDefaultValue(true);
		$form->addSubmit('send', 'Ulozit');
		$form->onValidate[] = [$this, 'validatePersonalityForm'];
		$form->onSuccess[] = [$this, 'personalityFormSucceeded'];
		return $form;
	}

	public function validatePersonalityForm(Form $form) {
		$values = $form->getValues();
		/* @var $file \Nette\Http\FileUpload */
		$file = $values->offsetGet('image');
		if ($file->getSize() != 0 && !$file->isImage()) {
			$form->addError('Nevhodny typ souboru');
		}
	}

	public function personalityFormSucceeded(Form $form) {
		$values = $form->getValues();
		if ($values->image->isImage()) {
			$values->image = $this->personalityMan->saveFileUpload($values->image);
		} else {
			$values->offsetUnset('image');
		}

		if ($values->id) {
			$this->personalityMan->update($values->id, $values);
		} else {
			$this->personalityMan->insert($values);
		}
		$this->flashMessage('Osobnost byla ulozena');
		$this->redirect('personalities');
	}

}
