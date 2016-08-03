<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace App\Model;
use Nette\Database;
/**
 * Description of Db1
 *
 * @author jakubmares
 */
class Db2 {

	/**
	 *
	 * @var Database\Context
	 */
	private $database;

	function __construct(Database\Connection $database,  Database\IStructure $structure) {
		$this->database = new Database\Context($database,$structure);
	}
	
	public function table($name){
		return $this->database->table($name);
	}
	
	/**
	 * 
	 * @return Database\Context
	 */
	public function getDb(){
		return $this->database;
	}

}
