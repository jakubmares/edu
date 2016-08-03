<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace App\Module\Admin\Presenters;

use App\Model\CategoryManager,
	App\Model\FocusManager,
	App\Forms\Form;

/**
 * Description of CoursePresenter
 *
 * @author jakubmares
 */
class CoursePresenter extends BasePresenter {

	/** @var CategoryManager */
	private $categoryMan;

	/** @var FocusManager */
	private $focusMan;

	/** @persistent */
	public $backlink = '';

	public function __construct(CategoryManager $categoryMan, FocusManager $focusMan) {
		parent::__construct();
		$this->categoryMan = $categoryMan;
		$this->focusMan = $focusMan;
	}

	public function renderCategories() {
		$this->template->categories = $this->categoryMan->getAll();
	}

	public function renderNewCategory() {
		
	}

	public function actionEditCategory($id) {
		$category = $this->categoryMan->get($id);
		$this->template->category = $category;
		/* @var $form Form */
		$form = $this['categoryForm'];
		$form->setDefaults($category);
	}

	public function actionNewFocus($id) {
		/* @var $form Form */
		$form = $this['focusForm'];
		$form->setDefaults(['categoryId' => $id]);
	}

	public function actionEditFocus($id) {
		/* @var $form Form */
		$form = $this['focusForm'];
		$focus = $this->focusMan->getFocus($id);
		$this->template->focus = $focus;
		$form->setDefaults($focus->toArray());
	}

	protected function createComponentFocusForm() {
		$form = new Form();
		$form->addHidden(FocusManager::COLUMN_ID);
		$form->addText(FocusManager::COLUMN_NAME, 'Nazev')->addRule(Form::FILLED);
		$form->addText(FocusManager::COLUMN_SEOKEY, 'Nazev pro URL')->addRule(Form::FILLED);
		$form->addText(FocusManager::COLUMN_POSITION, 'Pozice')
				->setAttribute('type', 'number')
				->addRule(Form::FILLED);
		$form->addCheckbox(FocusManager::COLUMN_ACTIVE, 'Aktivni');
		$form->addHidden(FocusManager::COLUMN_CATEGORY_ID);
		$form->addSubmit('save', 'Ulozit');
		$form->onSuccess[] = [$this, 'focusFormSucceeded'];
		return $form;
	}

	protected function createComponentCategoryForm() {
		$form = new Form();
		$form->addHidden(CategoryManager::COLUMN_ID);
		$form->addText(CategoryManager::COLUMN_NAME, 'Nazev')->addRule(Form::FILLED);
		$form->addText(CategoryManager::COLUMN_SEOKEY, 'Nazev pro URL')->addRule(Form::FILLED);
		$form->addText(CategoryManager::COLUMN_POSITION, 'Pozice')->addRule(Form::FILLED);
		$form->addCheckbox(CategoryManager::COLUMN_ACTIVE, 'Aktivni');
		$form->addSubmit('save', 'Uložit');
		$form->onSuccess[] = [$this, 'categoryFormSucceeded'];
		return $form;
	}

	public function focusFormSucceeded(Form $form, $values) {
		if ($values->id) {
			$this->focusMan->update($values->id, $values);
		} else {
			$this->focusMan->insertRow($values);
		}
		$this->flashMessage('Podkategorie byla ulozena');
		$this->redirect('Course:categories');
	}

	public function categoryFormSucceeded(Form $form, $values) {
		if ($values->id) {
			$this->categoryMan->update($values->id, $values);
		} else {
			$this->categoryMan->insertRow($values);
		}
		$this->flashMessage('Kategorie byla ulozena');
		$this->restoreRequest($this->backlink);
		$this->redirect('Course:categories');
	}

}
