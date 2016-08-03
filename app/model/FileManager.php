<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace App\Model;
use App\Model\So;
/**
 * Description of FileManager
 *
 * @author jakubmares
 */
class FileManager extends BaseManager{
	const TABLE_NAME = 'file',
			COLUMN_ID = 'id',
			COLUMN_TYPE = 'type',
			COLUMN_PATH = 'path',
			COLUMN_TITLE = 'title',
			COLUMN_USER_ID = 'user_id',
			COLUMN_UPDATED_AT = 'updated_at';
	
	public function delete($id) {
		$row = $this->getRow($id);
		unlink($row->offsetGet(self::COLUMN_PATH));
		return $row->delete();
	}
	
	public function getForDownload(){
		$sel = $this->table()->where(self::COLUMN_TYPE,  So\File::TYPE_DOWNLOAD);
		return $this->createSmartObjects($sel);
	}
	
	/**
	 * 
	 * @return So\File
	 */
	public function getLastPriceList(){
		$sel = $this->table()->where(self::COLUMN_TYPE,  So\File::TYPE_PRICE_LIST)
				->order(self::COLUMN_UPDATED_AT.' DESC');
		return $this->createSmartObject($sel->fetch());
	}

}
