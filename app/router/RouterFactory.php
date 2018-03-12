<?php

namespace App;

use Nette;
use Nette\Application\Routers\Route;
use Nette\Application\Routers\RouteList;


class RouterFactory
{
	use Nette\StaticClass;

	/**
	 * @return Nette\Application\IRouter
	 */
	public static function createRouter()
	{
		$router = new RouteList;

		$router[] = new Route('medicine/edit[/<id>]', 'Medicine:edit');
		$router[] = new Route('medicine/manage[/<do>][/<id>]', 'Medicine:manage');
		$router[] = new Route('medicine/manage[/<column>][/<sort>]', 'Medicine:manage');

		$router[] = new Route('supplier/edit[/<id>]', 'Supplier:edit');
		$router[] = new Route('supplier/manage[/<do>][/<id>]', 'Supplier:manage');
		$router[] = new Route('supplier/manage[/<column>][/<sort>]', 'Supplier:manage');

		$router[] = new Route('stock-medicine/edit[/<medicineId>][/<supplierId>]', 'StockMedicine:edit');
		$router[] = new Route('stock-medicine/manage[/<do>][/<medicineId>][/<supplierId>]', 'StockMedicine:manage');
		$router[] = new Route('stock-medicine/manage[/<column>][/<sort>]', 'StockMedicine:manage');

		$router[] = new Route('order-medicine/manage[/<column>][/<sort>]', 'OrderMedicine:manage');

		$router[] = new Route('user/manage[/<do>][/<id>]', 'User:manage');
		$router[] = new Route('user/edit[/<id>]', 'User:edit');
		$router[] = new Route('user/profil[/<id>]', 'User:profil');
		$router[] = new Route('user/manage[/<column>][/<sort>]', 'User:manage');

		$router[] = new route('<presenter>/<action>', 'Homepage:default');
		return $router;
	}
}
