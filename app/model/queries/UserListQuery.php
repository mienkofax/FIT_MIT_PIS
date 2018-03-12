<?php

namespace App\Model\Queries;

use App\Model\Entities\User;
use Doctrine\ORM\QueryBuilder;
use Kdyby;
use Kdyby\Doctrine\QueryObject;
use Kdyby\Persistence\Queryable;

/**
 * @package App\Model\Queries
 */
class UserListQuery extends QueryObject
{
	const SUPPORTED_COLUMNS = array("surname", "role", "registrationDate");

	/** @var array Pole filtrov, ktore sa nasledne aplikuju na dotaz */
	private $filters = array();
	/**
	 * DQL dotaz na vyber vsetkych uzivatelov s pozadovanimy filtrami.
	 * @param Queryable $repository
	 * @return \Doctrine\ORM\Query|QueryBuilder|Kdyby\Doctrine\QueryBuilder
	 */
	protected function doCreateQuery(Queryable $repository)
	{
		$qb = $repository->createQueryBuilder()
			->select("u")
			->from(User::class, "u");

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
	public function orderBy($column = "id", $order = "asc")
	{
		if ($order != "asc" && $order != "desc")
			$order = "asc";

		if (!in_array($column, self::SUPPORTED_COLUMNS))
			$column = "id";

		$this->filters[] =
			function (QueryBuilder $qb) use ($order, $column) {
				$qb->addOrderBy("u." . $column, $order);
			};

		return $this;
	}
}
