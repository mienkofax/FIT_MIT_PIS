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

	private $searchedSupplier;

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
	public function createComponentEditSupplierForm()
	{
		$form = $this->formFactory->createEditSupplierForm();
		$form->onSuccess[] = function (Form $form) {
			$tmp = $form->getPresenter();
			$tmp->flashMessage("Dodávateľ bol úspešne upravený.");
			$tmp->redirect("Supplier:detail", $this["editSupplierForm"]['supplierId']->getValue());
		};

		return $form;
	}

	public function renderManage($column, $sort)
	{
		$this->template->suppliers =
			$this->supplierFacade->getAllAsArray($column, $sort);
	}

	public function renderEdit()
	{
		$this->template->supplier = $this->searchedSupplier;
	}

	public function renderDetail($id = NULL)
	{
		if (is_null($id))
			return;

		$supplier = $this->supplierFacade->getSupplier($id);

		if (is_null($supplier))
			return;

		$this->template->supplier = $supplier;
	}

	public function actionEdit($id = NULL)
	{
		$this->searchedSupplier = $tmp = $this->supplierFacade->getSupplier($id);
		if (is_null($tmp))
			return;

		$this["editSupplierForm"]->setDefaults(
			array(
				"supplierId" => $id,
				"name" => $tmp->name,
				"city" => $tmp->city,
				"street" => $tmp->street,
				"house" => $tmp->houseNumber
			)
		);
	}

	public function handleRemoveSupplier($id)
	{
		try {
			$this->supplierFacade->deleteSupplier($id);
			$this->flashMessage("Dodávateľ bol odstránený.");
		}
		catch (\InvalidArgumentException $ex) {
			$this->flashMessage($ex->getMessage());
		}
		catch (\Doctrine\DBAL\Exception\ForeignKeyConstraintViolationException $ex) {
			$this->flashMessage("Dodávateľ obsahuje skladové zásoby,
			pred jeho odstránením zmažte skladové zásoby.", "danger");
		}

		$this->redirect("Supplier:manage");
	}
}
