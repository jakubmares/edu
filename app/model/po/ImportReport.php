<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace App\Model\Po;

use App\Model\ImportManager;

/**
 * Description of ImportReport
 *
 * @author jakubmares
 */
class ImportReport extends BasePo {

	private $courseImport;
	private $courseUpdate;
	private $termImport;
	private $notermImport;

	/** @var \Exception[] */
	private $exceptions;
	private $isError;
	private $time;
	private $parseXmlTime;

	public function __construct() {
		$this->courseImport = 0;
		$this->courseUpdate = 0;
		$this->termImport = 0;
		$this->time = 0;
		$this->parseXmlTime = 0;
		$this->notermImport = 0;
		$this->isError = false;
	}

	public function addCourseImport($count = 1) {
		$this->courseImport += $count;
	}

	public function addCourseUpdate($count = 1) {
		$this->courseUpdate += $count;
	}

	public function addTermImport($count = 1) {
		$this->termImport += $count;
	}

	public function addNotermImport($count = 1) {
		$this->notermImport += $count;
	}
	
	public function addException(\Exception  $ex){
		$this->exceptions[] = $ex;
		$this->isError = true;
	}

	public function setException(\Exception $exception) {
		$this->exceptions[] = $exception;
		$this->isError = true;
	}

	public function setTime($time) {
		$this->time = $time;
	}

	public function setParseXmlTime($parseXmlTime) {
		$this->parseXmlTime = $parseXmlTime;
	}

	public function getExecNote() {
		return $this->time . ' | ' . $this->parseXmlTime;
	}

	public function getLog() {
		$ret = '';
		if ($this->isError) {
			
			foreach ($this->exceptions as $ex) {
				$ret .= sprintf('Code: %s, Message: %s, Trace: %s |',$ex->getCode(),$ex->getMessage(),$ex->getTraceAsString());
			}
		} else {
			$ret = sprintf('Celkem importovano %s kurzu, z toho novych %s. Importovano %s terminu a %s NeTerminu.',
					$this->courseImport + $this->courseUpdate, $this->courseImport, $this->termImport, $this->notermImport);
		}
		return $ret;
	}

	public function arrayForInsert($companyId) {
		$now = new \Nette\Utils\DateTime();
		$array = new \Nette\Utils\ArrayHash();
		$array->offsetSet(ImportManager::TABLE_COLUMN_IMPORT_DATE, $now);
		$array->offsetSet(ImportManager::TABLE_COLUMN_COMPANY_ID, $companyId);
		$array->offsetSet(ImportManager::TABLE_COLUMN_EXEC_NOTE, $this->getExecNote());
		$array->offsetSet(ImportManager::TABLE_COLUMN_LOG, $this->getLog());
		return $array;
	}
	
	public function __toString(){
		return $this->getLog();
	}

}
