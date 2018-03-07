<?php

namespace App\Presenters;

use App\Forms\OrderMedicineFormFactory;
use App\Model\Facades\MedicineFacade;
use App\Model\Facades\OrderMedicineFacade;
use App\Model\Facades\StockMedicineFacade;
use Nette\Application\UI\Form;

/**
 * Presenter pre objednavky.
 * @package App\Presenters
 */
class OrderMedicinePresenter extends BasePresenter
{
	/** @var OrderMedicineFacade */
	private $orderFacade;

	/** @var MedicineFacade */
	private $medicineFacade;

	/** @var OrderMedicineFormFactory */
	private $formFactory;

	/** @var StockMedicineFacade */
	private $stockMedicine;

	/**
	 * Konstruktor s injektovanymi triedami pre pracu s objednavkami.
	 * @param OrderMedicineFacade $orderFacade
	 * @param OrderMedicineFormFactory $formFactory
	 * @param MedicineFacade $medicineFacade
	 * @param StockMedicineFacade $stockMedicineFacade
	 */
	public function __construct(
		OrderMedicineFacade $orderFacade,
		OrderMedicineFormFactory $formFactory,
		MedicineFacade $medicineFacade,
		StockMedicineFacade $stockMedicineFacade
		)
	{
		parent::__construct();
		$this->orderFacade = $orderFacade;
		$this->formFactory = $formFactory;
		$this->medicineFacade = $medicineFacade;
		$this->stockMedicine = $stockMedicineFacade;
	}

	/**
	 * Nastavenie premennych do sablony.
	 */
	public function renderCreate()
	{
		$this->template->db = $this->medicineFacade->toJSON();
	}

	/**
	 * Vytvorenie komponenty a vratenie komponenty pre pridanie objednavky,
	 * @return Form
	 */
	public function createComponentCreateOrderForm()
	{
		$form = $this->formFactory->createCreateOrder();


		return $form;
	}
}
