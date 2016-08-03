<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace App\Model;

use Nette\Utils\ArrayHash,
	App\Model\So\Company,
	Extensions\Paginator,
	Nette\Utils\DateTime;

/**
 * Description of CompanyManager
 *
 * @author jakubmares
 */
class CompanyManager extends BaseManager {

	const
	//TABLE
			TABLE_NAME = 'company',
			//COLUMNS
			COLUMN_ID = 'id',
			COLUMN_NAME = 'name',
			COLUMN_SEOKEY = 'seokey',
			COLUMN_IC = 'ic',
			COLUMN_DIC = 'dic',
			COLUMN_DESCRIPTION = 'description',
			COLUMN_ACTIVE = 'active',
			COLUMN_USER_ID = 'user_id',
			COLUMN_CREATED_AT = 'created_at',
			COLUMN_DEALER_ID = 'dealer_id',
			COLUMN_LOGO = 'logo',
			COLUMN_PARTNER = 'partner',
			COLUMN_WEB = 'web',
			COLUMN_IMPORT_URL = 'import_url',
			COLUMN_IMAPORT_AT = 'import_at',
			COLUMN_TOP = 'top',
			COLUMN_STATUS = 'status',
			COLUMN_POTENCIAL = 'potencial',
			COLUMN_TYPE = 'type',
			COLUMN_BANK_ACCOUNT = 'bank_account',
			COLUMN_NOTICE = 'notice',
			//IMAGE
			IMAGE_WIDTH = '100',
			IMAGE_HEIGHT = '100',
			IMAGE_PATH = '/images/company',
			IMAGE_PREFIX = 'logo-';

	public function getCompanies() {
		return $this->getAll();
	}

	public function isSeokeyExist($id, $key) {
		return (boolean) $this->table()->where(self::COLUMN_ID . ' != ?', $id)->where(self::COLUMN_SEOKEY, $key)->fetch();
	}

	public function insert($values) {
		$values->offsetSet(self::COLUMN_SEOKEY, $this->generateUniqName($values->offsetGet(self::COLUMN_NAME)));
		$values->offsetSet(self::COLUMN_CREATED_AT, new DateTime());
		return parent::insert($values);
	}

	public function update($id, ArrayHash $values) {
		if ($values->offsetExists(self::COLUMN_SEOKEY)) {
			$values->offsetSet(self::COLUMN_SEOKEY, $this->generateUniqName($values->offsetGet(self::COLUMN_NAME), $id));
		}

		return parent::update($id, $values);
	}

	/**
	 * 
	 * @return Company[]
	 */
	public function getTopCompanies() {
		return $this->createSmartObjects($this->table()->where(self::COLUMN_TOP, true)->where(self::COLUMN_ACTIVE, true));
	}

	/**
	 * 
	 * @param type $id
	 * @return bool
	 */
	public function deleteLogo($id) {
		$row = $this->getRow($id);
		unlink(ltrim($row->offsetGet(self::COLUMN_LOGO),'/'));
		$data = $row->toArray();
		unset($data[self::COLUMN_LOGO]);
		return $row->update($data);
	}

	/**
	 * 
	 * @param type $id
	 * @return Company
	 */
	public function get($id) {
		return parent::get($id);
	}

	/**
	 * 
	 * @param type $id
	 * @return Company[]
	 */
	public function getByUserId($id) {
		return $this->createSmartObjects($this->table()->where(self::COLUMN_USER_ID . ' = ?', $id));
	}

	/**
	 * 
	 * @param type $id
	 * @return Company[]
	 */
	public function getByDealerId($id) {
		return $this->createSmartObjects($this->table()->where(self::COLUMN_DEALER_ID . ' = ?', $id));
	}

	/**
	 * 
	 * @return Company[]
	 */
	public function getPartners() {
		$selection = $this->table()->where(self::COLUMN_ACTIVE, true)->where(self::COLUMN_PARTNER, true);
		return $this->createSmartObjects($selection);
	}

	/**
	 * 
	 * @param type $seokey
	 * @return Company[]
	 */
	public function getBySeokey($seokey) {
		return $this->createSmartObject($this->table()->where(self::COLUMN_SEOKEY, $seokey)->fetch());
	}

	/**
	 * 
	 * @return Company
	 */
	public function getForImport() {
		$curdate = new DateTime();
		$selection = $this->table()
				->where(self::COLUMN_IMPORT_URL . ' <> ""')
				->where(self::COLUMN_ACTIVE, true)
				->where('DATE(' . self::COLUMN_IMAPORT_AT . ') < DATE(?) OR ' . self::COLUMN_IMAPORT_AT . ' IS NULL', $curdate);
		return $this->createSmartObject($selection->fetch());
	}

	public function getByTerm(Paginator $paginator, $term = '', $address = '') {
		$selection = $this->getSelectionByTerm($term, $address);
		$selection->page($paginator->getPage(), $paginator->getItemsPerPage());
		return $this->createSmartObjects($selection);
	}

	public function getCountByTerm($term = '', $address = '') {
		$selection = $this->getSelectionByTerm($term, $address);
		return $selection->count();
	}

	public function getSelectionByTerm($term, $address) {
		$selection = $this->table()->where(self::COLUMN_ACTIVE, true);
		if ($term) {
			$selection->where(self::COLUMN_NAME . ' LIKE ?', "%$term%");
		}

		if ($address) {
			$selection->where('CONCAT(:' . AddressManager::TABLE_NAME . '.' . AddressManager::COLUMN_STREET . ' , :' . AddressManager::TABLE_NAME . '.' . AddressManager::COLUMN_CITY . '," ",:' . AddressManager::TABLE_NAME . '.' . AddressManager::COLUMN_ZIP . ') LIKE',
					"%$address%");
		}

		$selection->order(self::COLUMN_NAME);
		return $selection;
	}

	/**
	 * 
	 * @param type $id
	 * @return Company[]
	 */
	public function getCategoryPartners($id) {
		$selection = $this->table()
				->where(self::COLUMN_ACTIVE, true)
				->where(':company_category.category_id', $id);
		return $this->createSmartObjects($selection);
	}

	public function getFocusPartners($id) {
		$selection = $this->table()
				->where(self::COLUMN_ACTIVE, true)
				->where(':company_focus.focus_id', $id);
		return $this->createSmartObjects($selection);
	}

	/**
	 * 
	 * @return int
	 */
	public function countActive() {
		return $this->table()->where(self::COLUMN_ACTIVE, true)->count();
	}

	/**
	 * 
	 * @return Company[]
	 */
	public function getActive() {
		return $this->createSmartObjects($this->table()->where(self::COLUMN_ACTIVE, true));
	}

	public function isAllow($id, $userId) {
		$sel = $this->table()->where('user_id = ? OR dealer_id = ?', $userId, $userId)->where(self::COLUMN_ID, $id);
		return $sel->fetch() ? true : false;
	}

}
