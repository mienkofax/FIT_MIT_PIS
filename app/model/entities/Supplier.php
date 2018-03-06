<?php

namespace App\Model\Entities;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Kdyby\Doctrine\Entities\BaseEntity;

/**
 * Entita s informacia o dodavateloch.
 * @ORM\Entity
 */
class Supplier extends BaseEntity
{
	/**
	 * ID dodavatela.
	 * @ORM\Id
	 * @ORM\Column(type="integer")
	 * @ORM\GeneratedValue
	 */
	protected $id;

	/**
	 * Meno dodavatela.
	 * @ORM\Column(type="string")
	 */
	protected $name;

	/**
	 * Mesto dodavatela.
	 * @ORM\Column(type="string")
	 */
	protected $city;

	/**
	 * Ulica dodavatela.
	 * @ORM\Column(type="string")
	 */
	protected $street;

	/**
	 * Cislo domu dodavatela.
	 * @ORM\Column(type="string")
	 */
	protected $houseNumber;

	/**
	 * Jeden dodavatel je v niekolkych skladovych zasobach.
	 * @ORM\OneToMany(targetEntity="StockMedicine", mappedBy="supplier")
	 */
	protected $stockMedicines;

	/**
	 * Konstruktor s inicializaciou pociatocnych hodnot a vztahov medzi
	 * entitami.
	 */
	public function __construct()
	{
		parent::__construct();
		$this->stockMedicines = new ArrayCollection();
	}

	/**
	 * Pridanie skladovej zasoby k dodavatelovi.
	 * @param StockMedicine $stockMedicine
	 */
	public function addStockMedicine(StockMedicine $stockMedicine)
	{
		$this->stockMedicines[] = $stockMedicine;
		$stockMedicine->supplier = $this;
	}
}