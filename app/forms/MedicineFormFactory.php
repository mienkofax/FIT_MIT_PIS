<?php

namespace App\Forms;

use App\Model\Facades\MedicineFacade;
use App\Model\Facades\UserFacade;
use Kdyby\Console\InvalidApplicationModeException;
use Nette\Database\UniqueConstraintViolationException;
use Nette;
use Nette\Application\UI\Form;
use Nette\Security\User;
use Nette\Utils\ArrayHash;
use Nette\Utils\FileSystem;

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

	const MB = 1000000;

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

	public function createForm()
	{
		$form = new Form;

		$form->addText("name", "Názov lieku")
			->setRequired("Musí byť zadaný názov lieku.");

		$form->addTextArea("description", "Popis lieku", 50,2)
			->setAttribute('class', 'form-control')
			->setRequired("Musí byť zadaný popis lieku");

		$form->addText("price", "Predajná cena lieku")
			->addRule(Form::FLOAT, "Cena musí byť číslo.")
			->setRequired("Musí byť zadaná predajná cena lieku.");

		$form->addSelect("type", "Typ lieku", self::TYPE_MEDICINE)
			->setAttribute('class', 'form-control')
			->setRequired("Musí byť zadaný typ lieku.");

		return UtilForm::toBootstrapForm($form);
	}

	/**
	 * Vytvorenie formulara pre vlozenie lieku.
	 * @return Form Formular s moznostou vytvorenia lieku.
	 */
	public function createCreateMedicine()
	{
		$form = $this->createForm();

		$form->addText("id_sukl", "Kód lieku")
			->setRequired();

		$form->addSubmit("create", "Vložiť liek")
			->setAttribute('class', 'btn-primary');

		$form->onSuccess[] = array($this, "createMedicineSubmitted");

		return $form;
	}

	public function createEditMedicineForm()
	{
		$form = $this->createForm();

		$form->addText("id_sukl", "Kód lieku")
			->setDisabled()
			->setRequired();

		$form->addSubmit("create", "Editovať liek")
			->setAttribute('class', 'btn-primary');

		$form->addHidden("id");

		$form->onSuccess[] = array($this, "editMedicineSubmitted");

		return $form;
	}

	public function createImportContributionsForm()
	{
		$form = new Form();

		$form->addUpload("file", "Súbor s príspevkami")
			->setRequired("Musí byť zadaný súbor.")
			->addRule(Form::MAX_FILE_SIZE, "Maximálna veľkosť súboru je 1 MB", 1 * $this::MB)
			->setAttribute("class", "form-control");

		$form->addSubmit("import", "Importovať")
			->setAttribute("class", "btn-primary");

		$form->onSuccess[] = array($this, "importContributionsSubmitted");

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

	public function editMedicineSubmitted(Form $form, ArrayHash $values)
	{
		$medicine = $this->medicineFacade->getMedicine($values->id);
		if (is_null($medicine))
			throw new \InvalidArgumentException("Liek neexistuje.");

		try {
			$this->medicineFacade->editMedicine($values, $medicine);
		}
		catch (UniqueConstraintViolationException $ex) {
			$form->addError("Zadaný liek existuje.");
		}
	}

	public function importContributionsSubmitted(Form $form, ArrayHash $values)
	{
		$file = $values->file;
		$filePath = "upload/" . $file;

		$file->move($filePath);

		try {
			$json = FileSystem::read($filePath);
			FileSystem::delete($filePath);

			$this->medicineFacade->importContributionsFromJsonString($json);
		}
		catch (Nette\IOException $ex) {
			$form->addError("Problém so vstupným súborom.");
		}
		catch (Nette\Utils\JsonException $ex) {
			$form->addError("Vstupný súbor nie je JSON.");
		}
		catch (\InvalidArgumentException $ex) {
			$form->addError("Vstupný súbor má nesprávny formát.");
		}

	}
}
