<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace App\Module\Admin\Presenters;

use App\Model,
	Grido,
	App\Model\So,
	App\Components;

/**
 * Description of CompanyPresenter
 *
 * @author jakubmares
 */
class CompanyPresenter extends BasePresenter {

	/** @var Model\ContactManager */
	private $contactMan;

	/** @var Model\NoteManager */
	private $noteMan;

	/** @var Model\AddressManager */
	private $addressMan;

	/** @var Model\CountryManager */
	private $countryMan;

	/** @var Model\CompanyImageManager */
	private $imageMan;

	/** @var Model\CompanyVideoManager */
	private $videoMan;

	/** @var Components\ICompanyFormFactory */
	private $companyFormFa;

	/** @var So\Company */
	private $record;

	/** @persistent */
	public $backlink = '';

	public function __construct(Model\ContactManager $contactMan, Model\NoteManager $noteMan, Model\AddressManager $addressMan,
			Model\CountryManager $countryMan, Model\CompanyImageManager $imageMan, Model\CompanyVideoManager $videoMan,
			Components\ICompanyFormFactory $companyFormFa) {
		parent::__construct();
		$this->contactMan = $contactMan;
		$this->noteMan = $noteMan;
		$this->addressMan = $addressMan;
		$this->countryMan = $countryMan;
		$this->imageMan = $imageMan;
		$this->videoMan = $videoMan;
		$this->companyFormFa = $companyFormFa;
	}

	private function setRecord($id) {
		$this->record = $this->companyMan->get($id);
		if (!$this->record) {
			throw new BadRequestException('Firma nebyla nalezena');
		}
	}

	public function actionDetail($id) {
		$this->setRecord($id);
	}

	public function renderDetail($id) {
		$this->template->company = $this->record;

		$this->template->contacts = $this->contactMan->getByCompanyId($id);
		$this->template->addresses = $this->addressMan->getByCompanyId($id);
		$this->template->backlink = $this->storeRequest();
	}

	public function actionNotes($id) {
		$this->setRecord($id);
	}

	public function renderNotes() {
		$this->template->company = $this->record;
		$this->template->contacts = $this->contactMan->getContactsByType($this->record->getId(), So\Contact::TYPE_CONTACT_PERSON);
		$this->template->notes = $this->noteMan->getByCompanyId($this->record->getId());
		$this->template->backlink = $this->storeRequest();
	}

	public function renderList() {
		$this->template->backlink = $this->storeRequest();
	}

	public function actionNew() {
		
	}

	public function actionEdit($id) {
		$this->setRecord($id);
		$this->getForm()->setCompany($this->record);
	}

	public function renderEdit() {
		$this->template->company = $this->record;
	}

	protected function createComponentForm() {
		$control = $this->companyFormFa->create();
		$control->onSave[] = function(Components\CompanyForm $control, $id) {
			$this->flashMessage('Detail spolecnosti byl upraven');
			$this->restoreRequest($this->backlink);
			$this->redirect('detail', ['id' => $id]);
		};
		return $control;
	}

	protected function createComponentListGrid($name) {
		$grid = new Grido\Grid($this, $name);
		$grid->setModel($this->companyMan->getAllRows());
		$grid->filterRenderType = Grido\Components\Filters\Filter::RENDER_INNER;
		$grid->translator->lang = 'cs';
		$grid->defaultPerPage = 20;

		$grid->addColumnText(Model\CompanyManager::COLUMN_NAME, 'Název')->setFilterText();
		
		$grid->addColumnText(Model\CompanyManager::COLUMN_PARTNER, 'Partner')
				->setReplacement([0=>'Ne',1=>'Ano']);
		$grid->addFilterCheck(Model\CompanyManager::COLUMN_PARTNER, 'Partner')
				->setCondition('= 1');
		
		
		$grid->addColumnText(Model\CompanyManager::COLUMN_TOP, 'Top')
				->setReplacement([0=>'Ne',1=>'Ano']);
		$grid->addFilterCheck(Model\CompanyManager::COLUMN_TOP, 'Top')
				->setCondition('= 1');
		
		$grid->addColumnText(Model\CompanyManager::COLUMN_DEALER_ID, 'Obchodník')->setCustomRender(function($row) {
			$so = new So\Company($row);
			return $so->getDealer();
		});

		$dealerList = ['' => '--'];
		foreach (\Extensions\ListHelper::fetchPair($this->userMan->getDealers(), 'id', 'name') as $id => $item) {
			$dealerList[$id] = $item;
		}
		$grid->addFilterSelect(Model\CompanyManager::COLUMN_DEALER_ID, 'Obchodník', $dealerList);
	

		$grid->addColumnText(Model\CompanyManager::COLUMN_STATUS, 'Status')->setCustomRender(function($row) {
			$so = new So\Company($row);
			return $so->getStatusLabel();
		});
		$statusList = ['' => '--'];
		foreach (So\Company::statuses() as $id => $item) {
			$statusList[$id] = $item;
		}
		$grid->addFilterSelect(Model\CompanyManager::COLUMN_STATUS, 'Status', $statusList)->setDefaultValue(So\Company::STATUS_ADVERTISE);

$grid->addColumnText(Model\CompanyManager::COLUMN_ACTIVE, 'Aktivní')
				->setReplacement([0=>'Ne',1=>'Ano']);
		$grid->addFilterCheck(Model\CompanyManager::COLUMN_ACTIVE, 'Aktivní')
				->setCondition('= 1');

		$backlink = $this->storeRequest();

	

		$aEdit = \Nette\Utils\Html::el('a')->addAttributes(['style' => 'margin-right: 10px']);
		$aEdit->add('<span class="glyphicon glyphicon-edit" aria-hidden="true" data-toggle="tooltip" data-placement="bottom" title="Editovat firmu"></span>');

		$grid->addActionHref('edit', 'Edit', 'Company:edit', ['backlink' => $backlink])->setElementPrototype($aEdit);

		$aDet = \Nette\Utils\Html::el('a')->addAttributes(['style' => 'margin-right: 10px']);
		$aDet->add('<span class="glyphicon glyphicon-new-window" aria-hidden="true" data-toggle="tooltip" data-placement="bottom" title="Detail"></span>');

		$grid->addActionHref('detail', 'Detail', 'Company:notes')->setElementPrototype($aDet);

		return $grid;
	}

	/**
	 * 
	 * @return Components\CompanyForm
	 */
	public function getForm() {
		return $this['form'];
	}

}
