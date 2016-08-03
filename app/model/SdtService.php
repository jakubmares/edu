<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace App\Model;

use App\Model\sdt,
	App\Model\So;

/**
 * Description of LdJsonService
 *
 * @author jakubmares
 */
class SdtService {

	/** @var \Nette\Application\UI\Presenter */
	private $presenter;
	private $breadcrumbs;
	private $templateBc;
	private $webSite;
	private $company;
	private $article;
	private $terms;

	public function __construct(\Nette\Application\UI\Presenter $presenter) {
		$this->presenter = $presenter;
	}

	public function addBreadCrumb($name, $destination, $params = []) {
		if (!$this->breadcrumbs) {
			$this->breadcrumbs = [
				"@context" => "http://schema.org",
				"@type" => "BreadcrumbList",
				"itemListElement" => [],
			];
		}
		$this->breadcrumbs["itemListElement"][] = [
			"@type" => "ListItem",
			"position" => count($this->templateBc) + 1,
			"item" => [
				"@id" => $this->presenter->link('//' . $destination, $params),
				"name" => $name
			]
		];
		$this->templateBc[$name] = $this->presenter->link($destination, $params);
	}

	public function getTemplateBreadCrumbs() {
		return $this->templateBc;
	}

	public function setWeb($name, $altName, $destination) {
		$this->webSite = [
			"@context" => "http://schema.org",
			"@type" => "WebSite",
			"name" => $name,
			"alternateName" => $altName,
			"url" => $this->presenter->link('//' . $destination)
		];
	}

	public function setCompany($destination, $params = []) {
		$this->company = [
			"@context" => "http://schema.org",
			"@type" => "Organization",
			"url" => $this->presenter->link('//' . $destination, $params),
		];
	}

	public function setCompanyLogo($logoUrl) {
		$this->company['logo'] = $logoUrl;
	}

	public function addCompanySocialLink($url) {
		$this->company['sameAs'][] = $url;
	}

	public function addCompanyContact($type, $email, $telephone) {
		if (!isset($this->company['contactPoint'])) {
			$this->company['contactPoint'] = [];
		}

		$contact = [
			'@type' => "ContactPoint",
			"contactType" => $type,
			"areaServed" => 'CZ'
		];

		if ($email) {
			$contact["email"] = $email;
		}

		if ($telephone) {
			$contact["telephone"] = $telephone;
		} else {
			$contact["url"] = $this->presenter->link('//StaticPage:contacts');
		}

		$this->company['contactPoint'][] = $contact;
	}

	/**
	 * @todo neni dodelane
	 * @param type $headline
	 * @param type $description
	 * @param type $author
	 * @param \Nette\Utils\DateTime $publishedDate
	 * @param \Nette\Utils\Image $image
	 * @param type $destination
	 * @param type $params
	 */
	public function setArticle($headline, $description, $author, \Nette\Utils\DateTime $publishedDate, \Nette\Utils\Image $image, $destination,
			$params) {
		$this->article = [
			"@context" => "http://schema.org",
			"@type" => "NewsArticle",
			"mainEntityOfPage" => [
				"@type" => "WebPage",
				"@id" => $this->presenter->link($destination, $params)
			],
			"headline" => $headline,
			"image" => [
				"@type" => "ImageObject",
				"url" => "https://example.com/thumbnail1.jpg",
				"height" => $image->height,
				"width" => 800
			],
			"datePublished" => $publishedDate->format(\DateTime::ISO8601),
			"author" => [
				"@type" => "Person",
				"name" => $author
			],
			"publisher" => [
				"@type" => "Organization",
				"name" => "Evzdelavani",
			],
			"description" => $description
		];
	}

	public function addTerm(So\Term $term, $destination, $args = []) {
		$course = $term->getCourse();
		$company = $course->getCompany();
		$this->terms[] = ["@context" => "http://schema.org",
			"@type" => "EducationEvent",
			"name" => $course->getName(),
			"url" => $this->presenter->link('//'.$destination, $args),
			"startDate" => $term->getFrom()->format(\Nette\Utils\DateTime::ISO8601),
			"endDate" => $term->getTo()->format(\Nette\Utils\DateTime::ISO8601),
			"description" => strip_tags($course->getDescription()),
			"location" => [
				"@type" => "Place",
				"name" => $company->getName(),
				"sameAs" => $course->getLinkUrl(),
				"address" => $term->getAddress()
			],
			"offers" => [
				"@type" => "Offer",
				"name" => "Kurz",
				"category" => "primary",
				"price" => $term->getTotalPrice(),
				"priceCurrency" => $term->getCurrency(),
				"availability" => "http://schema.org/InStock",
				"url" => $this->presenter->link('//Course:course', $course->getSeokey())
			],
			"performer"=>[
				"@type" => "Person",
				"name" => $term->getLector()?$term->getLector():'Lektor'
			]
		];
	}

	public function setJsonToTemplate() {
		$res = [];
		if ($this->webSite) {
			$res[] = $this->webSite;
		}
		if ($this->breadcrumbs) {
			$res[] = $this->breadcrumbs;
		}
		if ($this->company) {
			$res[] = $this->company;
		}
		if(count($this->terms) > 0 ){
			$res = array_merge($res,$this->terms);
		}
		$this->presenter->template->jsonLtd = $res ? \Nette\Utils\Json::encode($res) : $res;
	}

}
