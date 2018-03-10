<?php

namespace App\Model\Entities;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Kdyby\Doctrine\Entities\BaseEntity;

/**
 * Entita, ktora reprezentuje liek a informacie o nom.
 * @ORM\Entity
 */
class Medicine extends BaseEntity
{
	/**
	 * ID lieku.
	 * @ORM\Id
	 * @ORM\Column(type="integer")
	 * @ORM\GeneratedValue
	 */
	protected $id;

	/**
	 * Id lieku podla SUKL.
	 * @ORM\Column(type="string", unique=true)
	 */
	protected $idSukl;

	/**
	 * Nazov lieku.
	 * @ORM\Column(type="string")
	 */
	protected $name;

	/**
	 * Popis lieku.
	 * @ORM\Column(type="text")
	 */
	protected $description;

	/**
	 * Typ lieku, bud je na predpis alebo nie. Liek na predpis (true),
	 * liek bez predpisu (false).
	 * @ORM\Column(type="boolean")
	 */
	protected $type;

	/**
	 * Prispevok na liek.
	 * @ORM\Column(type="float")
	 */
	protected $contribution;

	/**
	 * Jeden liek je v niekolkych skladovych zasobach.
	 * @ORM\OneToMany(targetEntity="StockMedicine", mappedBy="medicine")
	 */
	protected $stockMedicines;

	/**
	 * Konstruktor s inicializaciou pociatocnych hodnot a vztahov medzi
	 * entitami.
	 */
	public function __construct()
	{
		parent::__construct();
		$this->type = false;
		$this->contribution = 0.0;

		$this->stockMedicines = new ArrayCollection();
	}

	/**
	 * Pridanie skladovej zasoby k dodavatelovi.
	 * @param StockMedicine $stockMedicine
	 */
	public function addStockMedicine(StockMedicine $stockMedicine)
	{
		$this->stockMedicines[] = $stockMedicine;
		$stockMedicine->medicine = $this;
	}
}
