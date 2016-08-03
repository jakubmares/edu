<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace App\Model;

use Extensions,
	ReflectionClass,
	Nette\Object,
	Nette\Database\Context,
	Nette\Database\Table\ActiveRow,
	Nette\Database\Table\Selection,
	Nette\Utils\ArrayHash,
	Nette\Http\FileUpload,
	Nette\Utils\Image,
	App\Model\So\SmartObject,
	Nette\Utils\Strings,
	Nette\Utils\Random;

/**
 * Description of BaseManager
 *
 * @author jakubmares
 */
abstract class BaseManager extends Object {

	const
			UNIQUE_NAME_LENGHT = 70,
			UNIQUE_NAME_POSTFIX_LENGHT = 5,
			IMG_TYPE = 'png';

	/** @var Context */
	protected $database;

	public function __construct(Context $database) {
		$this->database = $database;
	}

	protected function cutCodeFromVideoLink($link) {
		return Extensions\VideoHelper::cutYoutubeCode($link);
	}

	/**
	 * Inserts row in a table.
	 * @param  array|\Traversable|Selection array($column => $value)|\Traversable|Selection for INSERT ... SELECT
	 * @return IRow|int|bool Returns IRow or number of affected rows for Selection or table without primary key
	 */
	public function insert($array) {
		if (count($array) == 0) {
			return false;
		}
		return $this->table()->insert($array);
	}

	/**
	 * 
	 * @param type $id
	 * @param ArrayHash $values
	 * @return bool
	 */
	public function update($id, ArrayHash $values) {
		return $this->getRow($id)->update($values);
	}

	public function delete($id) {
		return $this->getRow($id)->delete();
	}

	/**
	 * 
	 * @return Selection
	 */
	protected function table() {
		return $this->database->table($this->getTableName());
	}

	public function getTableColumns() {
		return $this->database->getStructure()->getColumns($this->getTableName());
	}

	private function getTableName() {
		return $this->reflection->getConstant('TABLE_NAME');
	}

	/**
	 * 
	 * @param type $id
	 * @return SmartObject
	 */
	public function get($id) {
		return $this->createSmartObject($this->getRow($id));
	}

	/**
	 * 
	 * @return SmartObject[]
	 */
	public function getAll() {
		return $this->createSmartObjects($this->getAllRows());
	}

	/**
	 * 
	 * @param type $id
	 * @return ActiveRow
	 */
	public function getRow($id) {
		return $this->table()->get($id);
	}

	/**
	 * 
	 * @return Selection
	 */
	public function getAllRows() {
		return $this->table();
	}

	protected function convertKeysForDb(ArrayHash $params) {
		$ret = [];
		foreach ($params as $key => $value) {
			$ret[Extensions\StringHelper::camelCaseToUnderscore($key)] = $value;
		}
		return $ret;
	}

	/**
	 * 
	 * @param type $row
	 * @param string $tableName
	 * @return SmartObject
	 */
	protected function createSmartObject($row) {
		return SmartObject::create($row);
	}

	/**
	 * 
	 * @param Selection $selection
	 * @param string $tableName
	 * @return SmartObject[]
	 */
	protected function createSmartObjects(Selection $selection) {
		return SmartObject::createList($selection);
	}

	/**
	 * 
	 * @param FileUpload $file
	 * @return string file name with path
	 */
	public function saveFileUpload(FileUpload $file) {
		return $this->saveImage($file->toImage());
	}

	/**
	 * 
	 * @param Image $image
	 * @return string file name with path
	 */
	public function saveImage(Image $image) {
		$width = $this->reflection->getConstant('IMAGE_WIDTH');
		$height = $this->reflection->getConstant('IMAGE_HEIGHT');

		$fileName = uniqid($this->reflection->getConstant('IMAGE_PREFIX'));
		$filePath = sprintf('%s/%s.%s', $this->reflection->getConstant('IMAGE_PATH'), $fileName, self::IMG_TYPE);
		$image->resize($width, $height);
		$image->sharpen();

		if ($width && $height) {
			$blank = Image::fromBlank($width, $height, Image::rgb(255, 255, 255));
			$blank->place($image, '50%', '50%', 100);
			$image = $blank;
		}
		if (!$image->save(ltrim($filePath,'/'), 100, Image::PNG)) {
			$filePath = '';
		}
		return $filePath;
	}

	protected function generateUniqName($name, $id = 0, $iteration = 0) {
		$suffix = $iteration == 0 ? '' : '-' . Random::generate(self::UNIQUE_NAME_POSTFIX_LENGHT - 1);
		$tableColumnSeokey = $this->reflection->getConstant('TABLE_COLUMN_SEOKEY') ? $this->reflection->getConstant('TABLE_COLUMN_SEOKEY') : $this->reflection->getConstant('COLUMN_SEOKEY');
		$tableColumnId = $this->reflection->getConstant('TABLE_COLUMN_ID') ? $this->reflection->getConstant('TABLE_COLUMN_ID') : $this->reflection->getConstant('COLUMN_ID');
		if ($suffix) {
			$seokey = substr(Strings::webalize($name), 0, self::UNIQUE_NAME_LENGHT) . $suffix;
		} else {
			$seokey = substr(Strings::webalize($name), 0, self::UNIQUE_NAME_LENGHT + self::UNIQUE_NAME_POSTFIX_LENGHT);
		}
		$selection = $this->table()->where($tableColumnSeokey, $seokey)->where($tableColumnId . ' != ', $id);
		if ($selection->count() == 0) {
			$ret = $seokey;
		} else {
			$iteration += 1;
			$ret = $this->generateUniqName($name, $id, $iteration);
		}
		return $ret;
	}

	public function count() {
		return $this->table()->count();
	}

}
