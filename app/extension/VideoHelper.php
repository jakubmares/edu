<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Extensions;

/**
 * Description of VideoHelper
 *
 * @author jakubmares
 */
class VideoHelper {
	public static function cutYoutubeCode($url){
		$ret = '';
		if (preg_match("#(?<=v=)[a-zA-Z0-9-]+(?=&)|(?<=v\/)[^&\n]+(?=\?)|(?<=v=)[^&\n]+|(?<=youtu.be/)[^&\n]+#", $url, $matches)) {
			$ret = $matches[0];
		}
		return $ret;
	}
}
