<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace App\Model\So;

use Extensions\Date,
	App\Model;

/**
 * Description of Course
 *
 * @author jakubmares
 */
class Course extends SmartObject {

	private $id;
	private $externalId;
	private $name;
	private $description;
	private $retraining;
	private $seokey;
	private $companyId;
	private $languageId;
	private $active;
	private $linkUrl;

	public function getId() {
		return $this->id;
	}

	public function getExternalId() {
		return $this->externalId;
	}

	public function getName() {
		return $this->name;
	}

	public function getDescription() {
		return $this->description;
	}

	public function getRetraining() {
		return $this->retraining;
	}

	public function getSeokey() {
		return $this->seokey;
	}

	public function getCompanyId() {
		return $this->companyId;
	}

	public function getLanguageId() {
		return $this->languageId;
	}

	public function getActive() {
		return $this->active;
	}

	public function getLinkUrl() {
		return $this->linkUrl;
	}

	/**
	 * 
	 * @return Company
	 */
	public function getCompany() {
		return $this->getRef(Model\CompanyManager::TABLE_NAME);
	}

	/**
	 * 
	 * @return Language
	 */
	public function getLanguage() {
		return $this->getRef(Model\LanguageManager::TABLE_NAME);
	}

	/**
	 * 
	 * @return Term[]
	 */
	public function getTerms() {
		$selection = $this->row->related(Model\TermManager::TABLE_NAME);
		$selection->where(Model\TermManager::COLUMN_ACTIVE, true);
		$selection->order(Model\TermManager::COLUMN_FROM);
		return self::createList($selection);
	}

	public function findTerms($from, $to, $active = true) {
		$selection = $this->row->related(sModel\TermManager::TABLE_NAME);
		$curdate = new Date();
		$from = $from < $curdate ? $curdate : $from;
		$selection->where(Model\TermManager::COLUMN_FROM . ' >= DATE(?)', $from);
		$selection->where(Model\TermManager::COLUMN_ACTIVE, true);

		if ($to) {
			$selection->where(Model\TermManager::COLUMN_TO . ' <= DATE(?)', $to);
		}
		$selection->order(Model\TermManager::COLUMN_FROM);
		return self::createList($selection);
	}

	public function getTermsLastminute() {
		$curdate = new Date();
		$toDate = new Date();
		$toDate->add(Term::getLastminuteInterval());
		$selection = $this->row->related(Model\TermManager::TABLE_NAME)
				->where(Model\TermManager::COLUMN_ACTIVE, true)
				->where(Model\TermManager::COLUMN_FROM . ' >= ? AND ', (string) $curdate)
				->where(Model\TermManager::COLUMN_FROM . ' <= ?', (string) $toDate)
				->where(Model\TermManager::COLUMN_DISCOUNT . ' > ?', 0);
		return self::createList($selection);
	}

	public function getFocusesId() {
		$ret = [];
		foreach ($this->row->related(Model\CourseFocusManager::TABLE_NAME, Model\CourseFocusManager::COLUMN_COURSE_ID) as $row) {
			$ret[] = $row->focus_id;
		}
		return $ret;
	}

	public function getKeywords() {
		return $this->getRelated(Model\CourseKeywordManager::TABLE_NAME);
	}

	public function getLevels() {
		$ret = [];
		foreach ($this->row->related(Model\CourseLevelManager::TABLE_NAME) as $row) {
			$ret[] = self::create($row->ref(Model\LevelManager::TABLE_NAME));
		}
		return $ret;
	}

	public function getLevelsId() {
		$ret = [];
		foreach ($this->row->related(Model\CourseLevelManager::TABLE_NAME) as $row) {
			$ret[] = $row->offsetGet(Model\CourseLevelManager::COLUMN_LEVEL_ID);
		}
		return $ret;
	}

}
