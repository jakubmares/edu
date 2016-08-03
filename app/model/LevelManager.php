<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace App\Model;
use App\Model\So;
/**
 * Description of LevelManager
 *
 * @author jakubmares
 */
class LevelManager extends BaseManager {

	const TABLE_NAME = 'level',
			COLUMN_ID = 'id',
			COLUMN_NAME = 'name';

	
	
	/**
	 * 
	 * @param type $id
	 * @return So\Level[]
	 */
	public function getByCourseId($id){
		$selection = $this->table()->where(':course_level.course_id',$id);
		return $this->createSmartObjects($selection);
	}
}
