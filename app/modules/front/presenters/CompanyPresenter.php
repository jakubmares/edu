<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace App\Module\Front\Presenters;

use App\Model,
	App\Model\So\Contact,
	Extensions\Paginator;

/**
 * Description of Company
 *
 * @author jakubmares
 */
class CompanyPresenter extends BasePresenter {

	const
			PAGE_SIZE = 20;

	/** @var Model\ContactManager */
	private $contactMan;

	/** @var Model\TermManager */
	private $termMan;

	/** @var Model\AddressManager */
	private $addressMan;

	/** @var Model\CompanyImageManager */
	private $imageMan;

	/** @var Model\CompanyVideoManager */
	private $videoMan;
	
	/** @var Extensions\Paginator */
	private $paginator;

	public function __construct(Model\ContactManager $contactMan, Model\TermManager $termMan, Model\AddressManager $addressMan,
			Model\CompanyImageManager $imageMan, Model\CompanyVideoManager $videoMan) {
		$this->contactMan = $contactMan;
		$this->termMan = $termMan;
		$this->addressMan = $addressMan;
		$this->imageMan = $imageMan;
		$this->videoMan = $videoMan;
	}

	protected function startup() {
		$this->paginator = new Paginator();
		$this->paginator->setPagin(10);
		$this->paginator->setItemsPerPage(self::PAGE_SIZE);
		$this->paginator->setPage(1);
		return parent::startup();
	}

	public function handlePaginate($page) {
		$this->paginator->setPage($page);
	}

	public function actionDefault($term = '', $address = '') {
		$this->sdtService->addBreadCrumb('Úvod', 'Homepage:default');
		$this->sdtService->addBreadCrumb('Vzdělávací společnosti', 'this');
	}
	
	public function renderDefault($term = '', $address = ''){
		$this->paginator->setItemCount($this->companyMan->getCountByTerm($term, $address));
		$this->template->companies = $this->companyMan->getByTerm($this->paginator, $term, $address);
		$this->template->paginator = $this->paginator;
	}

	public function actionCompany($companyKey) {	
		$company = $this->companyMan->getBySeokey($companyKey);
		$this->sdtService->addBreadCrumb('Úvod', 'Homepage:default');
		$this->sdtService->addBreadCrumb('Vzdělávací společnosti', 'Company:default');
		$this->sdtService->addBreadCrumb($company->name, 'this');
	}
	
	public function renderCompany($companyKey){
		$company = $this->companyMan->getBySeokey($companyKey);
		$id = $company->getId();
		$terms = $this->termMan->getTermsByCompanyId($id, $this->paginator);
		$this->paginator->setItemCount($this->termMan->countTermsByCompanyId($id));

		$this->template->company = $company;
		$this->template->images = $this->imageMan->getActiveByCompanyId($id);
		$this->template->contacts = $this->contactMan->getContactsByType($id, Contact::TYPE_SHOW);
		
		$this->template->addresses = $this->addressMan->getAddressesPresentableByCompanyId($id);
		$this->template->terms = $terms;
		$this->template->videos = $this->videoMan->getByCompanyId($id);
		$this->paginator->setItemCount($this->courseMan->getCoursesCountActiveByCompanyId($id));
		$this->template->paginator = $this->paginator;
	}

}
