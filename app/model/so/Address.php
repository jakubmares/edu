<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace App\Model\So;

/**
 * Description of Address
 *
 * @author jakubmares
 */
class Address extends SmartObject implements IAddress {

	const
			TYPE_BILLING = 'BILLING',
			TYPE_BASE = 'BASE',
			TYPE_BRANCH = 'BRANCH';

	private $id;
	private $city;
	private $street;
	private $registryNumber;
	private $houseNumber;
	private $zip;
	private $note;
	private $latitude;
	private $longitude;
	private $companyId;
	private $type;
	private $countryKey;

	public static function types() {
		return [
			self::TYPE_BILLING => 'Fakturační adresa',
			self::TYPE_BASE => 'Sídlo firmy',
			self::TYPE_BRANCH => 'Pobočka firmy',
		];
	}

	public function getId() {
		return $this->id;
	}

	public function getCity() {
		return $this->city;
	}

	public function getStreet() {
		return $this->street;
	}

	public function getRegistryNumber() {
		return $this->registryNumber;
	}

	public function getHouseNumber() {
		return $this->houseNumber;
	}

	public function getZip() {
		return $this->zip;
	}

	public function getNote() {
		return $this->note;
	}

	public function getLatitude() {
		return $this->latitude;
	}

	public function getLongitude() {
		return $this->longitude;
	}

	public function getCompanyId() {
		return $this->companyId;
	}

	public function getType() {
		return $this->type;
	}

	public function getAddressNote() {
		return $this->getNote();
	}

	public function getLabel() {
		$types = self::types();
		return $types[$this->type];
	}

	public function getCountryKey() {
		return $this->countryKey;
	}

	public function getAddress() {
		$numbers = [];
		if ($this->registryNumber) {
			$numbers[] = $this->registryNumber;
		}
		if ($this->houseNumber) {
			$numbers[] = $this->houseNumber;
		}
		$number = implode('/', $numbers);
		$ret = sprintf('%s %s %s', $this->street, $number?$number:'', $this->zip? $this->zip:'');
		$ret .= $this->city?', '.$this->city:'';
		return $ret;
	}

	public function getStreetAd() {
		$numbers = [];
		if ($this->registryNumber) {
			$numbers[] = $this->registryNumber;
		}
		if ($this->houseNumber) {
			$numbers[] = $this->houseNumber;
		}
		$number = implode('/', $numbers);
		return sprintf('%s %s', $this->street, $number);
	}

	public function __toString() {
		return $this->getAddress();
	}

}
