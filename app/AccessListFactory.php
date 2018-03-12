<?php

namespace App;

use Nette\Security\Permission;

class AccessListFactory extends Permission
{
	public function __construct()
	{
		// Uzivatelske role
		$this->addRole('guest');
		$this->addRole('seller', 'guest');
		$this->addRole('manager', 'seller');

		// Zdroje ku, ktorym je mozne udelit pristup
		$this->addResource('Homepage');
		$this->addResource('Medicine');
		$this->addResource('OrderMedicine');
		$this->addResource('Sign');
		$this->addResource('StockMedicine');
		$this->addResource('Supplier');
		$this->addResource('User');

		// Zoznam opravneni pre navstevnika
		$this->allow('guest', 'Homepage', array('logout', 'default'));
		$this->allow('guest', 'Sign', array('in'));

		// opravnenia pre predavaca
		$this->allow('seller', 'Medicine', array('manage', 'detail'));
		$this->allow('seller', 'OrderMedicine', array('manage', 'detail', 'create'));
		$this->allow('seller', 'StockMedicine', array('manage', 'detail', 'addToStock'));
		$this->allow('seller', 'Supplier', array('manage', 'detail'));
		$this->allow('seller', 'User', array('profil', 'changePassword', 'edit'));

		// Manager ma prava na vsetko
		$this->allow('manager', Permission::ALL, Permission::ALL);
	}
}
