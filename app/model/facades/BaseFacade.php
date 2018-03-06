<?php

namespace App\Model\Facades;

use Kdyby\Doctrine\EntityManager;
use Nette;

/**
 * Zakladna fasada pre vsetkych ostatne fasady. Uchovava informacie o
 * uzivatelovi.
 * @package App\Model\Facades
 */
class BaseFacade
{
	use Nette\SmartObject;

	/** @var EntityManager Manager pre pracu s entitami. */
	protected $entityManager;

	/**
	 * Konstruktor s injektovanou triedou pre pracu s entitami.
	 * @param EntityManager $entityManager automaticky injektovana
	 * trieda pre pracu s entitami.
	 */
	function __construct(EntityManager $entityManager)
	{
		$this->entityManager = $entityManager;
	}
}
