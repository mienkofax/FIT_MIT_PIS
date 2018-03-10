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
	 * Vyber vsetkych zaznamov.
	 * @return array
	 */
	public function getAll()
	{
		$query = new StockMedicineQueryList();
		return $this->entityManager
			->getRepository(StockMedicine::class)
			->fetch($query);
	}

	/**
	 * Zoznam skladovych zasob zoradeny podla stlpca a podla sposobu
	 * zoradenia.
	 * @param $column string
	 * @param $sort string
	 * @return array
	 */
	public function getAllAsArray($column, $sort)
	{
		$query = new StockMedicineQueryList();
		$query->orderBy($column, $sort);

		return $this->entityManager->fetch($query);
	}

	/**
	 * Vrati zoznam liekov, ktore sa nachadzaju v sklade, cize lieky,
	 * ktore dodavaju dodavatelia.
	 * @return array Asociativne pole, ktore obsahuje id lieku a nazov lieku.
	 */
	public function getMedicinesAsArray()
	{
		$medicines = array();

		foreach ($this->getAll() as $stockItem)
			$medicines[$stockItem->medicine->id] = $stockItem->medicine->name;

		return $medicines;
	}

	/**
	 * Vrati zoznam dodavatelov, ktori sa nachadzaju v sklade, cize dodavatelia,
	 * ktori dodavaju lieky.
	 * @return array Asociativne pole, ktore obsahuje id dodavatela a nazov dodavatela.
	 */
	public function getSuppliersAsArray()
	{
		$medicines = array();

		foreach ($this->getAll() as $stockItem)
			$medicines[$stockItem->supplier->id] = $stockItem->supplier->name;

		return $medicines;
	}

	/**
	 * Na zaklade zadaneho id lieku vrati zoznam dodavatelov, ktori
	 * dany liek dodavaju do lekarne.
	 * @param $id int ID lieku
	 * @return array|\Kdyby\Doctrine\ResultSet
	 */
	public function getMedicineSupplier($id)
	{
		$query = new StockMedicineQueryList();
		$query->onlyMedicineSupplies($id);
		return $this->entityManager->getRepository(StockMedicine::class)
			->fetch($query);
	}

	/**
	 * Na zaklade zadaneho id lieku vrati zoznam dodavatelov, ktori
	 * dany liek dodavaju do lekarne.
	 * @param $id int ID lieku
	 * @return array Asociativne pole, ktore obsahuje id dodavatela
	 * a nazov dodavatela.
	 */
	public function getMedicineSupplierAsArray($id)
	{
		$medicineSuppliers = array();

		foreach ($this->getMedicineSupplier($id) as $supplier)
			$medicineSuppliers[$supplier->supplier->id] = $supplier->supplier->name;

		return $medicineSuppliers;
	}

	/**
	 * Zaznam o skladovej zasobe na zaklade id lieku a id dodavatela.
	 * @param $medicine int Id lieku
	 * @param $supplier int id dodavatela
	 * @return array|\Kdyby\Doctrine\ResultSet
	 */
	public function getSupplierMedicine($medicine, $supplier)
	{
		$query = new StockMedicineQueryList();
		$query->onlyMedicineSupplies($medicine);
		$query->onlySupplierMedicines($supplier);
		return $this->entityManager->getRepository(StockMedicine::class)
			->fetch($query);
	}

	/**
	 * Zisti cenu skladovej zasoby na zaklade id.
	 * @param $medicine int
	 * @param $supplier int
	 * @return mixed cena skladovej polozky
	 */
	public function getStockItemPrice($medicine, $supplier)
	{
		if ($this->getSupplierMedicine($medicine, $supplier)->count() != 1)
			throw new \InvalidArgumentException("Databáza obsahuje niekoľko zhodných liekov a dodávateľov");

		foreach ($this->getSupplierMedicine($medicine, $supplier) as $item)
			return $item->price;
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
		if ($medicine->price < $values->price) {
			throw new \InvalidArgumentException(
				"Cena lieku od dodávateľa musí byť menšia ako predajná cena");
		}

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

	public function editStockMedicine($values, $stockMedicine, $medicine, $supplier)
	{
		if ($medicine->price < $values->price) {
			throw new \InvalidArgumentException(
				"Cena lieku od dodávateľa musí byť menšia ako predajná cena");
		}

		$stockMedicine->count = $values->count;
		$stockMedicine->price = $values->price;

		$stockMedicine->medicine = $medicine;
		$medicine->addStockMedicine($stockMedicine);

		$stockMedicine->supplier = $supplier;
		$supplier->addStockMedicine($stockMedicine);

		$this->entityManager->flush();
	}

	public function deleteStockMedicine($id = NULL)
	{
		$stockItem= NULL;

		if (is_null($id) | is_null($stockItem = $this->getStockMedicine($id)))
			throw new \InvalidArgumentException("Skladová zásoba neexistuje.");

		$this->entityManager->remove($stockItem);
		$this->entityManager->flush();
	}

	public function addToStock($data, StockMedicine $stockMedicine)
	{
		$stockMedicine->count += $data['count'];
		$this->entityManager->flush();
	}
}
