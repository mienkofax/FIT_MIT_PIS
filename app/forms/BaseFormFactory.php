<?php

namespace App\Forms;

use App\Model\Facades\UserFacade;
use Nette;
use Nette\Security\User;

/**
 * Zakladny tovaren pre vsetky ostatne tovarne na formulare.
 * Obsahuje informacie o uzivatelovi.
 * @package App\Forms
 */
class BaseFormFactory
{
	use Nette\SmartObject;

	/** @var UserFacade Fasada pre pracu s uzivatelmi. */
	protected $userFacade;

	/** @var User Informacie o uzivatelo (jedna sa o Nette triedu). */
	protected $user;

	/**
	 * Konstruktor s injektovanou triedami pre pracu s uzivatelmi.
	 * @param UserFacade $userFacade
	 * @param User $user
	 */
	public function __construct(UserFacade $userFacade, User $user)
	{
		$this->userFacade = $userFacade;
		$this->user = $user;
	}
}
