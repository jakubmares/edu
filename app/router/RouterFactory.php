<?php

namespace App;

use Nette;
use Nette\Application\Routers\RouteList;
use Nette\Application\Routers\Route;

class RouterFactory {

	/**
	 * @return Nette\Application\IRouter
	 */
	public static function createRouter() {
		$router = new RouteList;

		$router[] = $adminRouter = new RouteList('Admin');
		$adminRouter[] = new Route('admin/<presenter>/<action>', 'Homepage:default');

		$router[] = $adminRouter = new RouteList('Zone');
		$adminRouter[] = new Route('zone/<presenter>/<action>', 'Homepage:default');

		$router[] = $frontRouter = new RouteList('Front');
		$frontRouter[] = new Route('odhlaseni', 'Sign:out');
		$frontRouter[] = new Route('prihlaseni', 'Sign:in');
		$frontRouter[] = new Route('import', 'Cron:import');
		$frontRouter[] = new Route('sitemap', 'Cron:sitemap');
		$frontRouter[] = new Route('hledat-firmu', 'Homepage:searchCompany');
		$frontRouter[] = new Route('ke-stazeni', 'Homepage:forDownload');
		$frontRouter[] = new Route('partneri', 'Homepage:partners');
		$frontRouter[] = new Route('last-minute', 'Course:lastminute');
		$frontRouter[] = new Route('zmena-hesla', 'Sign:changePass');
		$frontRouter[] = new Route('zaslat-dotaz/<seokey>', 'Question:question');
		$frontRouter[] = new Route('objednat/<id>', 'Order:order');
		
		$frontRouter[] = new Route('vseobecne-podminky', 'StaticPage:conditions');
		$frontRouter[] = new Route('podminky-uzivani', 'StaticPage:codex');
		$frontRouter[] = new Route('kontakty', 'StaticPage:contacts');
		
		
		$frontRouter[] = new Route('vzdelavaci-spolecnost/<companyKey>', 'Company:company');
		$frontRouter[] = new Route('vzdelavaci-spolecnosti[/<id>]', 'Company:default');
		
		

		$frontRouter[] = new Route('kurzy[/<id>]', 'Course:default');
		$frontRouter[] = new Route('kurz/<courseKey>', 'Course:course');
		
		$frontRouter[] = new Route('rozhovory', 'Article:interviews');
		$frontRouter[] = new Route('rozhovory/<seokey>', 'Article:interview');
		
		$frontRouter[] = new Route('clanky', 'Article:articles');
		$frontRouter[] = new Route('clanky/<seokey>', 'Article:article');
		
		$frontRouter[] = new Route('osobnosti', 'Personality:personalityList');
		$frontRouter[] = new Route('osobnosti/<seokey>', 'Personality:personality');

		$frontRouter[] = new Route('<catKey>/<focKey>[/<id>]', 'Course:focus');
		$frontRouter[] = new Route('<catKey>[/<id>]', 'Course:category');
		
		$frontRouter[] = new Route('<presenter>/<action>[/<id>]', 'Homepage:default');
		
		

		return $router;
	}

}
