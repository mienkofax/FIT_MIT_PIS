<?php

namespace App\Forms;

use App\Model\Facades\UserFacade;
use Nette;
use Nette\Security\User;
use Nette\Forms\Controls\SubmitButton;

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

	/**
	 * Duplikovanie elementov vo formulari pomocou Kdyby.
	 * @param SubmitButton $button
	 */
	public function addElementClicked(SubmitButton $button)
	{
		$button->parent->createOne();
	}

	/**
	 * Odstranenie duplikovanych elementov vo formulari pomocou Kdyby.
	 * @param SubmitButton $button
	 */
	public function removeElementClicked(SubmitButton $button)
	{
		$users = $button->parent->parent;
		$users->remove($button->parent, true);
	}
}
