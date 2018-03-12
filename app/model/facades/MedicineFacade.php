<?php

namespace App\Model\Facades;

use App\Model\Entities\Medicine;
use App\Model\Queries\MedicineListQuery;
use Nette;
use Nette\Utils\Json;

/**
 * Trieda pre pracu s liekmi, umoznuje vytvorit novy liek ale aj vyhladat
 * liek.
 * @package App\Model\Facades
 */
class MedicineFacade extends BaseFacade
{
	use Nette\SmartObject;

	/**
	 * Vyhladanie a vratenie lieku podla zadaneho id.
	 * @param $id
	 * @return null|object
	 * @throws \Doctrine\ORM\ORMException
	 * @throws \Doctrine\ORM\OptimisticLockException
	 * @throws \Doctrine\ORM\TransactionRequiredException
	 */
	public function getMedicine($id)
	{
		if (isset($id))
			return $this->entityManager->find(Medicine::class, $id);

		return NULL;
	}

	public function getMedicineByIdSukl($idSukl)
	{
		if (isset($idSukl)) {
			$query = new MedicineListQuery();
			$query->withSuklId($idSukl);

			return $this->entityManager->fetchOne($query);
		}

		return NULL;
	}

	/**
	 * Zistenie poctu liekov.
	 * @return int pocet liekov
	 * @throws \Doctrine\ORM\NoResultException
	 * @throws \Doctrine\ORM\NonUniqueResultException
	 */
	public function getMedicinesCount()
	{
		return (int)$this->entityManager->createQuery("
				SELECT COUNT(m.id)
				FROM App\Model\Entities\Medicine m
		")->getSingleScalarResult();
	}

	/**
	 * Asociativne pole liekov s ich id a nazvami.
	 * @param $data
	 * @throws \Exception
	 */
	public function getIdsAndName()
	{
		return $this->entityManager->getRepository(Medicine::class)
			->findPairs([], "name", [], "id");
	}

	/**
	 * Vyberu sa vsetky lieky.
	 * @return array|\Kdyby\Doctrine\ResultSet
	 */
	public function getAll()
	{
		$query = new MedicineListQuery();
		return $this->entityManager
			->getRepository(Medicine::class)
			->fetch($query);
	}

    /**
     * Zoznam liekov zoradeny podla stlpca a podla sposobu
     * zoradenia.
     * @param $column string
     * @param $sort string
     * @return array
     */
    public function getAllAsArray($column, $sort)
    {
        $query = new MedicineListQuery();
        $query->orderBy($column, $sort);

        return $this->entityManager->fetch($query)->toArray();
    }

	/**
	 * Metoda prevedie skladove zasoby do JSON formatu.
	 * @return string
	 */
	public function toJSON()
	{
		$data = "{";
		$data .= '"medicines":[';

		foreach ($this->getAll() as $med) {
			if (count($med->stockMedicines) == 0)
				continue;

			$data .= "{";
			$data .= '"id":' . $med->id . ",";
			$data .= '"price":' . $med->price . ",";
			$data .= '"name":"' . $med->name . '",';

			$data .= '"suppliers": [';
			foreach ($med->stockMedicines as $stockItem) {
				$data .= "{";

				$data .= '"id":' . $stockItem->supplier->id . ",";
				$data .= '"name":"' . $stockItem->supplier->name . '",';
				$data .= '"price":' . $stockItem->price . ",";
				$data .= '"count":' . $stockItem->count . "";

				$data .= "},";
			}
			$data = rtrim($data,",");
			$data .= "]},";
		}
		$data = rtrim($data,",");

		$data .= "]}";
		return $data;
	}

	/**
	 * Vytvorenie lieku z udajov, ktore boli vyplne vo formulari.
	 * @param $data
	 * @throws \Exception
	 */
	public function createMedicine($data)
	{
		$medicine = new Medicine();
		$medicine->idSukl = $data->id_sukl;
		$medicine->name = $data->name;
		$medicine->description = $data->description;
		$medicine->type = $data->type;
		$medicine->price = $data->price;

		$this->entityManager->persist($medicine);
		$this->entityManager->flush();
	}

	public function editMedicine($data, Medicine $medicine)
	{
		$medicine->name = $data->name;
		$medicine->description = $data->description;
		$medicine->type = $data->type;
		$medicine->price = $data->price;

		$this->entityManager->flush();
	}

	public function deleteMedicine($id = NULL)
	{
		$medicine = NULL;
		if (is_null($id) || is_null($medicine = $this->getMedicine($id)))
			throw new \InvalidArgumentException("Liek neexistuje.");

		$this->entityManager->remove($medicine);
		$this->entityManager->flush();
	}

	public function importContributionsFromJsonString($json)
	{
		$json = Json::decode($json);

		if (!isset($json->contributions))
			throw new \InvalidArgumentException;

		if (!is_array($json->contributions))
			throw new \InvalidArgumentException;

		foreach ($json->contributions as $contribution) {
			if (!isset($contribution->sukl_id))
				continue;

			if (!is_string($contribution->sukl_id))
				continue;

			if (!isset($contribution->amount))
				continue;

			if (!is_int($contribution->amount))
				continue;

			$medicine = $this->getMedicineByIdSukl($contribution->sukl_id);

			if (is_null($medicine))
				continue;

			$medicine->contribution = $contribution->amount;

			$this->entityManager->flush();
		}
	}
}
