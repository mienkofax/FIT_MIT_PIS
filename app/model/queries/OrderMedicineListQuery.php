<?php

namespace App\Model\Queries;

use App\Model\Entities\OrderMedicine;
use Doctrine\ORM\QueryBuilder;
use Kdyby;
use Kdyby\Doctrine\QueryObject;
use Nette\Utils\DateTime;

/**
 * Trieda, ktora tvori DQL dotazy nad objednavkami.
 * @package App\Model\Queries
 */
class OrderMedicineListQuery extends QueryObject
{
	const SUPPORTED_COLUMNS = array("id", "user", "totalPrice", "createdTime", "storno");

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

	public function canceled($bool)
	{
		$this->filters[] = function (QueryBuilder $qb) use ($bool) {
				$qb->andWhere("om.storno = :storno")
					->setParameter("storno", $bool);
			};

		return $this;
	}

	public function fromDate(Datetime $from)
	{
		$this->filters[] = function (QueryBuilder $qb) use ($from) {
			$qb->andWhere("om.createdTime >= :from")
				->setParameter("from", $from);
		};

		return $this;
	}

	public function toDateExcluding(Datetime $to)
	{
		$this->filters[] = function (QueryBuilder $qb) use ($to) {
			$qb->andWhere("om.createdTime < :to")
				->setParameter("to", $to);
		};

		return $this;
	}
}
