<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of ESimpleXmlElement
 *
 * @author jakubmares
 */

namespace Extensions;

class ESimpleXmlElement extends \SimpleXMLElement {

	public function getAttribute($attribute, $default = false) {
		return isset($this[$attribute]) ? (string) $this[$attribute] : $default;
	}

	/**
	 * 
	 * @param string $tagName
	 * @return ESimpleXmlElement
	 */
	public function getTag($tagName) {
		return $this->{$tagName};
	}

	/**
	 * 
	 * @param string $tagName
	 * @return string
	 */
	public function getTagContent($tagName) {
		return htmlspecialchars((string) $this->{$tagName});
	}

	public function getTagContentHtml($tagName, $allowed = '') {
		return strip_tags((string) $this->{$tagName}, $allowed);
	}

	public function getContent() {
		return htmlspecialchars((string) $this);
	}

	public function getTagContentUrl($tagName) {
		$res = '';
		$patt = "_(^|[\s.:;?\-\]<\(])(https?://[-\w;/?:@&=+$\|\_.!~*\|'()\[\]%#,☺]+[\w/#](\(\))?)(?=$|[\s',\|\(\).:;?\-\[\]>\)])_i";
		if (preg_match($patt, $this->{$tagName})) {
			$res = (string) $this->{$tagName};
		}
		return $res;
	}

	/**
	 * 
	 * @param type $filename
	 * @return ESimpleXmlElement
	 */
	public static function loadXml($filename) {

		if (preg_match('#^(https):\/\/\S+#', $filename)) {
			$ch = curl_init($filename);
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
			curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
			curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
			$content = curl_exec($ch);
			curl_close($ch);
			$xml = simplexml_load_string($content, get_called_class());
		} else {
			$xml = simplexml_load_file($filename, get_called_class());
		}
		return $xml;
	}

}
