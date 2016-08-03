<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace App\Model\So;

/**
 * Description of Import
 *
 * @author jakubmares
 */
class Import extends SmartObject{
	
	private $id;
	private $companyId;
	private $importDate;
	private $log;
	private $execNote;
	
	public function getId() {
		return $this->id;
	}

	public function getCompanyId() {
		return $this->companyId;
	}

	public function getImportDate() {
		return $this->importDate;
	}

	public function getLog() {
		return $this->log;
	}

	public function getExecNote() {
		return $this->execNote;
	}


}
