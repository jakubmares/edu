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
interface IVideoFormFactory {
	
	/**
	 * 
	 * @param int $companyId
	 * @return VideoForm
	 */
	public function create($companyId);
}
