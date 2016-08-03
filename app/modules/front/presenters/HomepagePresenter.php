<?php

namespace App\Module\Front\Presenters;

use App\Model,
	App\Forms\Form,
	Extensions;

class HomepagePresenter extends BasePresenter {

	/** @var Model\TermManager */
	private $termMan;

	/** @var Model\AdviceManager */
	private $adviceMan;

	/** @var Model\ArticleManager */
	private $articleMan;
	private $ctab;

	public function __construct(Model\TermManager $termMan, Model\AdviceManager $adviceMan, Model\ArticleManager $articleMan) {
		$this->termMan = $termMan;
		$this->adviceMan = $adviceMan;
		$this->articleMan = $articleMan;
		$this->ctab = false;
	}

	public function actionSearchCompany() {
		$this->ctab = true;
		$this->actionDefault();
		$this->setView('default');
	}
	
	public function actionDefault(){	
		$this->sdtService->setCompany('Homepage:default');
		$this->sdtService->setWeb('Evzdelavani', 'Evzdelavani.cz', 'Homepage:default');
		$this->sdtService->setCompanyLogo($this->link('//Homepage:default').'images/logo.png');
		$this->sdtService->addCompanySocialLink('https://www.facebook.com/evzdelavani.cz');
		$this->sdtService->addCompanySocialLink('https://twitter.com/evzdelavani');
		$this->sdtService->addCompanyContact('customer service', 'info@evzdelavani.cz', '+420-602-545-853');
		$this->sdtService->addCompanyContact('technical support', 'helpdesk@evzdelavani.cz', null);
		$termsLastminute = $this->termMan->getTermsLastminuteHp();
		foreach ($termsLastminute as $term) {
			$this->sdtService->addTerm($term, 'Homepage:default');
		}
		$this->template->termsLastminute = $termsLastminute;
	}

	public function renderDefault() {
		$this->template->_form = $this['searchCourseForm'];
		$this->template->ctab = $this->ctab;
		
		$this->template->advices = $this->adviceMan->getAdvicesForHomepage(1);
		$this->template->articles = $this->articleMan->getHomepage(4);
		$this->template->interviews = $this->articleMan->getHomepageInterviews(2);
	}

	public function actionPartners() {
		$this->sdtService->addBreadCrumb('Úvod', 'Homepage:default');
		$this->sdtService->addBreadCrumb('Partneři portálu', 'this');
	}
	
	

	public function actionForDownload() {
		$this->sdtService->addBreadCrumb('Úvod', 'Homepage:default');
		$this->sdtService->addBreadCrumb('Ke stažení', 'this');
		$this->template->files = $this->fileMan->getForDownload();
	}

	protected function createComponentSearchCompanyForm() {
		$form = new Form();
		$form->addText('companyTerm', 'Jakou firmu hledáte')->setAttribute('placeholder', 'Jakou firmu hledáte');
		$form->addText('address', 'Kde firmu hledáte')->setAttribute('placeholder', 'Kde firmu hledáte');
		$form->addSubmit('search', 'Hledat');
		$form->onSuccess[] = [$this, 'companyFormSucceeded'];
		return $form;
	}

}
