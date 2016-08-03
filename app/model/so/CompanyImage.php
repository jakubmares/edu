<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace App\Model\So;

use App\Model;

/**
 * Description of CompanyImage
 *
 * @author jakubmares
 */
class CompanyImage extends SmartObject {

	private $id;
	private $title;
	private $img;
	private $active;
	private $companyId;

	public function getId() {
		return $this->id;
	}

	public function getTitle() {
		return $this->title;
	}

	public function getImg() {
		return $this->img;
	}

	public function getActive() {
		return $this->active;
	}

	public function getCompanyId() {
		return $this->companyId;
	}

	/**
	 * 
	 * @return Company
	 */
	public function getCompany() {
		return self::create($this->row->ref(Model\CompanyManager::TABLE_NAME));
	}

}
