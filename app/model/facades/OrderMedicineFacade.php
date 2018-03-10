<?php

namespace App\Model\Facades;

use App\Model\Entities\OrderItem;
use App\Model\Entities\OrderMedicine;
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

	public function __construct(
		EntityManager $entityManager,
		MedicineFacade $medicineFacade

		)
	{
		parent::__construct($entityManager);
		$this->medicineFacade = $medicineFacade;
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
			$stockItem = new OrderItem();
			$stockItem->price = $item['price'];
			$stockItem->count = $item['count'];

			$medicine = $this->medicineFacade
				->getMedicine($item['medicine_id']);

			$stockItem->contribution = $medicine->contribution;
			$stockItem->medicines = $medicine;

			$order->addOrderItem($stockItem);
			$this->entityManager->persist($stockItem);
		}

		$this->entityManager->flush();
	}
}
