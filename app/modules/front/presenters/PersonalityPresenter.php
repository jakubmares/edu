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
 * Description of PersonalityPresenter
 *
 * @author jakubmares
 */
class PersonalityPresenter extends BasePresenter {

	const
			PAGE_SIZE = 10;

	/** @var Paginator */
	private $paginator;

	/** @var Model\PersonalityManager */
	private $personalityMan;

	/** @var Model\ArticleManager */
	private $articleMan;

	public function __construct(Model\PersonalityManager $personalityMan, Model\ArticleManager $articleMan) {
		$this->personalityMan = $personalityMan;
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

	public function renderPersonalityList() {
		$this->paginator->setItemCount($this->personalityMan->countActive());
		
		$this->template->personalities = $this->personalityMan->getActivePagin($this->paginator);
		$this->template->paginator = $this->paginator;
		$this->template->breadcrumbs = [
			'Úvod' => $this->link('Homepage:default'),
			'Osobnosti' => null,
		];
	}

	public function renderPersonality($seokey) {
		$personality = $this->personalityMan->getBySeokey($seokey);
		$this->paginator->setItemCount($this->articleMan->countArticlesByPersonalityId($personality->getId()));
		
		$this->template->personality = $personality;
		$this->template->articles = $this->articleMan->getArticlesByPersonalityId($personality->getId(), $this->paginator);
		$this->template->paginator = $this->paginator;
		$this->template->breadcrumbs = [
			'Úvod' => $this->link('Homepage:default'),
			'seznam osobností' => $this->link('Personality:personalityList'),
			$personality->getName() => null
		];
	}

}
