<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace App\Module\Front\Presenters;

use App\Model,
	Extensions\Paginator;

/**
 * Description of AtriclePresenter
 *
 * @author jakubmares
 */
class ArticlePresenter extends BasePresenter {

	const
			PAGE_SIZE = 10;

	/** @var Paginator */
	private $paginator;

	/** @var Model\ArticleManager */
	public $articleMan;

	public function __construct(Model\ArticleManager $articleMan) {
		$this->articleMan = $articleMan;
	}

	protected function startup() {
		$this->paginator = new Paginator();
		$this->paginator->setPagin(6);
		$this->paginator->setItemsPerPage(self::PAGE_SIZE);
		$this->paginator->setPage(1);
		return parent::startup();
	}

	public function handlePaginate($page) {
		$this->paginator->setPage($page);
	}

	public function actionArticles() {
		$this->sdtService->addBreadCrumb('Úvod', 'Homepage:default');
		$this->sdtService->addBreadCrumb('články', 'this');
	}

	public function renderArticles() {
		$this->paginator->setItemCount($this->articleMan->countActiveArticles());
		$this->template->paginator = $this->paginator;
		$articles = $this->articleMan->getArticles($this->paginator);
		$this->template->articles = $articles;
	}

	public function actionArticle($seokey) {
		$article = $this->articleMan->getBySeokey($seokey);
		$this->sdtService->addBreadCrumb('Úvod', 'Homepage:default');
		$this->sdtService->addBreadCrumb('články', 'Article:articles');
		$this->sdtService->addBreadCrumb($article->title, 'this');
	}

	public function renderArticle($seokey) {
		$article = $this->articleMan->getBySeokey($seokey);
		$this->template->article = $article;
	}

	public function actionInterviews() {
		$this->sdtService->addBreadCrumb('Úvod', 'Homepage:default');
		$this->sdtService->addBreadCrumb('rozhovory', 'this');
	}

	public function renderInterviews() {
		$this->paginator->setItemCount($this->articleMan->countActiveInterviews());
		$this->template->paginator = $this->paginator;
		$this->template->articles = $this->articleMan->getInterviews($this->paginator);
	}

	public function actionInterview($seokey) {
		$article = $this->articleMan->getBySeokey($seokey);
		$this->template->article = $article;
		$this->template->personality = $article->getPersonality();
		$this->sdtService->addBreadCrumb('Úvod', 'Homepage:default');
		$this->sdtService->addBreadCrumb('rozhovory', 'Article:interviews');
		$this->sdtService->addBreadCrumb($article->title, 'this');
	}

}
