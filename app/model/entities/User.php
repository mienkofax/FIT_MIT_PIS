<?php

namespace App\Model\Entities;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Kdyby\Doctrine\Entities\BaseEntity;

/**
 * Zakladne informacie o uzivatelovay meno, heslo, email, ...
 * @ORM\Entity
 */
class User extends BaseEntity
{
	/** Uzivatelske role. */
	const ROLE_SELLER = 0;
	const ROLE_MANAGER = 1;

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
	 * @ORM\Column(type="string", unique=true)
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
	 * Flag s informaciou, ci dany uzivatel je deaktivovany a nemoze sa prihlasit.
	 * @ORM\Column(type="boolean")
	 */
	protected $deactivation;

	/**
	 * Jeden uzivatel moze vytvorit niekolko objednavok.
	 * @ORM\OneToMany(targetEntity="OrderMedicine", mappedBy="user")
	 */
	protected $orders;

	/**
	 * Konstruktor pre inicializaciu vstahu medzi uzivatelom a objednavkov.
	 * Jeden uzivatel moze mat niekolko objednavok.
	 */
	public function __construct()
	{
		parent::__construct();
		$this->deactivation = false;
		$this->orders = new ArrayCollection();
	}

	/**
	 * Overenie, ci dany uzivatel ma rolu manazera.
	 * @return bool true ak je manazer, inak false
	 */
	public function isManager()
	{
		return $this->role === self::ROLE_MANAGER;
	}

	public function isSeller()
	{
		return $this->role === self::ROLE_SELLER;
	}

	/**
	 * Vytvorenie mena a priezvicka, ktore je oddelene medzerou.
	 * @return string Retazec obsahujuci meno a priezvisko oddelene medzerou.
	 */
	public function getFullName()
	{
		return $this->name . " " . $this->surname;
	}

	/**
	 * Pridanie objednavky k uzivalovi.
	 * @param OrderItem $order
	 */
	public function addOrder(OrderItem $order)
	{
		$this->orders[] = $order;
		$order->user = $this;
	}
}
