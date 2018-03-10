<?php

namespace App\Presenters;

use App\Forms\StockMedicineFormFactory;
use App\Model\Facades\MedicineFacade;
use App\Model\Facades\StockMedicineFacade;
use App\Model\Facades\SupplierFacade;
use Nette\Application\UI\Form;

/**
 * Presenter pre pracu so skladovymi zasobami.
 * @package App\Presenters
 */
class StockMedicinePresenter extends BasePresenter
{
	/** @var StockMedicineFacade */
	private $stockMedicineFacade;

	/** @var MedicineFacade */
	private $medicineFacade;

	/** @var SupplierFacade */
	private $supplierFacade;

	/** @var StockMedicineFormFactory */
	private $formFactory;

	private $searchedStockMedicine;

	/**
	 * Konstruktor s injektovanymi triedami pre pracu so skladovymi
	 * zasobami.
	 * @param StockMedicineFacade $stockMedicineFacade
	 * @param MedicineFacade $medicineFacade
	 * @param SupplierFacade $supplierFacade
	 * @param StockMedicineFormFactory $formFactory
	 */
	public function __construct(
		StockMedicineFacade $stockMedicineFacade,
		MedicineFacade $medicineFacade,
		SupplierFacade $supplierFacade,
		StockMedicineFormFactory $formFactory
		)
	{
		parent::__construct();
		$this->stockMedicineFacade = $stockMedicineFacade;
		$this->medicineFacade = $medicineFacade;
		$this->supplierFacade = $supplierFacade;
		$this->formFactory = $formFactory;
	}

	/**
	 * Nastavenie premenych do sablony, ktore obsahuju pocet
	 * liekov a dodavatelov.
	 */
	public function renderCreate()
	{
		$this->template->medicineCount =
			$this->medicineFacade->getMedicinesCount();
		$this->template->supplierCount =
			$this->supplierFacade->getSuppliersCount();
	}

	public function renderManage($column, $sort)
	{
		$this->template->stockItems =
			$this->stockMedicineFacade->getAllAsArray($column, $sort);
	}

	public function renderEdit()
	{
		$this->template->stockItem = $this->searchedStockMedicine;
	}

	public function actionEdit($medicineId = NULL, $supplierId = NULL)
	{
		$id = array("medicine" => $medicineId, "supplier" => $supplierId);
		$this->searchedStockMedicine = $tmp =
			$this->stockMedicineFacade->getStockMedicine($id);

		if (is_null($tmp))
			return;

		$this["editStockMedicineForm"]->setDefaults(
			array(
				"medicineId" => $medicineId,
				"supplierId" => $supplierId,
				"count" => $tmp->count,
				"medicine" => $tmp->medicine->id,
				"supplier" => $tmp->supplier->id,
				"price" => $tmp->price
			)
		);
	}

	/**
	 * Vytvorenie komponenty a vratenie komponenty pre pridanie
	 * skladovej zasoby.
	 * @return Form
	 * @throws \Exception
	 */
	public function createComponentCreateStockMedicineForm()
	{
		$form = $this->formFactory->createCreateStockMedicine();
		$form->onSuccess[] = function (Form $form) {
			$tmp = $form->getPresenter();
			$tmp->flashMessage("Úspešné vytvorenie skladovej zásoby.");
			$tmp->redirect("this");
		};

		return $form;
	}
	public function createComponentEditStockMedicineForm()
	{
		$form = $this->formFactory->createEditStockMedicine();
		$form->onSuccess[] = function (Form $form) {
			$tmp = $form->getPresenter();
			$tmp->flashMessage("Skladová zásoba bola úspešne upravená.");
			$tmp->redirect("StockMedicine:manage");
		};

		return $form;
	}

	public function handleRemoveStockMedicine($medicineId, $supplierId)
	{
		$id = array("medicine" => $medicineId, "supplier" => $supplierId);

		try {
			$this->stockMedicineFacade->deleteStockMedicine($id);
			$this->flashMessage("Skladová zásoba bola odstránená.");
		}
		catch (\InvalidArgumentException $ex) {
			$this->flashMessage($ex->getMessage());
		}
		catch (\Doctrine\DBAL\Exception\ForeignKeyConstraintViolationException $ex) {
			$this->flashMessage("Skladová zásoba má závislosti.");
		}

		$this->redirect("StockMedicine:manage");
	}
}
