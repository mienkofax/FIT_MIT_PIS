<?php

namespace App\Model\Facades;

use App\Model\Entities\OrderItem;
use App\Model\Entities\OrderMedicine;
use App\Model\Queries\OrderMedicineListQuery;
use Kdyby\Doctrine\EntityManager;
use Nette;
use Nette\Utils\DateTime;

/**
 * Trieda pre pracu s liekmi, umoznuje vytvorit novy liek, vyhladat
 * liek.
 * @package App\Model\Facades
 */
class OrderMedicineFacade extends BaseFacade
{
	use Nette\SmartObject;

	/**
	 * @var MedicineFacade
	 */
	private $medicineFacade;

	/** @var SupplierFacade */
	private $supplierFacade;

	/** @var StockMedicineFacade */
	private $stockMedicineFacade;

	public function __construct(
		EntityManager $entityManager,
		MedicineFacade $medicineFacade,
		SupplierFacade $supplierFacade,
		StockMedicineFacade $stockMedicineFacade
		)
	{
		parent::__construct($entityManager);
		$this->medicineFacade = $medicineFacade;
		$this->supplierFacade = $supplierFacade;
		$this->stockMedicineFacade = $stockMedicineFacade;
	}

	/**
	 * Vyhladanie a vratenie objednavky lieku na zaklade zadaneho id.
	 * @param $id int Id objednavky
	 * @return null|object
	 * @throws \Doctrine\ORM\ORMException
	 * @throws \Doctrine\ORM\OptimisticLockException
	 * @throws \Doctrine\ORM\TransactionRequiredException
	 */
	public function getOrderMedicine($id)
	{
		if (isset($id))
			return $this->entityManager->find(OrderItem::class, $id);

		return NULL;
	}

	public function getAllAsArray($column, $sort)
	{
		$query = new OrderMedicineListQuery();
		$query->orderBy($column, $sort);

		return $this->entityManager->fetch($query)->toArray();
	}

	/**
	 * Vytvorenie objednavky lieku a vlozenie do db.
	 * Najprv sa vytvori objednavka a potom sa postupne vytvaraju
	 * jednotlive polozky a tie sa pridavaju k objednavke,
	 * nakoniec sa to zapise do db.
	 * @param $data
	 * @param $user
	 * @throws \Exception
	 */
	public function createOrder($data, $user)
	{
		$order = new OrderMedicine();
		$order->totalPrice = 0;
		$order->createdTime = new DateTime();
		$order->user = $user;
		$this->entityManager->persist($order);

		foreach ($data['items'] as $key => $item) {
			$id = array(
				"medicine" => $this->medicineFacade
					->getMedicine($item['medicine_id']),
				"supplier" => $this->supplierFacade
					->getSupplier($item['supplier_id']));

			$stockItem = $this->stockMedicineFacade->getStockMedicine($id);

			if ($stockItem->count < $item['count']) {
				throw new \InvalidArgumentException(
					"Bolo zadaných viacej kusov ako je na sklade."
				);
			}
		}

		foreach ($data['items'] as $key => $item) {
			$medicine = $this->medicineFacade
				->getMedicine($item['medicine_id']);

			$supplier = $this->supplierFacade
				->getSupplier($item['supplier_id']);

			$id = array("medicine" => $medicine->id, "supplier" => $supplier->id);
			$stockItem = $this->stockMedicineFacade->getStockMedicine($id);

			$orderItem = new OrderItem();
			$orderItem->count = $item['count'];
			$orderItem->price = $medicine->price;
			$order->totalPrice += $medicine->price * $item['count'];
			$stockItem->count -= $item['count'];

			$orderItem->contribution = $medicine->contribution;
			$orderItem->medicines = $medicine;
			$orderItem->suppliers = $supplier;

			$order->addOrderItem($orderItem);
			$this->entityManager->persist($orderItem);
		}

		$this->entityManager->flush();
	}
}
