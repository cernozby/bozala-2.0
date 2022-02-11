<?php

declare(strict_types=1);

namespace App\Router;

use Nette;
use Nette\Application\Routers\RouteList;


final class RouterFactory
{
	use Nette\StaticClass;

	public static function createRouter(): RouteList
	{
		$router = new RouteList;
		$router->withModule('Sys')
               ->addRoute('administrace', 'Homepage:default')
               ->addRoute('uzivatel', 'Homepage:user')
               ->addRoute('seznam-zavodu', 'Homepage:listOfComps')
               ->addRoute('zavodnici', 'Homepage:listOfCompetitors')
               ->addRoute('predregistrace[/<competitorId>]', 'Homepage:prereg')
               ->addRoute('zavod-predregistrace[/<compId>]', 'Homepage:listOfPrereg')
               ->addRoute('zadavani-vysledku[/<compId>][/<categoryId>]', 'Homepage:addResult')
               ->addRoute('vysledky[/<compId>][/<categoryId>]', 'Homepage:result');
        $router->withModule('Public')
               ->addRoute('registrace', 'Registration:registration')
               ->addRoute('prihlaseni', 'Registration:login')
               ->addRoute('<presenter>/<action>[/<id>]', 'Homepage:default');
		return $router;
	}
}
