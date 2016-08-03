<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace App\Model;

use Nette\Utils\ArrayHash,
	App\Model\CourseKeywordManager,
	Nette\Database\Context,
	App\Model\So\Course,
	Nette\Utils\DateTime,
	App\Model\So\Term,
	Nette\Utils\Image,
	Extensions\Paginator;

/**
 * Description of CourseManager
 *
 * @author jakubmares
 */
class CourseManager extends BaseManager {

	const
			TABLE_NAME = 'course',
			COLUMN_SEOKEY = 'seokey',
			COLUMN_ID = 'id',
			COLUMN_COMPANY_ID = 'company_id',
			COLUMN_NAME = 'name',
			COLUMN_DESCRIPTION = 'description',
			COLUMN_IMAGE = 'image',
			COLUMN_VIDEO = 'video',
			COLUMN_ACTIVE = 'active',
			COLUMN_RETRAINING = 'retraining',
			COLUMN_LANGUAGE_ID = 'language_id',
			COLUMN_LINK_URL = 'link_url',
			//RALATED course_focus
			TABLE_COURSE_FOCUS_NAME = 'course_focus',
			TABLE_COURSE_FOCUS_COLUMN_COURSE_ID = 'course_id',
			TABLE_COURSE_FOCUS_COLUMN_FOCUS_ID = 'focus_id',
			//RELATED course_level
			TABLE_COURSE_LEVEL_NAME = 'course_level',
			TABLE_COURSE_LEVEL_COLUMN_COURSE_ID = 'course_id',
			TABLE_COURSE_LEVEL_COLUMN_LEVEL_ID = 'level_id',
			//
			DESCRIPTION_ALLOWED_TAGS = '<strong><p><ul><li>',
			//IMAGE SETTINGS
			IMAGE_WIDTH = '540',
			IMAGE_HEIGHT = '405',
			IMAGE_PATH = '/images/course',
			IMAGE_PREFIX = 'course-',
			//COURSE PROPERTY
			PROPERTY_FOUCUSES_ID = 'focusesId',
			PROPERTY_LEVELS_ID = 'levelsId',
			PROPERTY_KEYWORDS = 'keywords',
			UNIQUE_NAME_LENGHT = 70;

	/** @var CourseKeywordManager */
	private $keywordMan;

	/** @var CourseLevelManager */
	private $courseLevelMan;

	public function __construct(Context $database, CourseKeywordManager $keywordMan, CourseLevelManager $courseLevelMan) {
		parent::__construct($database);
		$this->keywordMan = $keywordMan;
		$this->courseLevelMan = $courseLevelMan;
	}

	public function insertCourse(ArrayHash $values) {
		$seokey = \Extensions\StringHelper::removeSpecialChars($values->offsetGet(self::COLUMN_NAME));
		$values->offsetSet(self::COLUMN_SEOKEY, \Nette\Utils\Strings::webalize($seokey));
		$values->offsetSet(self::COLUMN_DESCRIPTION, strip_tags($values->offsetGet(self::COLUMN_DESCRIPTION)));

		if ($this->table()->where(self::COLUMN_SEOKEY, $values->offsetGet(self::COLUMN_SEOKEY))->fetch()) {
			$c = $this->database->table(CompanyManager::TABLE_NAME)->get($values->offsetGet(self::COLUMN_COMPANY_ID));
			$s = $values->offsetGet(self::COLUMN_NAME) . '-' . $c->offsetGet(CompanyManager::COLUMN_NAME);
			$values->offsetSet(self::COLUMN_SEOKEY, \Nette\Utils\Strings::webalize($s));
		}

		if ($this->table()->where(self::COLUMN_SEOKEY, $values->offsetGet(self::COLUMN_SEOKEY))->fetch()) {
			$seokey = $this->generateUniqName($values->offsetGet(self::COLUMN_SEOKEY));
			$values->offsetSet(self::COLUMN_SEOKEY, $seokey);
		}

		return $this->insert($values);
	}

	public function insertWithLevels($array, $levelsId) {
		$this->database->beginTransaction();
		try {
			$ret = $this->insertCourse($array);
			$this->courseLevelMan->insertByCourseId($levelsId, $ret->offsetGet(self::COLUMN_ID));
			$this->database->commit();
		} catch (Exception $exc) {
			$this->database->rollBack();
			throw $exc;
		}
		return $ret;
	}

	public function updateWithLevels($id, $values, $levelsId) {
		$this->courseLevelMan->deleteByCourseId($id);
		$this->database->beginTransaction();
		try {
			$ret = $this->update($id, $values);
			$this->courseLevelMan->insertByCourseId($levelsId, $id);
			$this->database->commit();
		} catch (Exception $exc) {
			$this->database->rollBack();
			throw $exc;
		}
		return $ret;
	}

	/**
	 * 
	 * @param type $id
	 * @return Course[]
	 */
	public function getCoursesByCompanyId($id) {
		return $this->createSmartObjects($this->getSelectionByCompanyId($id));
	}

	/**
	 * 
	 * @param type $id
	 * @return \Nette\Database\Table\Selection
	 */
	public function getSelectionByCompanyId($id) {
		return $this->table()->where(self::COLUMN_COMPANY_ID, $id);
	}

	/**
	 * 
	 * @param type $id
	 * @return Course[]
	 */
	public function getCoursesActiveByCompanyId($id, Paginator $paginator) {
		$selection = $this->getSelectionCoursesActiveByCompanyId($id);
		$selection->limit($paginator->getLength(), $paginator->getOffset());
		return $this->createSmartObjects($selection);
	}

	/**
	 * 
	 * @param type $id
	 * @return int
	 */
	public function getCoursesCountActiveByCompanyId($id) {
		return $this->getSelectionCoursesActiveByCompanyId($id)->count();
	}

	/**
	 * 
	 * @param type $id
	 * @return \Nette\Database\Table\Selection
	 */
	public function getSelectionCoursesActiveByCompanyId($id) {
		return $this->table()->where(self::COLUMN_COMPANY_ID, $id)->where(self::COLUMN_ACTIVE, true);
	}

	/**
	 * 
	 * @param Paginator $paginator
	 * @param string $term
	 * @param DateTime $dateFrom
	 * @param DateTime $dateTo
	 * @return Course[]
	 */
	public function getCoursesByTerm(Paginator $paginator, $term = '', $address = '', $dateFrom = null, $dateTo = null) {
		$selection = $this->getCoursesSelection($term, $address, $dateFrom, $dateTo);
		$selection->limit($paginator->getLength(), $paginator->getOffset());
		return $this->createSmartObjects($selection);
	}

	/**
	 * 
	 * @param string $term
	 * @param DateTime $dateFrom
	 * @param type $dateTo
	 * @return int
	 */
	public function getCoursesCountByTerm($term = '', $address = '', $dateFrom = null, $dateTo = null) {
		$selection = $this->getCoursesSelection($term, $address, $dateFrom, $dateTo);
		return $selection->count(sprintf('DISTINCT %s.%s', self::TABLE_NAME, self::COLUMN_ID));
	}

	/**
	 * 
	 * @param int $categoryId
	 * @param string $term
	 * @param DateTime|null $dateFrom
	 * @param DateTime|null $dateTo
	 * @return Category[]
	 */
	public function getCoursesByCategoryIdTerm(Paginator $paginator, $categoryId, $term = '', $address = '', $dateFrom = null, $dateTo = null) {
		$selection = $this->getCoursesSelection($term, $address, $dateFrom, $dateTo);
		$selection->where(':course_focus.focus.category_id', $categoryId);
		$selection->limit($paginator->getLength(), $paginator->getOffset());
		return $this->createSmartObjects($selection);
	}

	/**
	 * 
	 * @param int $categoryId
	 * @param string $term
	 * @param DateTime|null $dateFrom
	 * @param DateTime|null $dateTo
	 * @return int
	 */
	public function getCoursesCountByCategoryIdTerm($categoryId, $term = '', $address = '', $dateFrom = null, $dateTo = null) {
		$selection = $this->getCoursesSelection($term, $address, $dateFrom, $dateTo);
		$selection->where(':course_focus.focus.category_id', $categoryId);
		return $selection->count(sprintf('DISTINCT %s.%s', self::TABLE_NAME, self::COLUMN_ID));
	}

	/**
	 * 
	 * @param int $focusId
	 * @param string $term
	 * @param DateTime|null $dateFrom
	 * @param DateTime|null $dateTo
	 * @return Course[]
	 */
	public function getCoursesByFocusIdTerm(Paginator $paginator, $focusId, $term = '', $address = '', $dateFrom = null, $dateTo = null) {
		$selection = $this->getCoursesSelection($term, $address, $dateFrom, $dateTo);
		$selection->where(':course_focus.focus_id', $focusId);
		$selection->limit($paginator->getLength(), $paginator->getOffset());
		return $this->createSmartObjects($selection);
	}

	/**
	 * 
	 * @param int $focusId
	 * @param string $term
	 * @param DateTime|null $dateFrom
	 * @param DateTime|null $dateTo
	 * @return int
	 */
	public function getCoursesCountByFocusIdTerm($focusId, $term = '', $address = '', $dateFrom = null, $dateTo = null) {
		$selection = $this->getCoursesSelection($term, $address, $dateFrom, $dateTo);
		$selection->where(':course_focus.focus_id', $focusId);
		return $selection->count(sprintf('DISTINCT %s.%s', self::TABLE_NAME, self::COLUMN_ID));
	}

	/**
	 * 
	 * @param type $term
	 * @param type $dateFrom
	 * @param type $dateTo
	 * @return \Nette\Database\Table\Selection
	 */
	private function getCoursesSelection($term, $address, $dateFrom, $dateTo) {
		$selection = $this->table()->where('.company.active', true);
		$curdate = new \Extensions\Date();
		$dateFrom = $dateFrom < $curdate ? $curdate : $dateFrom;
		$selection->where(':term.active', true)
				->where(':term.from >= DATE(?) ', $dateFrom);

		if ($address) {
			$selection->where('CONCAT(:term.street,", ",:term.city," ",:term.zip) LIKE', "%$address%");
		}
		if ($dateTo) {
			$selection->where(':term.to <= DATE(?)', $dateTo);
		}

		if ($term) {
			$selection->where('course.name LIKE ?', '%' . $term . '%');
		}

		$selection->group('course.id');

		$selection->order('.company.top DESC,:term.from , :term.to');
		return $selection;
	}

	/**
	 * 
	 * @param type $seokey
	 * @return Course
	 * @throws Exception
	 */
	public function getCourseBySeokey($seokey) {
		$row = $this->table()->where(self::COLUMN_SEOKEY, $seokey)->fetch();
		return $this->createSmartObject($row);
	}

	/**
	 * 
	 * @return Course
	 */
	public function getCoursesLastminute() {
		$curdate = new DateTime();
		$toDate = new DateTime();
		$toDate->add(Term::getLastminuteInterval());
		$selection = $this->table()->where(':term.discount > ? AND :term.from > ? AND :term.from <= ?', 0, $curdate, $toDate);
		return $this->createSmartObjects($selection);
	}

	/**
	 * 
	 * @return Course[]
	 */
	public function getActive() {
		return $this->createSmartObjects($this->table()->where('.company.active', true));
	}

	/**
	 * 
	 * @param type $companyId
	 * @param type $externalId
	 * @return Course
	 */
	public function getCourseByExternalId($companyId, $externalId) {
		$row = $this->table()->where('company_id = ? AND external_id = ?', $companyId, $externalId)->fetch();
		return $this->createSmartObject($row);
	}

	public function deactivateCompanyCourses($companyId) {
		return $this->table()->where('company_id = ? AND active = 1 AND external_id IS NOT NULL', $companyId)->update(['active' => false]);
	}

	public function importExternalImages($limit) {
		$selection = $this->table()->where('image LIKE ?', 'http%')->limit($limit);

		foreach ($selection as $row) {
			$image = Image::fromFile($row->offsetGet(self::COLUMN_IMAGE));
			$imagePath = $this->saveImage($image);
			$row->update([self::COLUMN_IMAGE => $imagePath]);
		}
	}

	public function deleteUnusedImages() {
		$images = $this->table()->fetchPairs(self::COLUMN_IMAGE, self::COLUMN_ID);

		foreach (scandir(self::IMAGE_PATH) as $item) {
			if (preg_match('#.png$#', $item) && !array_key_exists(self::IMAGE_PATH . '/' . $item, $images)) {
				unlink(self::IMAGE_PATH . '/' . $item);
			}
		}
	}

	public function countActive() {
		return $this->table()->where(self::COLUMN_ACTIVE, true)->count();
	}

	public function getSelection() {
		return $this->table();
	}

}
