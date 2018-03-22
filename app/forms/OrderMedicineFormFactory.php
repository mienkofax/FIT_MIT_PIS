<?php

namespace App\Forms;

use App\Model\Facades\MedicineFacade;
use App\Model\Facades\OrderMedicineFacade;
use App\Model\Facades\StockMedicineFacade;
use App\Model\Facades\UserFacade;
use Nette\Application\Responses\FileResponse;
use Nette\Application\UI\Form;
use Doctrine\DBAL\Exception\UniqueConstraintViolationException;;
use Nette\Forms\Container;
use Nette\Forms\Controls\SubmitButton;
use Nette\Security\User;
use Nette\Utils\DateTime;
use Nette\Utils\FileSystem;

/**
 * Tovaren pre vygenerovanie formulara na pracu s objednavkami.
 * @package App\Forms
 */
class OrderMedicineFormFactory extends BaseFormFactory
{
	/** @var OrderMedicineFacade */
	private $orderFacade;

	/** @var MedicineFacade */
	private $medicineFacade;

	/** @var StockMedicineFacade */
	private $stockMedicine;

	/**
	 * Nastavenie automatickeho injektovania zadanych tried pre dalsiu pracu.
	 * @param UserFacade $userFacade
	 * @param User $user
	 * @param OrderMedicineFacade $orderFacade
	 * @param MedicineFacade $medicineFacade
	 * @param StockMedicineFacade $stockMedicineFacade
	 */
	public function __construct(
		UserFacade $userFacade,
		User $user,
		OrderMedicineFacade $orderFacade,
		MedicineFacade $medicineFacade,
		StockMedicineFacade $stockMedicineFacade
		)
	{
		parent::__construct($userFacade, $user);
		$this->orderFacade = $orderFacade;
		$this->medicineFacade = $medicineFacade;
		$this->stockMedicine = $stockMedicineFacade;
	}

	/**
	 * Vytvorenie formulara pre objednavky.
	 * @return Form
	 */
	public function createCreateOrder()
	{
		$form = new Form;

		$form->addGroup('Lieky');
		$items = $form->addDynamic('items',
			function (Container $item) {
				$item->addSelect("medicine_id", "Liek")
					->setItems($this->stockMedicine->getMedicinesAsArray())
					->setAttribute("class", "form-control")
					->setPrompt("Zoznam liekov")
					->setRequired();

				$item->addSelect("supplier_id", "Dodávateľ")
					->setAttribute("class", "form-control")
					->setItems($this->stockMedicine->getSuppliersAsArray())
					->setPrompt("Zoznam dodávateľov")
					->setRequired();

				$item->addText("price", "Predajná cena lieku")
					->setDisabled();

				$item->addText("type", "Typ lieku")
					->setDisabled();

				$item->addText("in_stock", "Na skade")
					->setDisabled();

				$item->addText("count", "Počet")
					->addRule(Form::NUMERIC, "Počet liekov musí byť číslo.")
					->setRequired();

				$item->addSubmit("remove", "Odstrániť liek z objednávky")
					->setValidationScope(false)
					->setAttribute("class", "btn-danger")
					->onClick[] = [$this, "removeElementClicked"];
			}, 1
		);

		$form->addGroup('');
		$items->addSubmit('add', 'Pridat liek do objednávky')
			->setValidationScope(false)
			->setAttribute('class', 'btn-success')
			->onClick[] = [$this, 'addElementClicked'];

		$form->addGroup('');
		$form->addSubmit('submit', 'Uložiť objednávku')
			->setAttribute('class', 'btn-primary')
			->onClick[] = [$this, 'createOrderSubmitted'];

		return UtilForm::toBootstrapForm($form);
	}

	public function createExportReportForm()
	{
		$form = new Form;

		$form->addText("dateFrom", "Dátum od")
			->setRequired("Datum je povinný údaj!")
			->setAttribute("class", "dtpicker form-control")
			->setAttribute("placeholder", "dd.mm.rrrr")
			->addRule($form::PATTERN, "Datum musí být ve formátu dd.mm.rrrr",
				"(0[1-9]|[12][0-9]|3[01])\.(0[1-9]|1[012])\.(19|20)\d\d");

		$form->addText("dateTo", "Dátum do")
			->setRequired("Datum je povinný údaj!")
			->setAttribute("class", "dtpicker form-control")
			->setAttribute("placeholder", "dd.mm.rrrr")
			->addRule($form::PATTERN, "Datum musí být ve formátu dd.mm.rrrr",
				"(0[1-9]|[12][0-9]|3[01])\.(0[1-9]|1[012])\.(19|20)\d\d");

		$form->addSubmit("export", "Exportovať výkaz")
			->setAttribute("class", "btn-primary");

		$form->onSuccess[] = array($this, "exportReportSubmitted");

		return UtilForm::toBootstrapForm($form);
	}

	/**
	 * Operacia, ktora sa zavola po stlaceni tlacidla na vytvorenie objednacky.
	 * Nasledne sa ulozi objednavka do db.
	 * @param SubmitButton $button
	 * @throws \Exception
	 */
	public function createOrderSubmitted(SubmitButton $button)
	{
		if (empty($button->getForm()->getValues(true)['items'])) {
			$button->getForm()->addError("Objednávka musí obsahovať aspoň jednu položku.");
			return;
		}

		try {
			$user = $this->userFacade->getUser($this->user->id);
			$this->orderFacade->createOrder($button->getForm()->getValues(true), $user);

			$button->getForm()->onSuccess[] = function (Form $form) {
				$tmp = $form->getPresenter();
				$tmp->flashMessage("Objednávka bola úspešne vytvorená.");
				$tmp->redirect("OrderMedicine:detail");
			};
		}
		catch (UniqueConstraintViolationException $ex) {
			$button->getForm()->addError("Objednávka už existuje.");
		}
		catch (\InvalidArgumentException $ex) {
			$button->getForm()->addError($ex->getMessage());
		}
	}

	public function exportReportSubmitted(Form $form, $values)
	{
		$dateFrom = Datetime::createFromFormat("d.m.Y", $form->values->dateFrom);
		$dateToIncluding = Datetime::createFromFormat("d.m.Y", $form->values->dateTo);

		$version = (float) phpversion();
		if ($version < 7.1) {
			$dateFrom->setTime(0, 0, 0);
			$dateToIncluding->setTime(0, 0, 0);
		}
		else {
			$dateFrom->setTime(0, 0, 0, 0);
			$dateToIncluding->setTime(0, 0, 0, 0);
		}

		if ($dateFrom > $dateToIncluding) {
			$form->addError("Dátum od musí byť skorší dátum ako dátum do");
			return;
		}

		$report = $this->orderFacade->getReportAsJson($dateFrom, $dateToIncluding);

		FileSystem::write("tmp/report.json", $report);
		$form->getPresenter()->sendResponse(New FileResponse("tmp/report.json"));
	}
}
