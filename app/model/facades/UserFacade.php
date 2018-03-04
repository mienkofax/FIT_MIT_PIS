<?php

namespace App\Model\Facades;

use App\Model\Entities\User;
use Nette;
use Nette\Database\UniqueConstraintViolationException;
use Nette\Security\AuthenticationException;
use Nette\Security\IAuthenticator;
use Nette\Security\Identity;
use Nette\Security\Passwords;
use Nette\Utils\ArrayHash;
use Nette\Utils\DateTime;

/**
 * Fasada pre spravu uzivatelov.
 * @package App\Model\Facades
 */
class UserFacade extends BaseFacade implements IAuthenticator
{
	use Nette\SmartObject;

	/**
	 * Vyhladanie uzivatela podla zadaneho ID.
	 * @return User|NULL vratenie entity s uzivatelom, ak sa nepodari najst
	 * uzivatel vrati sa NULL
	 */
	public function getUser($id)
	{
		if (isset($id))
			return $this->entityManager->find(User::class, $id);

		return NULL;
	}

	/**
	 * Pridanie noveho uzivatela do databaze
	 * @param ArrayHash $values hodnoty uzivatela, ktory sa ma pridat
	 * @throws \Exception
	 */
	public function registerUser($values)
	{
		// kontrola, ci uz niekto nema zaregistrovany dany email
		$count = $this->entityManager->getRepository(User::class)
			->countBy(array("email" => $values->email));

		if ($count >= 1)
			throw new UniqueConstraintViolationException("Zadaný email je už registrovaný.");

		// ulozenie dat do db
		$user = new User();
		$user->name = $values->name;
		$user->surname = $values->surname;
		$user->email = $values->email;
		$user->password = Passwords::hash($values->password);
		$user->registrationDate = new DateTime();
		$user->lastLogin = new DateTime();
		$user->role = User::ROLE_USER;

		$this->entityManager->persist($user);
		$this->entityManager->flush();
	}

	/**
	 * Prihlasenie uzivatela do systemu.
	 * @param array $credentials Uzivatelske meno a heslo.
	 * @return Identity Informacie o uzivatelo pre neskorsi pouzitie.
	 * @throws AuthenticationException
	 * @throws \Exception
	 */
	function authenticate(array $credentials)
	{
		list($username, $password) = $credentials;

		// vyhladanie v entite User podla emailu
		$user = $this->entityManager->getRepository(User::class)
			->findOneBy(array("email" => $username));

		if (!isset($user) || !Passwords::verify($password, $user->password))
			throw new AuthenticationException("Bolo zadané nesprávne meno alebo heslo.");

		if (Passwords::needsRehash($user->password)) {
			$user->password = Passwords::has($password);
			$this->entityManager->flush();
		}

		return new Identity($user->id);
	}
}
