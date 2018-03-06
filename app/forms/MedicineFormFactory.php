<?php

namespace App\Forms;

use App\Model\Facades\MedicineFacade;
use App\Model\Facades\UserFacade;
use Nette\Database\UniqueConstraintViolationException;
use Nette;
use Nette\Application\UI\Form;
use Nette\Security\User;
use Nette\Utils\ArrayHash;

/**
 * Tovaren pre vygenerovanie formulara na pracu s liekmi.
 * @package App\Forms
 */
class MedicineFormFactory extends BaseFormFactory
{
	use Nette\SmartObject;

	const TYPE_MEDICINE = array(
		0 => "Liek bez predpisu",
		1 => "Liek na predpis",
	);

	/** @var MedicineFacade Fasada pre pracu s liekmi */
	private $medicineFacade;

	/**
	 * Nastavenie automatickeho injektovania zadanych tried pre dalsiu pracu.
	 * @param UserFacade $userFacade
	 * @param User $user
	 * @param MedicineFacade $medicineFacade Fasada pre pracu s liekami.
	 */
	public function __construct(
		UserFacade $userFacade,
		User $user,
		MedicineFacade $medicineFacade
		)
	{
		parent::__construct($userFacade, $user);
		$this->medicineFacade = $medicineFacade;
	}

	/**
	 * Vytvorenie formulara pre vlozenie lieku.
	 * @return Form Formular s moznostou vytvorenia lieku.
	 */
	public function createCreateMedicine()
	{
		$form = new Form;
		$form->addText("id", "Kód lieku")
			->setRequired("Musí byť zadaný kód lieku.")
			->addRule(Form::INTEGER, "Položka %label musí byť číslo.");

		$form->addText("name", "Názov lieku")
			->setRequired("Musí byť zadaný názov lieku.");

		$form->addTextArea("description", "Popis lieku", 50,2)
			->setAttribute('class', 'form-control')
			->setRequired("Musí byť zadaný popis lieku");

		$form->addSelect("type", "Typ lieku", self::TYPE_MEDICINE)
			->setAttribute('class', 'form-control')
			->setRequired("Musí byť zadaný typ lieku.");

		$form->addSubmit("create", "Vložiť liek")
			->setAttribute('class', 'btn-primary');

		$form->onSuccess[] = array($this, "createMedicineSubmitted");

		return UtilForm::toBootstrapForm($form);
	}

	/**
	 * Operacia, ktora sa zavola po stlaceni tlacidla na vytvorenie
	 * lieku.
	 * @param Form $form
	 * @param ArrayHash $values
	 */
	public function createMedicineSubmitted(Form $form, ArrayHash $values)
	{
		try {
			$this->medicineFacade->createMedicine($values);
		}
		catch (UniqueConstraintViolationException $ex) {
			$form->addError("Liek so zadaným kódom už existuje.");
		}
	}
}
