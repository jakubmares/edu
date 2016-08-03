<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace App\Model;

use App\Model\So\Term,
	Extensions\Date;

/**
 * Description of TermManager
 *
 * @author jakubmares
 */
class TermManager extends BaseManager {

	const TABLE_NAME = 'term',
			COLUMN_ID = 'id',
			COLUMN_COURSE_ID = 'course_id',
			COLUMN_DISCOUNT = 'discount',
			COLUMN_EXTERNAL_ID = 'external_id',
			COLUMN_ACTIVE = 'active',
			COLUMN_FROM = 'from',
			COLUMN_TO = 'to',
			COLUMN_NOTERM = 'noterm',
			COLUMN_PRICE_FLAG = 'price_flag',
			COLUMN_PRICE = 'price',
			COLUMN_CURRENCY = 'currency',
			COLUMN_VAT = 'vat',
			COLUMN_ADDRESS_NOTE = 'address_note',
			COLUMN_CITY = 'city',
			COLUMN_STREET = 'street',
			COLUMN_REGISTRY_NUMBER = 'registry_number',
			COLUMN_HOUSE_NUMBER = 'house_number',
			COLUMN_ZIP = 'zip',
			COLUMN_ADDRESS_FLAG = 'address_flag',
			COLUMN_LATITUDE = 'latitude',
			COLUMN_LONGITUDE = 'longitude',
			COLUMN_COUNTRY_KEY = 'country_key',
			COLUMN_LECTOR_FIRSTNAME = 'lector_firstname',
			COLUMN_LECTOR_SURNAME = 'lector_surname',
			COLUMN_LECTOR_DEGREES_BEFORE = 'lector_degrees_before',
			COLUMN_LECTOR_DEGREES_AFTER = 'lector_degrees_after',
			COLUMN_LECTOR_DESCRIPTION = 'lector_description',
			COLUMN_LECTOR_SKILLS = 'lector_skills',
			COLUMN_LECTOR_IMAGE = 'lector_image',
			//ALLOWED_TAGS
			ALLOWED_TAGS_LECTOR_DESCRIPTION = '<strong><p><ul><li>',
			ALLOWED_TAGS_LECTOR_SKILLS = '<strong><p><ul><li>',
			//IMAGE
			IMAGE_WIDTH = '220',
			IMAGE_HEIGHT = '220',
			IMAGE_PATH = 'images/lector',
			IMAGE_PREFIX = 'img-';

	public function insertTerm(\Nette\Utils\ArrayHash $values) {
		$desc = strip_tags($values->offsetGet(self::COLUMN_LECTOR_DESCRIPTION), self::ALLOWED_TAGS_LECTOR_DESCRIPTION);
		$values->offsetSet(self::COLUMN_LECTOR_DESCRIPTION, $desc);
		$skills = strip_tags($values->offsetGet(self::COLUMN_LECTOR_SKILLS), self::ALLOWED_TAGS_LECTOR_SKILLS);
		$values->offsetSet(self::COLUMN_LECTOR_SKILLS, $skills);
		return $this->insert($values);
	}

	public function update($id, \Nette\Utils\ArrayHash $values) {
		$desc = strip_tags($values->offsetGet(self::COLUMN_LECTOR_DESCRIPTION), self::ALLOWED_TAGS_LECTOR_DESCRIPTION);
		$values->offsetSet(self::COLUMN_LECTOR_DESCRIPTION, $desc);
		$skills = strip_tags($values->offsetGet(self::COLUMN_LECTOR_SKILLS), self::ALLOWED_TAGS_LECTOR_SKILLS);
		$values->offsetSet(self::COLUMN_LECTOR_SKILLS, $skills);
		return parent::update($id, $values);
	}

	public function copy($id, \Nette\Utils\ArrayHash $values) {
		$term = $this->get($id);
		if ($term->getLectorImage()) {
			$filePath = sprintf('%s/%s.%s', self::IMAGE_PATH, uniqid(self::IMAGE_PREFIX), self::IMG_TYPE);
			copy($term->getLectorImage(), $filePath);
			$values->offsetSet(self::COLUMN_LECTOR_IMAGE, $filePath);
		}
		return $this->insertTerm($values);
	}

	public function delete($id) {
		$row = $this->getRow($id);
		if (file_exists(ltrim(self::COLUMN_LECTOR_IMAGE, '/'))) {
			unlink(ltrim($row->offsetGet(self::COLUMN_LECTOR_IMAGE), '/'));
		}
		return $row->delete();
	}

	/**
	 * 
	 * @param type $id
	 * @return Term
	 */
	public function getActive($id) {
		return $this->createSmartObject($this->table()->where(self::COLUMN_ID, $id)->where(self::COLUMN_ACTIVE, true)->fetch());
	}

	/**
	 * 
	 * @return Term[]
	 */
	public function getTermsLastminuteHp() {
		$res = [];
		$ret = [];
		foreach ($this->getSelectionLastminute() as $termRow) {
			$courseRow = $termRow->ref('course');
			$res[$courseRow->company_id][] = $termRow;
			if (count($res[$courseRow->company_id]) <= 3) {
				$ret[] = $this->createSmartObject($termRow);
			}
		}
		return $ret;
	}

	/**
	 * 
	 * @param \Extensions\Paginator $paginator
	 * @return Term[]
	 */
	public function getTermsLastminute(\Extensions\Paginator $paginator) {
		$curdate = new Date();
		$toDate = new Date();
		$toDate->add(Term::getLastminuteInterval());
		$sel = $this->table()
				->where('discount > ?', 0)
				->where('from > ?', $curdate)
				->where('.course.company.active', true)
				->where(self::TABLE_NAME . '.' . self::COLUMN_ACTIVE, true)
				->where('.course.active', true)
				->order('from, discount DESC');
		$sel->page($paginator->getPage(), $paginator->getItemsPerPage());
		return $this->createSmartObjects($sel);
	}

	/**
	 * 
	 * @return int
	 */
	public function countTermsLastminute() {
		return $this->getSelectionLastminute()->count();
	}

	/**
	 * 
	 * @return \Nette\Database\Table\Selection
	 */
	public function getSelectionLastminute() {
		$curdate = new Date();
		$toDate = new Date();
		$toDate->add(Term::getLastminuteInterval());
		return $this->table()
						->where('discount > ?', 0)
						->where('from > ? AND from <= ?', $curdate, $toDate)
						->where('.course.company.active', true)
						->where(self::TABLE_NAME . '.' . self::COLUMN_ACTIVE, true)
						->where('.course.active', true)
						->order('from, discount DESC');
	}

	public function deleteExternalTermsByCourseId($courseId) {
		return $this->table()->where(self::COLUMN_COURSE_ID, $courseId)->where('external_id IS NOT NULL')->delete();
	}

	public function insertCourseTerms($terms) {
		$data = [];
		foreach ($terms as $term) {
			$desc = strip_tags($term->offsetGet(self::COLUMN_LECTOR_DESCRIPTION), self::ALLOWED_TAGS_LECTOR_DESCRIPTION);
			$term->offsetSet(self::COLUMN_LECTOR_DESCRIPTION, $desc);
			$skills = strip_tags($term->offsetGet(self::COLUMN_LECTOR_SKILLS), self::ALLOWED_TAGS_LECTOR_SKILLS);
			$term->offsetSet(self::COLUMN_LECTOR_SKILLS, $skills);
			$data[] = $term;
		}
		return $this->insert($data);
	}

	/**
	 * 
	 * @param type $id
	 * @return term[]
	 */
	public function getTermsByCourseId($id) {
		return $this->createSmartObjects($this->table()->where(self::COLUMN_COURSE_ID, $id)->order(self::COLUMN_FROM . ',' . self::COLUMN_TO));
	}

	/**
	 * 
	 * @param type $id
	 * @return term[]
	 */
	public function getActiveTermsByCourseId($id) {
		$now = new \Nette\Utils\DateTime();
		return $this->createSmartObjects($this->table()
								->where(self::COLUMN_FROM . ' >= DATE(?)', $now)
								->where(self::COLUMN_ACTIVE, true)
								->where(self::COLUMN_COURSE_ID, $id)
								->order(self::COLUMN_FROM . ',' . self::COLUMN_TO));
	}

	/**
	 * 
	 * @param type $term
	 * @param type $address
	 * @param type $dateFrom
	 * @param type $dateTo
	 * @return Term[]
	 */
	public function getByTerm(\Extensions\Paginator $paginator, $term, $address, $dateFrom, $dateTo) {
		$selection = $this->getSelection($term, $address, $dateFrom, $dateTo);
		$selection->page($paginator->getPage(), $paginator->getItemsPerPage());
		return $this->createSmartObjects($selection);
	}

	/**
	 * 
	 * @param type $term
	 * @param type $address
	 * @param type $dateFrom
	 * @param type $dateTo
	 * @return int
	 */
	public function countByTerm($term, $address, $dateFrom, $dateTo) {
		return $this->getSelection($term, $address, $dateFrom, $dateTo)->count();
	}

	/**
	 * 
	 * @param type $id
	 * @param \Extensions\Paginator $paginator
	 * @param type $term
	 * @param type $address
	 * @param type $dateFrom
	 * @param type $dateTo
	 * @return Term[]
	 */
	public function getByTermByCategoryId($id, \Extensions\Paginator $paginator, $term, $address, $dateFrom, $dateTo) {
		$selection = $this->getSelection($term, $address, $dateFrom, $dateTo);
		$selection->where('.course:course_focus.focus.category_id', $id);
		$selection->page($paginator->getPage(), $paginator->getItemsPerPage());
		return $this->createSmartObjects($selection);
	}

	/**
	 * 
	 * @param type $id
	 * @param type $term
	 * @param type $address
	 * @param type $dateFrom
	 * @param type $dateTo
	 * @return int
	 */
	public function countByTermByCategoryId($id, $term, $address, $dateFrom, $dateTo) {
		$selection = $this->getSelection($term, $address, $dateFrom, $dateTo);
		$selection->where('.course:course_focus.focus.category_id', $id);
		return $selection->count();
	}

	public function getByTermByFocusId($id, \Extensions\Paginator $paginator, $term, $address, $dateFrom, $dateTo) {
		$selection = $this->getSelection($term, $address, $dateFrom, $dateTo);
		$selection->where('.course:course_focus.focus_id', $id);
		$selection->page($paginator->getPage(), $paginator->getItemsPerPage());
		return $this->createSmartObjects($selection);
	}

	public function countByTermByFocusId($id, $term, $address, $dateFrom, $dateTo) {
		$selection = $this->getSelection($term, $address, $dateFrom, $dateTo);
		$selection->where('.course:course_focus.focus_id', $id);
		return $selection->count();
	}

	private function getSelection($term, $address, $dateFrom, $dateTo) {
		$curdate = new \Extensions\Date();
		$dateFrom = $dateFrom < $curdate ? $curdate : $dateFrom;
		$selection = $this->table()
				->where(self::TABLE_NAME . '.' . self::COLUMN_ACTIVE, true)
				->where('(from >= DATE(?)) OR noterm = ?', $dateFrom, true)
				->where('course.active', true)
				->where('course.company.active', true);

		if ($address) {
			$selection->where('CONCAT(street,", ",city," ",zip) LIKE', "%$address%");
		}
		if ($dateTo) {
			$selection->where('to <= DATE(?)', $dateTo);
		}

		if ($term) {
			$selection->where('course.name LIKE ?', '%' . $term . '%');
		}

		$selection->order('noterm,from , to');
		return $selection;
	}

	/**
	 * 
	 * @param type $id
	 * @param \Extensions\Paginator $paginator
	 * @return Term[]
	 */
	public function getTermsByCompanyId($id, \Extensions\Paginator $paginator) {
		$sel = $this->getSelectionTermsByCompanyId($id);
		$sel->page($paginator->getPage(), $paginator->getItemsPerPage());
		return $this->createSmartObjects($sel);
	}

	/**
	 * 
	 * @param type $id
	 * @return int
	 */
	public function countTermsByCompanyId($id) {
		return $this->getSelectionTermsByCompanyId($id)->count();
	}

	public function getSelectionTermsByCompanyId($id) {
		return $this->table()
						->where(self::TABLE_NAME . '.' . self::COLUMN_ACTIVE, true)
						->where('.course.active', true)
						->where('.course.company_id', $id)
						->order('from , to');
	}

	public function countActive() {
		return $this->table()->where(self::COLUMN_ACTIVE, true)->count();
	}

}
