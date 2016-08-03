<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace App\Model\So;

/**
 *
 * @author jakubmares
 */
interface IAddress {
	public function getCity();

	public function getStreet();

	public function getRegistryNumber();

	public function getHouseNumber();

	public function getZip();

	public function getAddressNote();

	public function getLatitude();

	public function getLongitude();
	
	public function getCountryKey();

	public function getAddress();
	
	public function getStreetAd();

}
