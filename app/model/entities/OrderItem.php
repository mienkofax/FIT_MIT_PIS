<?php

namespace App\Model\Entities;

use Doctrine\ORM\Mapping as ORM;
use Kdyby\Doctrine\Entities\BaseEntity;

/**
 * Entita reprezentujuca polozku v objednavke.
 * Uchovava napr. informacie o cene, ktora musi byt vlozena s danou
 * polozkou, preloze cena sa moze menit.
 * @ORM\Entity
 */
class OrderItem extends BaseEntity
{
	/**
	 * ID polozky.
	 * @ORM\Id
	 * @ORM\Column(type="integer")
	 * @ORM\GeneratedValue
	 */
	protected $id;

	/**
	 * Cena polozky.
	 * @ORM\Column(type="float")
	 */
	protected $price;

	/**
	 * Pocet kusok.
	 * @ORM\Column(type="integer")
	 */
	protected $count;

	/**
	 * Prispevok na liek od zdravotnej poistovni.
	 * @ORM\Column(type="float")
	 */
	protected $contribution;

	/**
	 * Niekolko poloziek objednavky ma jednu objednavku.
	 * @ORM\ManyToOne(targetEntity="OrderMedicine", inversedBy="orderItems")
	 * @ORM\JoinColumn(name="order_id", referencedColumnName="id")
	 */
	protected $order;

	/**
	 * Dodatocne informacie o lieku, ktore nemusia byt ulozene v tejto casti.
	 *
	 * Viacrymi polozkami moze byt odkazovany jeden liek.
	 * @ORM\ManyToOne(targetEntity="Medicine")
	 * @ORM\JoinColumn(name="medicine_id", referencedColumnName="id")
	 */
	protected $medicines;

	/**
	 * Dodatocne informacie o dodavatelovi, ktore nemusia byt ulozene
	 * v tejto casti.
	 *
	 * Viacerymi polozkami moze byt odkazovany jeden dodavatel.
	 * @ORM\ManyToOne(targetEntity="Supplier")
	 * @ORM\JoinColumn(name="supplier_id", referencedColumnName="id")
	 */
	protected $suppliers;
}
