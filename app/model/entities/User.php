<?php

namespace App\Model\Entities;

use Doctrine\ORM\Mapping as ORM;
use Kdyby\Doctrine\Entities\BaseEntity;

/**
 * Zakladne informacie o uzivatelovay meno, heslo, email, ...
 * @ORM\Entity
 */
class User extends BaseEntity
{
	/** Uzivatelske role. */
	const ROLE_GUEST = 1;
	const ROLE_USER = 2;
	const ROLE_MANAGER = 3;
	const ROLE_ADMIN = 4;

	/** Maximalna dlzka prihlasovacieho mena */
	const NAME_MAX_LENGTH = 15;

	/** Format prihlasovacieho mena. */
	const NAME_FORMAT = "^[a-zA-Z0-9]*$";

	/**
	 * ID uzivatela.
	 * @ORM\Id
	 * @ORM\Column(type="integer")
	 * @ORM\GeneratedValue
	 */
	protected $id;

	/**
	 * Meno pouzivatela.
	 * @ORM\Column(type="string")
	 */
	protected $name;

	/**
	 * Priezvisko pouzivatela.
	 * @ORM\Column(type="string")
	 */
	protected $surname;

	/**
	 * Email pouzivatela.
	 * @ORM\Column(type="string")
	 */
	protected $email;

	/**
	 * Heslo pouzivatela.
	 * @ORM\Column(type="string")
	 */
	protected $password;

	/**
	 * Datum a cas, kedy sa uzivatel zaregistroval.
	 * @ORM\Column(name="`registration_date`", type="datetime")
	 */
	protected $registrationDate;

	/**
	 * Datum a cas, kedy sa uzivatel naposledy prihlasil.
	 * @ORM\Column(name="`last_login`", type="datetime", nullable=true)
	 */
	protected $lastLogin;

	/**
	 * Rola uzivatela, ake operacie moze/nemoze vykonavat.
	 * @ORM\Column(type="integer")
	 */
	protected $role;

	/**
	 * Overenie, ci dany uzivatel ma rolu administratora.
	 * @return bool true ak je administrator, inak false
	 */
	public function isAdmin()
	{
		return $this->role === self::ROLE_ADMIN;
	}

	/**
	 * Overenie, ci dany uzivatel ma rolu manazera.
	 * @return bool true ak je manazer, inak false
	 */
	public function isManager()
	{
		return $this->role === self::ROLE_MANAGER;
	}

	/**
	 * Vytvorenie mena a priezvicka, ktore je oddelene medzerou.
	 * @return string Retazec obsahujuci meno a priezvisko oddelene medzerou.
	 */
	public function getFullName()
	{
		return $this->name . " " . $this->surname;
	}
}
