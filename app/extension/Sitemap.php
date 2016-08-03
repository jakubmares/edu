<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Extensions;

/**
 * Description of Sitemap
 *
 * @author jakubmares
 */
class Sitemap extends \SimpleXMLElement {

	const LOC = 'loc',
			ULR = 'url',
			LASTMOD = 'lastmod',
			CHANGEFREQ = 'changefreq',
			CHANGEFREQ_ALWAYS = 'always',
			CHANGEFREQ_HOURLY = 'hourly',
			CHANGEFREQ_DAILY = 'daily',
			CHANGEFREQ_WEEKLY = 'weekly',
			CHANGEFREQ_MONTHLY = 'monthly',
			CHANGEFREQ_YEARLY = 'yearly',
			CHANGEFREQ_NEVER = 'never',
			PRIORITY = 'priority';

	private static $initXml = '<?xml version="1.0" encoding="UTF-8"?>
				<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">
				</urlset>';

	public static function create() {
		return new Sitemap(self::$initXml);
	}

	public function addUrl($link, $changefreq = null, $priority = null) {
		
		$url = $this->addChild(self::ULR);
		$url->addChild(self::LOC, $link);
		if($changefreq){
			$url->addChild(self::CHANGEFREQ, $changefreq);
		}
		if($priority){
			$url->addChild(self::PRIORITY, $priority);
		}
		return $url;
	}

}
