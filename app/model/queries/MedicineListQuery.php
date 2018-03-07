<?php

namespace App\Model\Queries;

use App\Model\Entities\Medicine;
use Doctrine\ORM\QueryBuilder;
use Kdyby;
use Kdyby\Doctrine\QueryObject;
use Kdyby\Persistence\Queryable;

/**
 * Class MedicineListQuery
 * @package App\Model\Queries
 */
class MedicineListQuery extends QueryObject
{
	/** @var array Pole filtrov, ktore sa nasledne aplikuju na dotaz */
	private $filters = array();
	/**
	 * DQL dotaz na vyber vsetkych liekov s pozadovanimy filtrami.
	 * @param Queryable $repository
	 * @return \Doctrine\ORM\Query|QueryBuilder|Kdyby\Doctrine\QueryBuilder
	 */
	protected function doCreateQuery(Queryable $repository)
	{
		return $repository->createQueryBuilder()
			->addSelect("m")
			->from(Medicine::class, "m");
	}
}
