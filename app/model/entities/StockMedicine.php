<?php

namespace App\Model\Entities;

use Doctrine\ORM\Mapping as ORM;
use Kdyby\Doctrine\Entities\BaseEntity;

/**
 * Entita uchovavajuca informacie o liekoch a ich dodavatelov.
 * Na zaklade toho ma dany liek od daneho dodavatela urcitu cenu.
 * @ORM\Entity
 */
class StockMedicine extends BaseEntity
{
	/**
	 * Niekolko skladovych zasob ma jeden liek.
	 * @ORM\Id
	 * @ORM\ManyToOne(targetEntity="Medicine", inversedBy="stockMedicines")
	 * @ORM\JoinColumn(name="medicine_id", referencedColumnName="id")
	 */
	protected $medicine;

	/**
	 * Niekolko skladovych zasob ma jeden dodavatel.
	 * @ORM\Id
	 * @ORM\ManyToOne(targetEntity="Supplier", inversedBy="stockMedicines")
	 * @ORM\JoinColumn(name="supplier_id", referencedColumnName="id")
	 */
	protected $supplier;

	/**
	 * Pocet kusov.
	 * @ORM\Column(type="integer")
	 */
	protected $count;

	/**
	 * Cena lieku od dodavatela.
	 * @ORM\Column(type="float")
	 */
	protected $price;
}
