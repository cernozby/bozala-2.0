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
               ->addRoute('administrace', 'Homepage:default');
        $router->withModule('Public')
               ->addRoute('registrace', 'Registration:registration')
               ->addRoute('prihlaseni', 'Registration:login')
               ->addRoute('<presenter>/<action>[/<id>]', 'Homepage:default');
		return $router;
	}
}
