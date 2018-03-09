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
    const SUPPORTED_COLUMNS = array("id", "name", "type", "contribution");

	/** @var array Pole filtrov, ktore sa nasledne aplikuju na dotaz */
	private $filters = array();
	/**
	 * DQL dotaz na vyber vsetkych liekov s pozadovanimy filtrami.
	 * @param Queryable $repository
	 * @return \Doctrine\ORM\Query|QueryBuilder|Kdyby\Doctrine\QueryBuilder
	 */
	protected function doCreateQuery(Queryable $repository)
	{
		$qb = $repository->createQueryBuilder()
			->addSelect("m")
			->from(Medicine::class, "m");

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
                $qb->addOrderBy("m." . $column, $order);
            };

        return $this;
    }
}
