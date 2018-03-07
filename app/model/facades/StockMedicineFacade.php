<?php

namespace App\Model\Facades;

use App\Model\Entities\StockMedicine;
use App\Model\Queries\StockMedicineQueryList;
use Nette;

/**
 * Trieda pre pracu so skladovymi zasobami, umoznuje vytvorit novu
 * skladovu zasobu, vyhladat s poskytuje dotazy na skladove zasoby.
 * @package App\Model\Facades
 */
class StockMedicineFacade extends BaseFacade
{
	use Nette\SmartObject;

	/**
	 * Vyhladanie a vratenie skladovej zasoby na zaklade zadaneho id.
	 * @param $id
	 * @return null|object
	 * @throws \Doctrine\ORM\ORMException
	 * @throws \Doctrine\ORM\OptimisticLockException
	 * @throws \Doctrine\ORM\TransactionRequiredException
	 */
	public function getStockMedicine($id)
	{
		if (isset($id))
			return $this->entityManager->find(StockMedicine::class, $id);

		return NULL;
	}

	/**
	 * Vytvorenie skladovej zasoby.
	 * @param $values
	 * @param $medicine
	 * @param $supplier
	 * @throws \Exception
	 */
	public function createStockMedicine($values, $medicine, $supplier)
	{
		$stockMedicine = new StockMedicine();
		$stockMedicine->count = $values->count;
		$stockMedicine->price = $values->price;

		$stockMedicine->medicine = $medicine;
		$medicine->addStockMedicine($stockMedicine);

		$stockMedicine->supplier = $supplier;
		$supplier->addStockMedicine($stockMedicine);

		$this->entityManager->persist($stockMedicine);
		$this->entityManager->flush();
	}
}
