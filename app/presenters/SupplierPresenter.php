<?php

namespace App\Presenters;

use App\Forms\SupplierFormFactory;
use App\Model\Facades\SupplierFacade;
use Nette\Application\UI\Form;

/**
 * Presenter pre pracu s dodavatelmi.
 * @package App\Presenters
 */
class SupplierPresenter extends BasePresenter
{
	/** @var SupplierFacade */
	private $supplierFacade;

	/** @var SupplierFormFactory */
	private $formFactory;

	/**
	 * Konstruktor s injektovanymi triedami pre pracu s dodavatelmi.
	 * @param SupplierFacade $supplierFacade
	 * @param SupplierFormFactory $supplierFormFactory
	 */
	public function __construct(
		SupplierFacade $supplierFacade,
		SupplierFormFactory $supplierFormFactory
		)
	{
		parent::__construct();
		$this->supplierFacade = $supplierFacade;
		$this->formFactory = $supplierFormFactory;
	}

	/**
	 * Vytvorenie komponenty a vratenie komponenty pre pridanie
	 * dodavatela.
	 * @return Form
	 */
	public function createComponentCreateSupplierForm()
	{
		$form = $this->formFactory->createCreateSupplierForm();
		$form->onSuccess[] = function (Form $form) {
			$tmp = $form->getPresenter();
			$tmp->flashMessage("Dodávateľ bol úspešne vytvorený.");
			$tmp->redirect("this");
		};

		return $form;
	}

	/**
	 * Nastavenie premennej do sablony.
	 * @param $column string
	 * @param $sort string
	 */
	public function renderManage($column, $sort)
	{
		$this->template->suppliers =
			$this->supplierFacade->getAllAsArray($column, $sort);
	}
}
