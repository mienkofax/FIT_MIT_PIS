<?php

namespace App\Forms;

use App\Model\Facades\MedicineFacade;
use App\Model\Facades\SupplierFacade;
use App\Model\Facades\UserFacade;
use Doctrine\DBAL\Exception\UniqueConstraintViolationException;
use Nette\Application\UI\Form;
use Nette\Security\User;

/**
 * Tovaren pre vygenerovanie formulara na pracu s dodavatelmi.
 * @package App\Forms
 */
class SupplierFormFactory extends BaseFormFactory
{
	/** @var MedicineFacade */
	private $supplierFacade;

	/**
	 * Nastavenie automatickeho injektovania zadanych tried pre dalsiu pracu.
	 * @param UserFacade $userFacade
	 * @param User $user
	 * @param SupplierFacade $supplierFacade
	 */
	public function __construct(
		UserFacade $userFacade,
		User $user,
		SupplierFacade $supplierFacade
		)
	{
		parent::__construct($userFacade, $user);
		$this->supplierFacade = $supplierFacade;
	}

	private function createForm()
	{
		$form = new Form();
		$form->addText("name", "Meno")
			->setRequired("Musí byť zadané meno.");

		$form->addText("city", "Mesto")
			->setRequired("Musí byť zadané mesto.");

		$form->addText("street", "Ulica")
			->setRequired("Musí byť zadaná ulica.");

		$form->addText("house", "Číslo domu")
			->setRequired("Musí byť zadané číslo domu.");

		return UtilForm::toBootstrapForm($form);
	}

	/**
	 * Vytvorenie formulara pre vlozenie dodavatela.
	 * @return Form
	 */
	public function createCreateSupplierForm()
	{
		$form = $this->createForm();

		$form->addSubmit("create", "Vytvoriť dodávateľa")
			->setAttribute('class', 'btn-primary');

		$form->onSuccess[] = array($this, "createSupplierSubmitted");

		return $form;
	}

	/**
	 * Formular pre editaciu dodavatelov.
	 * @return Form
	 */
	public function createEditSupplierForm()
	{
		$form = $this->createForm();

		$form->addSubmit("create", "Editovať dodávateľa")
			->setAttribute('class', 'btn-primary');

		$form->addHidden("supplierId");
		$form->onSuccess[] = array($this, "editSupplierSubmitted");

		return $form;
	}

	/**
	 * Operacia, ktora sa zavola po stlaceni tlacidla na vytvorenie
	 * dodavatela.
	 * @param $form
	 * @param $values
	 * @throws \Exception
	 */
	public function createSupplierSubmitted($form, $values)
	{
		try {
			$this->supplierFacade->createSupplier($values);
		}
		catch (UniqueConstraintViolationException $ex) {
			$form->addError("Zadaný dodávateľ už existuje.");
		}
	}

	public function editSupplierSubmitted($form, $values)
	{
		$supplier = $this->supplierFacade->getSupplier($values->supplierId);
		if (is_null($supplier))
			throw new \InvalidArgumentException("Dodávateľ neexistuje.");

		try {
			$this->supplierFacade->editSupplier($values, $supplier);
		}
		catch (UniqueConstraintViolationException $ex) {
			$form->addError("Zadaný dodávateľ už existuje.");
		}
	}
}
