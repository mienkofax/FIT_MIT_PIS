<?php

namespace App\Model\Queries;

use App\Model\Entities\OrderMedicine;
use Doctrine\ORM\QueryBuilder;
use Kdyby;
use Kdyby\Doctrine\QueryObject;

/**
 * Trieda, ktora tvori DQL dotazy nad objednavkami.
 * @package App\Model\Queries
 */
class OrderMedicineListQuery extends QueryObject
{
	const SUPPORTED_COLUMNS = array("id", "user", "totalPrice", "createdTime");

	/** @var array Pole filtrov, ktore sa nasledne aplikuju na dotaz */
	private $filters = array();

	/**
	 * DQL dotaz na vyber vsetkych objednavok s pozadovanimy filtrami.
	 * @param \Kdyby\Persistence\Queryable $repository
	 * @return \Doctrine\ORM\Query|\Doctrine\ORM\QueryBuilder
	 */
	protected function doCreateQuery(Kdyby\Persistence\Queryable $repository)
	{
		$qb = $repository->createQueryBuilder()
			->select("om")
			->from(OrderMedicine::class, "om");

		foreach ($this->filters as $filter)
			$filter($qb);

		return $qb;
	}

	/**
	 * Metoda pre zoradenie na zaklade zadaneho stlpca a sposobu zoradenia.
	 * @param string $column nazov stlpca
	 * @param string $order typ zoradenie ASC|DESC
	 * @return $this
	 */
	public function orderBy($column = "id", $order = "desc")
	{
		if ($order != "asc" && $order != "desc")
			$order = "desc";

		if (!in_array($column, self::SUPPORTED_COLUMNS))
			$column = "id";

		$this->filters[] =
			function (QueryBuilder $qb) use ($order, $column) {
				$qb->addOrderBy("om." . $column, $order);
			};

		return $this;
	}
}
