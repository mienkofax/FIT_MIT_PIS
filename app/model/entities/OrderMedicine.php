<?php

namespace App\Model\Entities;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Kdyby\Doctrine\Entities\BaseEntity;

/**
 * Entita reprezentujuca objednavku a informacie o nej.
 * @ORM\Entity
 */
class OrderMedicine extends BaseEntity
{
	/**
	 * ID objednavky.
	 * @ORM\Id
	 * @ORM\Column(type="integer")
	 * @ORM\GeneratedValue
	 */
	protected $id;

	/**
	 * Celkova cena.
	 * @ORM\Column(type="integer")
	 */
	protected $totalPrice;

	/**
	 * Cas vytvorenia.
	 * @ORM\Column(type="datetime")
	 */
	protected $dateTime;

	/**
	 * Jedna objednavka ma niekolko poloziek.
	 * @ORM\OneToMany(targetEntity="OrderItem", mappedBy="order")
	 */
	protected $orderItems;

	/**
	 * Niekolko objednavok moze byt vytvorenych jednym uzivatelom.
	 * @ORM\ManyToOne(targetEntity="User", inversedBy="orders")
	 * @ORM\JoinColumn(name="user_id", referencedColumnName="id")
	 */
	protected $user;

	/**
	 * Konstruktor pre inicializaciu pociatocnych hodnot a vztahov medzi
	 * entitami.
	 */
	public function __construct()
	{
		parent::__construct();
		$this->orderItems = new ArrayCollection();
	}

	/**
	 * Prida polozku do objednavky.
	 * @param OrderItem $orderItem
	 */
	public function addOrderItem(OrderItem $orderItem)
	{
		$this->orderItems[] = $orderItem;
		$orderItem->order = $this;
	}
}
