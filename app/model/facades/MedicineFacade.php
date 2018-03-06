<?php

namespace App\Model\Facades;

use App\Model\Entities\Medicine;
use Nette;

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

	/**
	 * Vytvorenie lieku z udajov, ktore boli vyplne vo formulari.
	 * @param $data
	 * @throws \Exception
	 */
	public function createMedicine($data)
	{
		$medicine = new Medicine();
		$medicine->id = $data->id;
		$medicine->name = $data->name;
		$medicine->description = $data->description;
		$medicine->type = $data->type;

		$this->entityManager->persist($medicine);
		$this->entityManager->flush();
	}
}
