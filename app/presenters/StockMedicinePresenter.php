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
}
