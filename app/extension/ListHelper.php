<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Extensions;

/**
 * Description of ListHelper
 *
 * @author jakubmares
 */
class ListHelper {
	
	
	public static function fetchPair($objects , $keyProp, $valueProp){
		$res = [];
		/* @var $object Nette\Object */
		foreach ($objects as $object) {
			$res[$object->{$keyProp}] = $object->{$valueProp};
		}
		return $res;
	}
}
