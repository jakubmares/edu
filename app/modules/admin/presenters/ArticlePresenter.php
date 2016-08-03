<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace App\Module\Admin\Presenters;

use App\Forms\Form,
	App\Model\ArticleManager,
	App\Model\PersonalityManager;

/**
 * Description of ArticlePresenter
 *
 * @author jakubmares
 */
class ArticlePresenter extends BasePresenter {

	/** @var ArticleManager */
	private $articleMan;

	/** @var PersonalityManager */
	private $personalityMan;

	public function __construct(ArticleManager $articleMan, PersonalityManager $personalityMan) {
		parent::__construct();
		$this->articleMan = $articleMan;
		$this->personalityMan = $personalityMan;
	}

		public function renderArticles() {
		$this->template->articles = $this->articleMan->getAll();
	}

	public function renderNewArticle() {
		
	}

	public function actionEditArticle($id) {
		$article = $this->articleMan->get($id);
		$this->template->article = $article;
		$this['articleForm']->setDefaults($article->toArray());
	}

	protected function createComponentArticleForm() {
		$form = new Form();
		$form->addHidden('id');
		$form->addText(ArticleManager::COLUMN_TITLE, 'Titulek')
				->addRule(Form::FILLED);
		$form->addTextArea(ArticleManager::COLUMN_PEREX, 'Perex')->setAttribute('class', 'mceEditor')
				->addRule(Form::FILLED);
		$form->addTextArea(ArticleManager::COLUMN_CONTENT, 'Obsah')->setAttribute('class', 'mceEditor');
		$form->addUpload(ArticleManager::COLUMN_IMAGE, 'Obrazek');
		$form->addSelect(ArticleManager::COLUMN_PERSONALITY_ID, 'Osobnost', $this->createList($this->personalityMan->getAll()))
				->setPrompt('Osobnost neprirazena');
		$form->addText(ArticleManager::COLUMN_AUTHOR,'Autor');
		$form->addDate(ArticleManager::COLUMN_PUBLISHED_AT, 'Publikovat dne')
				->addRule(Form::FILLED);
		$form->addCheckbox(ArticleManager::COLUMN_ACTIVE, 'Aktivni')->setDefaultValue(true);
		$form->addSubmit('send', 'Ulozit');
		$form->onValidate[] = [$this, 'validateArticleForm'];
		$form->onSuccess[] = [$this, 'articleFormSucceeded'];
		return $form;
	}

	public function validateArticleForm(Form $form) {
		$values = $form->getValues();
		/* @var $file \Nette\Http\FileUpload */
		$file = $values->offsetGet(ArticleManager::COLUMN_IMAGE);
		if ($file->getSize() != 0 && !$file->isImage()) {
			$form->addError('Nevhodny typ souboru');
		}
	}

	public function articleFormSucceeded(Form $form) {
		$values = $form->getValues();
		$values->offsetSet(ArticleManager::COLUMN_USER_ID, $this->getUser()->getIdentity()->getId());
		
		if ($values->image->isImage()) {
			$values->image = $this->articleMan->saveFileUpload($values->image);
		} else {
			$values->offsetUnset(ArticleManager::COLUMN_IMAGE);
		}
		
		if ($values->id) {
			$this->articleMan->update($values->id, $values);
		} else {
			$this->articleMan->insert($values);
		}
		$this->flashMessage('Clanek byl ulozen');
		$this->redirect('articles');
	}

}
