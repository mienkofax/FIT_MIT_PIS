<?php

namespace App\Model\Facades;

use App\Model\Entities\Supplier;
use Nette;

/**
 * Trieda pre pracu s dodavatelmi, umoznuje vytvarat dodavatelov
 * a vyhladat medzi dodavatelmi.
 * @package App\Model\Facades
 */
class SupplierFacade extends BaseFacade
{
	use Nette\SmartObject;

	/**
	 * Vyhladanie a vratenie dodavatela na zaklade zadaneho id.
	 * @param $id
	 * @return null|object
	 * @throws \Doctrine\ORM\ORMException
	 * @throws \Doctrine\ORM\OptimisticLockException
	 * @throws \Doctrine\ORM\TransactionRequiredException
	 */
	public function getSupplier($id)
	{
		if (isset($id))
			return $this->entityManager->find(Supplier::class, $id);

		return NULL;
	}

	/**
	 * Asociativne pole dodavatelov s ich id a nazvami.
	 * @return array
	 */
	public function getIdsAndName()
	{
		return $this->entityManager->getRepository(Supplier::class)
			->findPairs([], "name", [], "id");
	}

	/*
	 * Zistenie poctu dodavatelov.
	 * @return int pocet dodavatelov
	 * @throws \Doctrine\ORM\NoResultException
	 * @throws \Doctrine\ORM\NonUniqueResultException
	 */
	public function getSuppliersCount()
	{
		return (int) $this->entityManager->createQuery("
				SELECT COUNT (s.id)
				FROM App\Model\Entities\Supplier s
		")->getSingleScalarResult();
	}

	/**
	 * Vytvorenie dodavatela z dat zastalych z formulara.
	 * @param $data
	 * @throws \Exception
	 */
	public function createSupplier($data)
	{
		$supplier = new Supplier();
		$supplier->name = $data->name;
		$supplier->city = $data->city;
		$supplier->street = $data->street;
		$supplier->houseNumber = $data->house;

		$this->entityManager->persist($supplier);
		$this->entityManager->flush();

	}
}
