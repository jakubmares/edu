<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace App\Components;

/**
 *
 * @author jakubmares
 */
interface IAddressFormFactory {
	/**
	 * 
	 * @param int $companyId
	 * @return AddressForm
	 */
	public function create($companyId);
}
