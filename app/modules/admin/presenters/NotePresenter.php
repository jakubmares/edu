<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace App\Module\Admin\Presenters;

use App\Model,
	App\Model\So,
	App\Forms\Form,
	App\Model\NoteManager,
	App\Model\ContactManager,
	Nette\Application\BadRequestException,
	Grido;

/**
 * Description of NotePresenter
 *
 * @author jakubmares
 */
class NotePresenter extends BasePresenter {

	/** @var NoteManager */
	private $noteMan;

	/** @var ContactManager */
	private $contactMan;

	/** @var So\Note */
	private $note;

	/** @var So\Company */
	private $company;

	/** @persistent */
	public $backlink = '';

	public function __construct(NoteManager $noteMan, ContactManager $contactMan) {
		parent::__construct();
		$this->noteMan = $noteMan;
		$this->contactMan = $contactMan;
	}

	private function setCompany($companyId) {
		$this->company = $this->companyMan->get($companyId);
		if (!$this->company) {
			throw new BadRequestException('Firma nebyla nalezena');
		}
	}

	private function setNote($id) {
		$this->note = $this->noteMan->get($id);
		if (!$this->note) {
			throw new BadRequestException('Poznamka nebyla nalezena');
		}
	}

	public function actionNew($companyId) {
		$this->setCompany($companyId);
		$items = $this->contactMan->getContactsByType($companyId, So\Contact::TYPE_CONTACT_PERSON);
		/* @var $noteForm Form */
		$noteForm = $this['form'];
		$noteForm->setDefaults([NoteManager::COLUMN_COMPANY_ID => $companyId]);
		$noteForm->getComponent(NoteManager::COLUMN_CONTACT_ID)->setItems(self::createList($items, 'id', 'name'));
	}

	public function renderNew() {
		$this->template->company = $this->company;
	}

	public function actionEdit($id) {
		$this->setNote($id);
		$companyId = $this->note->getCompanyId();
		$this->setCompany($companyId);

		$items = $this->contactMan->getContactsByType($companyId, So\Contact::TYPE_CONTACT_PERSON);
		/* @var $noteForm Form */
		$noteForm = $this['form'];
		$noteForm->getComponent(NoteManager::COLUMN_CONTACT_ID)->setItems(self::createList($items, 'id', 'name'));
		$noteForm->setDefaults($this->note);
	}

	public function renderEdit() {
		$this->template->company = $this->company;
	}

	public function actionList() {
		$this->template->note = '';
		$this->template->contactNote = '';
	}

	public function renderList() {
		$this->template->companies = $this->companyMan->getCompanies();
		$this->template->backlink = $this->storeRequest();
	}

	protected function createComponentNotesGrid($name) {
		$grid = new Grido\Grid($this, $name);
		$grid->setModel($this->companyMan->getAllRows());
		$grid->filterRenderType = Grido\Components\Filters\Filter::RENDER_INNER;
		$grid->translator->lang = 'cs';
		$grid->defaultPerPage = 20;
		$grid->addColumnText('last_contact', 'Poslední kontakt')->setCustomRender(function($row) {
			$so = new So\Company($row);
			$contact = $so->getLastContactNote();
			return $contact ? $contact->getContactAt()->format('d.m.Y') : null;
		});

		$grid->addColumnText(Model\CompanyManager::COLUMN_DEALER_ID, 'Obchodník')->setCustomRender(function($row) {
			$so = new So\Company($row);
			return $so->getDealer();
		});
		
		$dealerList = [''=>'--'];
		foreach (\Extensions\ListHelper::fetchPair($this->userMan->getDealers(), 'id', 'name') as $id => $item) {
			$dealerList[$id]=$item;
		}		
		$grid->addFilterSelect(Model\CompanyManager::COLUMN_DEALER_ID, 'Obchodník', $dealerList);

		$grid->addColumnText(Model\CompanyManager::COLUMN_POTENCIAL, 'Potenciál')->setCustomRender(function($row) {
			$so = new So\Company($row);
			return $so->getPotencialLabel();
		});
		
		$potencialList = [''=>'--'];
		foreach (So\Company::potencials() as $id => $item) {
			$potencialList[$id]=$item;
		}
		$grid->addFilterSelect(Model\CompanyManager::COLUMN_POTENCIAL, 'Potenciál',$potencialList);
		
		$grid->addColumnText(Model\CompanyManager::COLUMN_NAME, 'Název')->setFilterText();
		$grid->addColumnText(Model\CompanyManager::COLUMN_TYPE, 'Typ')->setCustomRender(function($row) {
			$so = new So\Company($row);
			return $so->getTypeLabel();
		});
		$typeList = [''=>'--'];
		foreach (So\Company::types() as $id => $item) {
			$typeList[$id]=$item;
		}
		$grid->addFilterSelect(Model\CompanyManager::COLUMN_TYPE, 'Typ',$typeList);
		
		$grid->addColumnText(Model\CompanyManager::COLUMN_STATUS, 'Status')->setCustomRender(function($row) {
			$so = new So\Company($row);
			return $so->getStatusLabel();
		});
		$statusList = [''=>'--'];
		foreach (So\Company::statuses() as $id => $item) {
			$statusList[$id]=$item;
		}
		$grid->addFilterSelect(Model\CompanyManager::COLUMN_STATUS, 'Status',$statusList);

		$grid->addColumnText('next_contact', 'Příští kontakt')->setCustomRender(function($row) {
			$so = new So\Company($row);
			$contact = $so->getLastContactNote();
			return $contact ? $contact->getNextContactAt()->format('d.m.Y') : null;
		});
		
		$backlink = $this->storeRequest();

		$aNew = \Nette\Utils\Html::el('a')->addAttributes(['class'=>'btn btn-default btn-xs btn-mini','style' => 'margin-right: 10px']);
		$aNew->add('<span class="glyphicon glyphicon-plus" aria-hidden="true" data-toggle="tooltip" data-placement="bottom" title="Nová poznámka"></span>');
		
		$grid->addActionHref('new', 'Nová poznámka')
				->setCustomRender(function($row)use($aNew,$backlink){
					$link = $this->link('Note:new', ['backlink' => $backlink,'companyId'=>$row->id]);
					$aNew->addAttributes(['href' => $link]);
					return $aNew;
				});

		$aEdit = \Nette\Utils\Html::el('a')->addAttributes(['style' => 'margin-right: 10px']);
		$aEdit->add('<span class="glyphicon glyphicon-edit" aria-hidden="true" data-toggle="tooltip" data-placement="bottom" title="Editovat firmu"></span>');

		$grid->addActionHref('edit', 'Edit', 'Company:edit', ['backlink' => $backlink])->setElementPrototype($aEdit);

		$aDet = \Nette\Utils\Html::el('a')->addAttributes(['style' => 'margin-right: 10px']);
		$aDet->add('<span class="glyphicon glyphicon-new-window" aria-hidden="true" data-toggle="tooltip" data-placement="bottom" title="Poznámky"></span>');
		
		$grid->addActionHref('detail', 'Poznámky', 'Company:notes')->setElementPrototype($aDet);
		
		$a = \Nette\Utils\Html::el('a')->addAttributes(['class' => 'ajax', 'onClick' => 'modalShow()']);
		$a->add('<span class="glyphicon glyphicon-modal-window" aria-hidden="true" data-toggle="tooltip" data-placement="bottom" title="Poslední poznámka"></span>');

		$grid->addActionHref('click', 'Poslední poznámka', 'click!')->setElementPrototype($a);

		return $grid;
	}

	public function handleClick($id) {
		if ($this->isAjax()) {
			$note = $this->noteMan->getLastNoteByCompanyId($id);
			$this->template->note = $note ? $note->getNote() : 'Není poznámka';
			$this->template->contactNote = $note ? $note->getContactNote() : 'Není poznámka';
		}
		$this->invalidateControl('modal');
	}

	protected function createComponentForm() {
		$form = new Form();
		$form->addHidden(NoteManager::COLUMN_COMPANY_ID);
		$form->addDate(NoteManager::COLUMN_CONTACT_AT, 'Datum kontaktu');
		$form->addTextArea(NoteManager::COLUMN_NOTE, 'Poznamka')
				->setAttribute('class', 'mceEditorBasic');
		$form->addDate(NoteManager::COLUMN_NEXT_CONTACT_AT, 'Datum pristho kontaktu');
		$form->addSelect(NoteManager::COLUMN_CONTACT_ID, 'Kontaktni osoba');
		$form->addTextArea(NoteManager::COLUMN_CONTACT_NOTE, 'Duvod pristiho kontaktu')
				->setAttribute('class', 'mceEditorBasic');
		$form->addCheckbox(NoteManager::COLUMN_DONE, 'Vyrizeno');
		$form->onSuccess[] = array($this, 'formSucceeded');
		$form->addSubmit('send', 'Uložit');
		return $form;
	}

	public function formSucceeded(Form $form) {
		if ((int) $this->getParameter('id') && !$this->note) { // kontrola existence záznamu pouze v případe editace
			throw new BadRequestException;
		}

		$values = $form->getValues();
		$values->offsetSet(NoteManager::COLUMN_USER_ID, $this->getUser()->getId());
		if ($this->note) {
			$this->noteMan->update($this->note->getId(), $values);
		} else {
			$this->noteMan->insert($values);
		}
		$this->flashMessage('Poznamka firmy byl uložena.');
		$this->restoreRequest($this->backlink);
		$this->redirect('Company:notes', $this->company->getId());
	}

}
