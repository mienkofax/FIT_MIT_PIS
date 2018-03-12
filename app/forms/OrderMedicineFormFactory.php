<?php

namespace App\Forms;

use App\Model\Facades\MedicineFacade;
use App\Model\Facades\OrderMedicineFacade;
use App\Model\Facades\StockMedicineFacade;
use App\Model\Facades\UserFacade;
use Nette\Application\UI\Form;
use Nette\Database\UniqueConstraintViolationException;
use Nette\Forms\Container;
use Nette\Forms\Controls\SubmitButton;
use Nette\Security\User;

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
					->setPrompt("Zoznam dodávateľov");

				$item->addText("price", "Predajná cena lieku")
					->setDisabled();

				$item->addText("in_stock", "Na skade")
					->setDisabled();

				$item->addText("count", "Počet");

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

	/**
	 * Operacia, ktora sa zavola po stlaceni tlacidla na vytvorenie objednacky.
	 * Nasledne sa ulozi objednavka do db.
	 * @param SubmitButton $button
	 * @throws \Exception
	 */
	public function createOrderSubmitted(SubmitButton $button)
	{
		try {
			$user = $this->userFacade->getUser($this->user->id);
			$this->orderFacade->createOrder($button->getForm()->getValues(true), $user);

			$button->getForm()->onSuccess[] = function (Form $form) {
				$tmp = $form->getPresenter();
				$tmp->flashMessage("Objednávka bola úspešne vytvorená.");
				$tmp->redirect("this");
			};
		}
		catch (UniqueConstraintViolationException $ex) {
			$button->getForm()->addError("Objednávka už existuje.");
		}
		catch (\InvalidArgumentException $ex) {
			$button->getForm()->addError($ex->getMessage());
		}
	}
}
