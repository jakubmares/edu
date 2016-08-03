<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace App\Components;

use Nette\Application\UI,
	App\Model,
	App\Model\So,
	App\Forms\Form;

/**
 * Description of ImageForm
 *
 * @author jakubmares
 */
class ImageForm extends UI\Control {

	/** @var So\CompanyImage */
	private $record;

	/** @var int */
	private $companyId;

	/** @var Model\CompanyImageManager */
	private $imageMan;
	public $onSave;

	public function __construct($companyId, Model\CompanyImageManager $imageMan) {
		$this->companyId = $companyId;
		$this->imageMan = $imageMan;
	}

	public function setRecord(So\CompanyImage $record) {
		$this->record = $record;
		$this['form']->setDefaults($record);
	}

	protected function createComponentForm() {
		$form = new Form();
		$form->addHidden(Model\CompanyImageManager::COLUMN_COMPANY_ID, $this->companyId);
		$form->addText(Model\CompanyImageManager::COLUMN_TITLE, 'Popis:');
		$form->addUpload(Model\CompanyImageManager::COLUMN_IMG, 'Obrazek:');
		$form->addCheckbox(Model\CompanyImageManager::COLUMN_ACTIVE, 'Aktivni')->setDefaultValue(true);
		$form->addSubmit('send', 'Ulozit');
		$form->onSuccess[] = [$this, 'processForm'];
		return $form;
	}

	public function processForm($form) {
		if ((int) $this->getParameter('id') && !$this->record) {
			throw new \Nette\Application\BadRequestException;
		}

		$values = $form->getValues();
		$image = $values->offsetGet(Model\CompanyImageManager::COLUMN_IMG);

		if ($image->isImage()) {
			$values->offsetSet(Model\CompanyImageManager::COLUMN_IMG, $this->imageMan->saveFileUpload($image));
		} else {
			$values->offsetUnset(Model\CompanyImageManager::COLUMN_IMG);
		}

		if ($this->record) {
			$this->imageMan->update($this->record->getId(), $values);
		} else {
			$this->imageMan->insert($values);
		}
		$this->onSave($this);
	}

	public function render() {
		$template = $this->template;
		$template->setFile(__DIR__ . '/templates/imageForm.latte');
		$template->render();
	}

}
