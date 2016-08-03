<?php

namespace App\Module\Front\Presenters;

use App\Model,
	App\Forms\Form;

/**
 * Base presenter for all application presenters.
 */
abstract class BasePresenter extends \App\Presenters\BasePresenter {

	/** @var Model\CategoryManager */
	protected $categoryMan;

	/** @var Model\FocusManager */
	protected $focusMan;

	/** @var Model\CourseManager */
	protected $courseMan;

	/** @var Model\CompanyManager */
	protected $companyMan;

	/** @var Model\CompanyImageManager */
	protected $companyImageMan;

	/** @var Model\CompanyVideoManager */
	protected $companyVideoMan;

	/** @var Model\PartnerManager */
	protected $partnerMan;

	/** @var Model\FileManager */
	protected $fileMan;
	
	/** @var Model\SdtService */
	protected $sdtService;

	public function injectCategoryManager(Model\CategoryManager $categoryMan) {
		$this->categoryMan = $categoryMan;
	}

	public function injectCompanyVideoManager(Model\CompanyVideoManager $companyVideoMan) {
		$this->companyVideoMan = $companyVideoMan;
	}

	public function injectCompanyImageManager(Model\CompanyImageManager $companyImageMan) {
		$this->companyImageMan = $companyImageMan;
	}

	public function injectFocusManager(Model\FocusManager $focusMan) {
		$this->focusMan = $focusMan;
	}

	public function injectCourseManager(Model\CourseManager $courseMan) {
		$this->courseMan = $courseMan;
	}

	public function injectCompanyManager(Model\CompanyManager $companyMan) {
		$this->companyMan = $companyMan;
	}

	public function injectPartnerManager(Model\PartnerManager $partnerMan) {
		$this->partnerMan = $partnerMan;
	}

	public function injectFileManager(Model\FileManager $fileMan) {
		$this->fileMan = $fileMan;
	}
	
	protected function startup() {
		$this->sdtService = new Model\SdtService($this);
		
		return parent::startup();
	}

		public function beforeRender() {
		parent::beforeRender();
		$this->template->categories = $this->categoryMan->getActive();
		$this->template->topCompanies = $this->companyMan->getTopCompanies();
		$this->template->partners = $this->companyMan->getPartners();
		$this->template->lastImage = $this->companyImageMan->getLast();
		$this->template->lastVideo = $this->companyVideoMan->getLast();
		$this->template->collaborators = $this->partnerMan->getPartners();
		$this->template->priceList = $this->fileMan->getLastPriceList();
		$this->sdtService->setJsonToTemplate();
		if($this->sdtService->getTemplateBreadCrumbs()){
			$this->template->breadcrumbs = $this->sdtService->getTemplateBreadCrumbs();
		}
	}

	public function handleCategoryChange($value) {
		if ($value) {
			$this['searchCourseForm']['focus']->setPrompt('Vsechna zamereni v kategorii')
					->setItems($this->createList($this->focusMan->getActiveFocusesByCategoryId($value), 'id', 'name'));
		} else {
			$this['searchCourseForm']['focus']->setItems($this->createList($this->focusMan->getActiveFocuses(), 'id', 'name'));
		}
		$this->invalidateControl('focusSnippet');
	}

	public function handleFocusChange($value) {
		if ($value) {
			$category = $this->categoryMan->getByFocusId($value);
			$this['searchCourseForm']['category']->setDefaultValue($category->getId());
		} else {
			$this['searchCourseForm']['category']->setDefaultValue(null);
		}
		$this->invalidateControl('categorySnippet');
	}

	protected function setSearchCourseFormFocuses($focuses) {
		$this['searchCourseForm']['focus']->setItems($this->createList($focuses, 'id', 'name'));
	}

	protected function createComponentSearchCourseForm() {
		$form = new Form();
		$form->addSelect('category', 'Kategorie', $this->createList($this->categoryMan->getActive(), 'id', 'name'))
				->setPrompt('Vsechny kategorie');
		$form->addSelect('focus', 'Zamereni', $this->createList($this->focusMan->getActiveFocuses(), 'id', 'name'))
				->setPrompt('Vsechna zamereni');
		$form->addText('address', 'Kde kurz hledáte')->setAttribute('placeholder', 'Kde kurz hledáte');
		$form->addText('courseTerm', 'JAKÝ KURZ HLEDÁTE')->setAttribute('placeholder', 'Jaký kurz hledáte');
		$form->addDate('dateFrom')->setAttribute('placeholder', 'Datum od');
		$form->addDate('dateTo')->setAttribute('placeholder', 'Datum do');
		$form->addSubmit('search', 'Hledat');
		$form->onSuccess[] = [$this, 'courseFormSucceeded'];
		return $form;
	}

	public function courseFormSucceeded(Form $form) {

		$values = $form->getValues();
		$dateFrom = $values->dateFrom ? $values->dateFrom->format('Y-m-d') : null;
		$dateTo = $values->dateTo ? $values->dateTo->format('Y-m-d') : null;

		if ($values->focus) {
			$focus = $this->focusMan->getFocus($values->focus);
			$category = $focus->getCategory();
			$this->redirect('Course:focus', $category->getSeokey(), $focus->getSeokey(), $values->courseTerm, $values->address, $dateFrom, $dateTo);
		}

		if ($values->category) {
			$category = $this->categoryMan->get($values->category);
			$this->redirect('Course:category', $category->getSeokey(), $values->courseTerm, $values->address, $dateFrom, $dateTo);
		}

		$this->redirect('Course:default', $values->courseTerm, $values->address, $dateFrom, $dateTo);
	}

	public function companyFormSucceeded(Form $form) {
		$values = $form->getValues();

		$this->redirect('Company:default', $values->companyTerm, $values->address);
	}

}
