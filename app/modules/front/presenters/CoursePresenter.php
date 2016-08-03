<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace App\Module\Front\Presenters;

use App\Components,
	Extensions\Paginator,
	App\Model,
	App\Model\So,
	App\Forms\Form;

/**
 * Description of CoursePresenter
 *
 * @author jakubmares
 */
class CoursePresenter extends BasePresenter {

	const
			PAGE_SIZE = 30;

	/** @var Components\ICourseControlFactory */
	private $coursesControlFactory;

	/** @var Model\TermManager */
	private $termMan;

	/** @var Model\CourseImageManager */
	private $courseImageMan;

	/** @var Model\CourseVideoManager */
	private $courseVideoMan;
	
	/** @var Model\LevelManager */
	private $levelMan;

	/** @var Paginator */
	private $paginator;
	private $term;
	private $address;
	private $from;
	private $to;
	private $courses;

	/** @var So\Category */
	private $category;
	private $categoryId;

	/** @var So\Focus */
	private $focus;
	private $focusId;

	public function __construct(Components\ICourseControlFactory $coursesControlFactory, Model\TermManager $termMan,
			Model\CourseImageManager $courseImageMan, Model\CourseVideoManager $courseVideoMan,Model\LevelManager $levelMan) {
		$this->coursesControlFactory = $coursesControlFactory;
		$this->termMan = $termMan;
		$this->courseImageMan = $courseImageMan;
		$this->courseVideoMan = $courseVideoMan;
		$this->levelMan = $levelMan;
	}

	protected function startup() {
		$this->paginator = new Paginator();
		$this->paginator->setPagin(6);
		$this->paginator->setItemsPerPage(self::PAGE_SIZE);
		$this->paginator->setPage(1);
		return parent::startup();
	}

	private function setCategoryBySeokey($seokey) {
		$this->category = $this->categoryMan->getBySeokey($seokey);
		if (!$this->category) {
			$this->flashMessage('Kategorie nenalezena');
			$this->redirect('Homepage:default');
			//throw new \Nette\Application\BadRequestException('Kategorie nenalezena');
		}
		$this->categoryId = $this->category->getId();
	}

	private function setFocusBySeokey($seokey) {
		$this->focus = $this->focusMan->getFocusBySeokey($seokey);
		if (!$this->focus) {
			$this->flashMessage('Zaměření nenalezeno');
			$this->redirect('Homepage:default');
			//throw new \Nette\Application\BadRequestException('Zaměření nenalezeno');
		}
		$this->focusId = $this->focus->getId();
	}

	public function handlePaginate($page) {
		$this->paginator->setPage($page);
	}
	
	public function actionDefault($term = '', $address = '', $from = null, $to = null) {
		$this['searchCourseForm']->setDefaults(['courseTerm'=>$term,'address'=>$address,'dateFrom'=>$from,'dateTo'=>$to]);
		$this->sdtService->addBreadCrumb('Úvod', 'Homepage:default');
		$this->sdtService->addBreadCrumb('seznam kurzů', 'this');
	}

	public function renderDefault($term = '', $address = '', $from = null, $to = null) {
		$itemCount = $this->termMan->countByTerm($term, $address, $from, $to);
		$this->paginator->setItemCount($itemCount);
		$this->template->_form = $this['searchCourseForm'];
		$this->template->paginator = $this->paginator;
		
		
		$this->template->terms = $this->termMan->getByTerm($this->paginator, $term, $address, $from, $to);
	}

	public function actionCategory($catKey, $term = '', $address = '', $from = null, $to = null) {

		$this->setCategoryBySeokey($catKey);
		$defaults = ['category' => $this->categoryId, 'courseTerm' => $term, 'address' => $address, 'dateFrom' => $from, 'dateTo' => $to];
		$this['searchCourseCategoryForm']->setDefaults($defaults);
		$this->sdtService->addBreadCrumb('Úvod', 'Homepage:default');
		$this->sdtService->addBreadCrumb($this->category->name, 'this');
	}

	public function renderCategory($catKey, $term = '', $address = '', $from = null, $to = null) {
		$itemCount = $this->termMan->countByTermByCategoryId($this->categoryId, $term, $address, $from, $to);
		$this->template->terms = $this->termMan->getByTermByCategoryId($this->categoryId, $this->paginator, $term, $address, $from, $to);
		$this->template->catPartners = $this->companyMan->getCategoryPartners($this->categoryId);
		$this->template->category = $this->category;
		$this->paginator->setItemCount($itemCount);
		$this->template->paginator = $this->paginator;
		
		
	}

	public function actionFocus($catKey, $focKey, $term = '', $address = '', $from = null, $to = null) {
		$this->setCategoryBySeokey($catKey);
		$this->setFocusBySeokey($focKey);
		$defaults = ['category' => $this->categoryId, 'focus' => $this->focusId, 'courseTerm' => $term, 'address' => $address, 'dateFrom' => $from,
			'dateTo' => $to];
		$this['searchCourseCategoryForm']->setDefaults($defaults);
		
		$this->sdtService->addBreadCrumb('Úvod', 'Homepage:default');
		$this->sdtService->addBreadCrumb($this->category->name, "Course:category", $catKey);
		$this->sdtService->addBreadCrumb($this->focus->name, 'this');
		
		
	}

	public function renderFocus($catKey, $focKey, $term = '', $address = '', $from = null, $to = null) {
		$itemCount = $this->termMan->countByTermByFocusId($this->focusId, $term, $address, $from, $to);
		$this->template->terms = $this->termMan->getByTermByFocusId($this->focusId, $this->paginator, $term, $address, $from, $to);
		$this->template->focPartners = $this->companyMan->getFocusPartners($this->focusId);
		$this->template->category = $this->category;
		$this->template->focus = $this->focus;
		$this->paginator->setItemCount($itemCount);
		$this->template->paginator = $this->paginator;
		
	}
	
	public function actionLastminute(){
		$itemCount = $this->termMan->countTermsLastminute();
		$this->template->terms = $this->termMan->getTermsLastminute($this->paginator);
		$this->paginator->setItemCount($itemCount);
		$this->template->paginator = $this->paginator;
		$this->sdtService->addBreadCrumb('Úvod', 'Homepage:default');
		$this->sdtService->addBreadCrumb('Last minute', 'this');
	}
	


	public function actionCourse($courseKey) {
		$course = $this->courseMan->getCourseBySeokey($courseKey);

		if(!$course){
			$this->flashMessage("Hledaný kurz se nepodařilo nalézt");
			$this->redirect("Homepage:default");
		}
		foreach ($course->getTerms() as $term) {
			$this->sdtService->addTerm($term, 'this');
		}
		
		$this->focus = $this->focusMan->getFocusByCourseId($course->getId());
		$this->category = $this->focus->getCategory();

		$this->template->category = $this->category;
		$this->template->focuses = $this->focusMan->getFocusesByCourseId($course->getId());
		$this->template->course = $course;
		$this->template->images = $this->courseImageMan->getActiveByCourseId($course->getId());
		$this->template->videos = $this->courseVideoMan->getActiveByCourseId($course->getId());
		$this->template->company = $course->getCompany();
		$this->template->terms = $this->termMan->getActiveTermsByCourseId($course->getId());
		$catFoc = [];
		foreach ($this->focusMan->getFocusesByCourseId($course->getId()) as $focus) {
			$catFoc[$focus->getCategoryId()][] = $focus;
		}
		$this->template->catFoc = $catFoc;
		$this->template->levels = $this->levelMan->getByCourseId($course->getId());

		$this->sdtService->addBreadCrumb('Úvod', 'Homepage:default');
		$this->sdtService->addBreadCrumb($this->category->name, "Course:category", $this->category->getSeokey());
		$this->sdtService->addBreadCrumb($this->focus->name, "Course:focus", [$this->category->getSeokey(), $this->focus->getSeokey()]);
		$this->sdtService->addBreadCrumb($course->getName(), 'this');
		
	}

	protected function createComponentSearchCourseCategoryForm() {
		$form = new Form();
		$form->addHidden('category', $this->categoryId);
		$form->addSelect('focus', 'Zaměření', $this->createList($this->focusMan->getActiveFocusesByCategoryId($this->categoryId), 'id', 'name'))
				->setPrompt('Všechna zaměření v kategorii');
		$form->addText('address', 'Kde kurz hledáte')->setAttribute('placeholder', 'Kde kurz hledáte');
		$form->addText('courseTerm', 'JAKÝ KURZ HLEDÁTE')->setAttribute('placeholder', 'Jaký kurz hledáte');
		$form->addDate('dateFrom')->setAttribute('placeholder', 'Datum od');
		$form->addDate('dateTo')->setAttribute('placeholder', 'Datum do');
		$form->addSubmit('search', 'Hledat');
		$form->onSuccess[] = [$this, 'courseFormSucceeded'];
		return $form;
	}

}
