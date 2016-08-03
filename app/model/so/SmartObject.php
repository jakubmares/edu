<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace App\Model\So;

use Extensions,
	ReflectionClass,
	Nette\Object,
	Nette\Database\Table\Selection,
	Nette\Database\Table\ActiveRow,
	Nette\Utils\ArrayHash;

/**
 * Description of PlainObject
 *
 * @author jakubmares
 */
abstract class SmartObject extends Object {

	/**  @var ActiveRow */
	protected $row;

	/**
	 * 
	 * @param ActiveRow|null $row
	 */
	public function __construct($row = null) {
		if ($row instanceof ActiveRow) {
			$this->setPropertiesFormActiveRow($row);
		} elseif ($row instanceof \IteratorAggregate) {
			$this->setValues($row);
		} else {
			throw new \Exception('Unexpected variable');
		}
	}

	/**
	 * naplni property objekty z radku databaze
	 * @param ActiveRow $row
	 */
	private function setPropertiesFormActiveRow(ActiveRow $row) {
		$this->row = $row;
		$this->setProperties($row);
	}

	private function setProperties(\IteratorAggregate $array) {
		foreach ($array as $name => $value) {
			$propertyName = Extensions\StringHelper::underscoreToCamelCase($name);
			if ($this->reflection->hasProperty($propertyName)) {
				$property = $this->reflection->getProperty($propertyName);
				$property->setAccessible(true);
				$property->setValue($this, $value);
			}
		}
	}

	/**
	 *
	 * @return ArrayHash
	 */
	public function toArray() {
		$ah = new ArrayHash();
		/* @var $property \Nette\Reflection\Property */
		foreach ($this->reflection->getProperties() as $property) {
			if ($property->getName() != 'row') {
				$property->setAccessible(true);
				$ah->offsetSet(Extensions\StringHelper::camelCaseToUnderscore($property->getName()), $property->getValue($this));
			}
		}
		return $ah;
	}

	/**
	 * 
	 * @param string $key
	 * @param string $throughColumn
	 * @return SmartObject|null
	 */
	protected function getRef($key, $throughColumn = null) {
		$row = $this->row->ref($key, $throughColumn);
		return self::create($row);
	}

	/**
	 * 
	 * @param type $key
	 * @param type $throughColumn
	 * @return SmartObject[]
	 */
	protected function getRelated($key, $throughColumn = null) {
		$selection = $this->row->related($key, $throughColumn);
		return self::createList($selection);
	}

	/**
	 * 
	 * @param type $row
	 * @return SmartObject
	 */
	public static function create($row) {
		if (!$row) {
			return null;
		}

		if (!($row instanceof ActiveRow)) {
			throw new \Exception('Argument $row must be an instance of Nette\Database\Table\ActiveRow');
		}

		$refClass = new ReflectionClass(sprintf('%s\%s', __NAMESPACE__, Extensions\StringHelper::underscoreToCamelCase($row->getTable()->getName(), true)));
		$ret = $refClass->newInstance($row);
		return $ret;
	}

	public static function createList(Selection $selection) {
		$ret = [];
		foreach ($selection as $row) {
			array_push($ret, self::create($row));
		}
		return $ret;
	}

}
