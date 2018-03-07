<?php

namespace App\Forms;

use App\Model\Facades\MedicineFacade;
use App\Model\Facades\StockMedicineFacade;
use App\Model\Facades\SupplierFacade;
use App\Model\Facades\UserFacade;
use InvalidArgumentException;
use Doctrine\DBAL\Exception\UniqueConstraintViolationException;
use Nette;
use Nette\Application\UI\Form;
use Nette\Security\User;
use Nette\Utils\ArrayHash;

/**
 * Tovaren pre vygenerovanie formulara na pracu s skladovymi zasobami.
 * @package App\Forms
 */
class StockMedicineFormFactory extends BaseFormFactory
{
	use Nette\SmartObject;

	/** @var StockMedicineFacade */
	private $stockMedicineFacade;

	/** @var MedicineFacade */
	private $medicineFacade;

	/** @var SupplierFacade */
	private $supplierFacade;

	/**
	 * Nastavenie automatickeho injektovania zadanych tried pre dalsiu pracu.
	 * @param UserFacade $userFacade
	 * @param User $user
	 * @param StockMedicineFacade $stockMedicineFacade
	 * @param MedicineFacade $medicineFacade
	 * @param SupplierFacade $supplierFacade
	 */
	public function __construct(
		UserFacade $userFacade,
		User $user,
		StockMedicineFacade $stockMedicineFacade,
		MedicineFacade $medicineFacade,
		SupplierFacade $supplierFacade
		)
	{
		parent::__construct($userFacade, $user);
		$this->stockMedicineFacade = $stockMedicineFacade;
		$this->medicineFacade = $medicineFacade;
		$this->supplierFacade = $supplierFacade;
	}

	/**
	 * Vytvorenie formulara pre vlozenie skladovej zasoby.
	 * @return Form
	 * @throws \Exception
	 */
	public function createCreateStockMedicine()
	{
		$form = new Form();

		$form->addText("count", "Počet liekov")
			->addRule(Form::INTEGER, "Počeť liekov musí byť číslo.")
			->addRule(Form::MIN, "Počeť liekov musí byť kladné.", 0)
			->setRequired("Musí byť zadaný počet liekov.");

		$form->addText("price", "Cena lieku")
			->addRule(Form::FLOAT, "Cena lieku musí byť číslo")
			->setRequired("Musí byť zadaná cena lieku.");

		$form->addSelect("medicine", "Liek")
			->setItems($this->medicineFacade->getIdsAndName())
			->setAttribute("class", "form-control")
			->setPrompt("Zoznam liekov")
			->setRequired("Musí byť zadaný liek.");

		$form->addSelect("supplier", "Dodávateľ")
			->setItems($this->supplierFacade->getIdsAndName())
			->setAttribute("class", "form-control")
			->setPrompt("Zoznam dodávateľov")
			->setRequired("Musí byť zadaný dodávateľ.");

		$form->addSubmit("stockMedicineCreate", "Pridať skladovú zásobu")
			->setAttribute('class', 'btn-primary');

		$form->onSuccess[] = array($this, "createStockMedicineSubmitted");

		return UtilForm::toBootstrapForm($form);
	}

	/**
	 * Operacia, ktora sa zavola po stlaceni tlacidla na vytvorenie
	 * skladovej zasboby.
	 * @param Form $form
	 * @param ArrayHash $value
	 * @throws \Doctrine\ORM\ORMException
	 * @throws \Doctrine\ORM\OptimisticLockException
	 * @throws \Doctrine\ORM\TransactionRequiredException
	 * @throws \Exception
	 */
	public function createStockMedicineSubmitted(Form $form, ArrayHash $value)
	{
		$medicine = $this->medicineFacade->getMedicine($value->medicine);
		if (is_null($medicine))
			throw new InvalidArgumentException("Požadovaný liek neexistuje.");

		$supplier = $this->supplierFacade->getSupplier($value->supplier);
		if (is_null($supplier))
			throw new InvalidArgumentException("Požadovaný dodávateľ neexistuje");

		try {
			$this->stockMedicineFacade
				->createStockMedicine($value, $medicine, $supplier);
		}
		catch (UniqueConstraintViolationException $ex) {
			$form->addError("Záznam s týmto liekom už existuje.");
		}
	}
}
