<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace App\Model;

use Nette\Utils\ArrayHash,
	App\Model\So\Article,
	Nette\Utils\DateTime;

/**
 * Description of ArticleManager
 *
 * @author jakubmares
 */
class ArticleManager extends BaseManager {

	const
			TABLE_NAME = 'article',
			COLUMN_ID = 'id',
			COLUMN_SEOKEY = 'seokey',
			COLUMN_PEREX = 'perex',
			COLUMN_CONTENT = 'content',
			COLUMN_USER_ID = 'user_id',
			COLUMN_CREATED_AT = 'created_at',
			COLUMN_IMAGE = 'image',
			COLUMN_AUTHOR = 'author',
			COLUMN_TITLE = 'title',
			COLUMN_ACTIVE = 'active',
			COLUMN_PUBLISHED_AT = 'published_at',
			COLUMN_PERSONALITY_ID = 'personality_id',
			IMAGE_WIDTH = '150',
			IMAGE_PATH = '/images/article',
			IMAGE_PREFIX = 'article-';

	public function insert($values) {
		$curdate = new DateTime();
		$values->offsetSet('createdAt', $curdate);
		$values->offsetSet(self::COLUMN_SEOKEY, $this->generateUniqName($values->offsetGet(self::COLUMN_TITLE)));
		return parent::insert($values);
	}

	public function update($id, ArrayHash $values) {
		$values->offsetSet(self::COLUMN_SEOKEY, $this->generateUniqName($values->offsetGet(self::COLUMN_TITLE), $id));
		return parent::update($id, $values);
	}

	/**
	 * 
	 * @param \Nette\Utils\Paginator $paginator
	 * @return Article[]
	 */
	public function getArticles(\Nette\Utils\Paginator $paginator) {
		$selection = $this->getSelectionArticles()->page($paginator->getPage(), $paginator->getItemsPerPage());
		return $this->createSmartObjects($selection);
	}

	/**
	 * 
	 * @param type $limit
	 * @return Article[]
	 */
	public function getHomepage($limit = 4) {
		$sel = $this->getSelectionArticles(true);
		$curdate = new DateTime();
		$sel->limit($limit);
		return $this->createSmartObjects($sel);
	}

	/**
	 * 
	 * @return int
	 */
	public function countActiveArticles() {
		return $this->getSelectionArticles()->count();
	}

	public function countAdricles() {
		return $this->getSelectionArticles(false)->count();
	}

	/**
	 * 
	 * @return \Nette\Database\Table\Selection
	 */
	private function getSelectionArticles($active = true) {
		$curdate = new DateTime();
		$selection = $this->table()
				->where(self::COLUMN_PUBLISHED_AT . ' <= DATE(?)', $curdate)
				->where(self::COLUMN_PERSONALITY_ID . ' IS NULL')
				->order(self::COLUMN_PUBLISHED_AT . ' DESC');
		if ($active) {
			$selection->where(self::COLUMN_ACTIVE, true);
		}
		return $selection;
	}

	/**
	 * 
	 * @param \Nette\Utils\Paginator $paginator
	 * @return Article[]
	 */
	public function getInterviews(\Nette\Utils\Paginator $paginator) {
		$selection = $this->getSelectionInterviews()->page($paginator->getPage(), $paginator->getItemsPerPage());
		return $this->createSmartObjects($selection);
	}
	
	public function getHomepageInterviews($limit = 2){
		$sel = $this->getSelectionInterviews(true);
		$curdate = new DateTime();
		$sel->limit($limit);
		return $this->createSmartObjects($sel);
	}

	/**
	 * 
	 * @return int
	 */
	public function countActiveInterviews() {
		return $this->getSelectionInterviews()->count();
	}

	/**
	 * 
	 * @return int
	 */
	public function countInterviews() {
		return $this->getSelectionInterviews(false)->count();
	}

	/**
	 * 
	 * @return \Nette\Database\Table\Selection
	 */
	private function getSelectionInterviews($active = true) {
		$curdate = new DateTime();
		$sel = $this->table()
				->where(self::COLUMN_PUBLISHED_AT . ' <= DATE(?)', $curdate)
				->where(self::COLUMN_PERSONALITY_ID . ' IS NOT NULL')
				->order(self::COLUMN_PUBLISHED_AT . ' DESC');
		if ($active) {
			$sel->where(self::COLUMN_ACTIVE, $active);
		}
		return $sel;
	}

	/**
	 * 
	 * @param type $seokey
	 * @return Article
	 */
	public function getBySeokey($seokey) {
		$sel = $this->table()->where(self::COLUMN_SEOKEY, $seokey);
		return $this->createSmartObject($sel->fetch());
	}

	/**
	 * 
	 * @param type $id
	 * @return Article[]
	 */
	public function getArticlesByPersonalityId($id, \Extensions\Paginator $paginator) {
		$sel = $this->getSelectionArticlesByPersonalityId($id);
		$sel->page($paginator->getPage(), $paginator->getItemsPerPage());
		return $this->createSmartObjects($sel);
	}

	/**
	 * 
	 * @param type $id
	 * @return int
	 */
	public function countArticlesByPersonalityId($id) {
		return $this->getSelectionArticlesByPersonalityId($id)->count();
	}

	/**
	 * 
	 * @param type $id
	 * @return \Nette\Database\Table\Selection
	 */
	private function getSelectionArticlesByPersonalityId($id) {
		return $this->table()
						->where(self::COLUMN_PERSONALITY_ID, $id)
						->where(self::COLUMN_ACTIVE, true)
						->order(self::COLUMN_PUBLISHED_AT . ' DESC');
	}

	/**
	 * 
	 * @return Article
	 */
	public function getActiveArticles() {
		return $this->createSmartObjects($this->getSelectionArticles());
	}

	/**
	 * 
	 * @return Article
	 */
	public function getActiveInterviews() {
		return $this->createSmartObjects($this->getSelectionInterviews());
	}

}
