<?php

namespace App\Forms;

use App\Model\Entities\User;
use Doctrine\DBAL\Exception\UniqueConstraintViolationException;
use Nette\Application\UI\Form;

/**
 * Tovaren pre vygenerovanie formulara na pracu s uzivatelmi.
 * @package App\Forms
 */
class UserFormFactory extends BaseFormFactory
{
	/** Uzivatelske role. */
	const USER_ROLES = array(
		User::ROLE_SELLER => "Predavač",
		User::ROLE_MANAGER => "Manažér"
	);

	private function createForm()
	{
		$form = new Form;
		$form->addText("name", "Meno")
			->setRequired("Musí byť zadané meno.");

		$form->addText("surname", "Priezvisko")
			->setRequired("Musí byť zadané priezvisko.");

		$form->addText("email", "E-mail")
			->setRequired("Musí byť zadaný email.")
			->addRule(Form::EMAIL, "Nevalidný email.")
			->setAttribute("placeholder", "user@example.com");

		$form->addSelect("role", "Opávnenia užívateľa")
			->setItems(self::USER_ROLES)
			->setPrompt("Užívateľské oprávnenia")
			->setAttribute("class","form-control")
			->setRequired();

		return $form;
	}

	public function createUserForm()
	{
		$form = $this->createForm();

		$form->addPassword("password", "Heslo")
			->setRequired("Musí byť zadané heslo.")
			->addRule(Form::MIN_LENGTH, "Položka %label musí obsahovať min. %d znakov.", 5);

		$form->addPassword("password_repeat", "Heslo znova")
			->setRequired("Musí byť zadané heslo.")
			->addRule(Form::EQUAL, "Heslá sa nezhodujú", $form["password"])
			->addRule(Form::MIN_LENGTH, "Položka %label musí obsahovať min. %d znakov.", 5);

		$form->addSubmit("signUp", "Vytvoriť užívateľa")
			->setAttribute("class", "btn-primary");

		$form->onSuccess[] = array($this, "newUserSubmitted");

		return UtilForm::toBootstrapForm($form);
	}

	public function editUserForm()
	{
		$form = $this->createForm();
		$form->addHidden("userId");

		$form->addSubmit("signUp", "Editovať užívateľa")
			->setAttribute("class", "btn-primary");

		$form->onSuccess[] = array($this, "editUserSubmitted");

		return UtilForm::toBootstrapForm($form);
	}

	public function changeUserPasswordForm()
	{
		$form = new Form;
		$form->addPassword("password", "Heslo")
			->setRequired("Musí byť zadané heslo.")
			->addRule(Form::MIN_LENGTH, "Položka %label musí obsahovať min. %d znakov.", 5);

		$form->addPassword("password_repeat", "Heslo znova")
			->setRequired("Musí byť zadané heslo.")
			->addRule(Form::EQUAL, "Heslá sa nezhodujú", $form["password"])
			->addRule(Form::MIN_LENGTH, "Položka %label musí obsahovať min. %d znakov.", 5);

		$form->addSubmit("signUp", "Zmeniť heslo")
			->setAttribute("class", "btn-primary");

		$form->addHidden("userId");

		$form->onSuccess[] = array($this, "changeUserPasswordSubmitted");

		return UtilForm::toBootstrapForm($form);
	}

	public function deactivationUserForm()
	{
		$form = new Form;

		$form->addSelect("medicine_id", "Užívateľ")
			->setItems($this->userFacade->getIdsAndName())
			->setPrompt("Zoznam užívateľov")
			->setAttribute("class", "form-control")
			->setRequired();

		$form->addSelect("deactivation", "Deaktivácia")
			->setItems(array(0 => "Nie", 1 => "Áno"))
			->setAttribute("class", "form-control")
			->setRequired();

		$form->addSubmit("signUp", "Zmeniť")
			->setAttribute("class", "btn-primary");

		$form->onSuccess[] = array($this, "deactivationUserSubmitted");

		return UtilForm::toBootstrapForm($form);
	}

	public function newUserSubmitted($form, $values)
	{
		try {
			$this->userFacade->registerUser($values);
		}
		catch (UniqueConstraintViolationException $ex) {
			$form->addError("Zadaný email je už používaný.");
		}
	}

	public function editUserSubmitted($form, $values)
	{
		$user = $this->userFacade->getUser($values->userId);
		if (is_null($user))
			throw new \InvalidArgumentException("Užívateľ neexistuje.");

		try {
			$this->userFacade->editUser($values, $user);
		}
		catch (UniqueConstraintViolationException $ex) {
			$form->addError($ex->getMessage());
		}
	}

	public function changeUserPasswordSubmitted($form, $values)
	{
		$user = $this->userFacade->getUser($values->userId);
		if (is_null($user))
			throw new \InvalidArgumentException("Užívateľ neexistuje.");

		$this->userFacade->changeUserPassword($values, $user);
	}

	public function deactivationUserSubmitted($form, $values)
	{
		$user = $this->userFacade->getUser($values->medicine_id);
		if (is_null($user))
			throw new \InvalidArgumentException("Užívateľ neexistuje.");

		$this->userFacade->changeDeactivation($values, $user);
	}
}
