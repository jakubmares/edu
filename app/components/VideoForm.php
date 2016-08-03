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
 * Description of VideoForm
 *
 * @author jakubmares
 */
class VideoForm extends UI\Control {

	/** @var So\CompanyImage */
	private $record;

	/** @var int */
	private $companyId;

	/** @var Model\CompanyVideoManager */
	private $videoMan;
	public $onSave;

	public function __construct($companyId, Model\CompanyVideoManager $videoMan) {
		$this->companyId = $companyId;
		$this->videoMan = $videoMan;
	}

	public function setRecord(So\CompanyVideo $record) {
		$this->record = $record;
		$data = $record->toArray();
		$data[Model\CompanyVideoManager::COLUMN_VIDEO] = 'http://youtu.be/'.$record->getVideo();
		$this['form']->setDefaults($data);
	}

	protected function createComponentForm() {
		$form = new Form();
		$form->addHidden(Model\CompanyVideoManager::COLUMN_COMPANY_ID, $this->companyId);
		$form->addText(Model\CompanyVideoManager::COLUMN_TITLE, 'Titulek:');
		$form->addText(Model\CompanyVideoManager::COLUMN_VIDEO, 'Adresa videa YouTube:');
		$form->addCheckbox(Model\CompanyVideoManager::COLUMN_ACTIVE, 'Aktivni:')->setValue(true);
		$form->addSubmit('send', 'Ulozit');
		$form->onSuccess[] = [$this, 'processForm'];
		return $form;
	}

	public function processForm($form) {
		if ((int) $this->getParameter('id') && !$this->record) {
			throw new \Nette\Application\BadRequestException;
		}

		if ($this->record) {
			$this->videoMan->update($this->record->getId(), $form->getValues());
		} else {
			$this->videoMan->insert($form->getValues());
		}
		$this->onSave($this);
	}

	public function render() {
		$template = $this->template;
		$template->setFile(__DIR__ . '/templates/imageForm.latte');
		$template->render();
	}

}
