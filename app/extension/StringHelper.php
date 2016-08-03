<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Extensions;

/**
 * Description of StringHelper
 *
 * @author jakubmares
 */
class StringHelper {

	/**
	 * 
	 * @param string $string
	 * @param type $firstUpper
	 * @return type
	 */
	public static function underscoreToCamelCase(string $string, $firstUpper = false) {
		$str = str_replace('_', '', ucwords($string, '_'));
		return $firstUpper ? $str : lcfirst($str);
	}

	/**
	 * 
	 * @param string $string
	 * @return type
	 */
	public static function camelCaseToUnderscore(string $string) {
		return lcfirst(strtolower(preg_replace('/([a-z])([A-Z])/', '$1_$2', $string)));
	}
	
	public static function removeSpecialChars($text){
		$text = preg_replace("/[®яЯ]/u",  "", $text);
		$text = preg_replace("/[Тт]/u",   "", $text);
		$text = preg_replace("/[™]/u",   "", $text);
		return $text;
	}

}
