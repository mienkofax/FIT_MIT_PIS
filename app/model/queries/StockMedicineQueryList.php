<?php

namespace App\Model\Queries;

use App\Model\Entities\StockMedicine;
use Doctrine\ORM\QueryBuilder;
use Kdyby;
use Kdyby\Doctrine\QueryObject;

/**
 * Trieda, ktora tvori DQL dotazy nad skladovymi zasobami.
 * @package App\Model\Queries
 */
class StockMedicineQueryList extends QueryObject
{
	/** @var array Pole filtrov, ktore sa nasledne aplikuju na dotaz */
	private $filters = array();

	/**
	 * DQL dotaz na vyber vsetkych skladovych zasob s pozadovanimy filtrami.
	 * @param \Kdyby\Persistence\Queryable $repository
	 * @return \Doctrine\ORM\Query|\Doctrine\ORM\QueryBuilder
	 */
	protected function doCreateQuery(Kdyby\Persistence\Queryable $repository)
	{
		$qb = $repository->createQueryBuilder()
			->select("sm")
			->from(StockMedicine::class, "sm");

		foreach ($this->filters as $filter)
			$filter($qb);

		return $qb;
	}

	/**
	 * Filter pre vyber skladovych zasob na zaklade zadaneho lieku.
	 * @param $id
	 * @return $this
	 */
	public function onlyMedicineSupplies($id)
	{
		$this->filters[] = function(QueryBuilder $qb) use ($id) {
			$qb->where("sm.medicine = :medicine")
				->setParameter("medicine", $id);
		};

		return $this;
	}

	/**
	 * Filter pre vyber skladovych zason na zaklade zadaneho id dodavatela.
	 * @param $id int Id dodavatela
	 * @return $this
	 */
	public function onlySupplierMedicines($id)
	{
		$this->filters[] = function(QueryBuilder $qb) use ($id) {
			$qb->andWhere("sm.supplier = :supplier")
				->setParameter("supplier", $id);
		};

		return $this;
	}
}
