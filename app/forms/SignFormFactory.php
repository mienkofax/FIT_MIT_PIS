<?php

namespace App\Forms;

use App\Model\Facades\UserFacade;
use Nette\Database\UniqueConstraintViolationException;
use Nette;
use Nette\Application\UI\Form;
use Nette\Security\AuthenticationException;
use Nette\Security\User;
use Nette\Utils\ArrayHash;

/**
 * Tovaren pre vytvorenie formulara na prihlasenie a registrovanie.
 * @package App\Forms
 */
class SignFormFactory
{
	use Nette\SmartObject;

	/** @var UserFacade Fasada pre pracu s uzivatelmi. */
	private $userFacade;

	/** @var User Informacie o uzivatelo (jedna sa o Nette triedu). */
	private $user;

	public function __construct(UserFacade $userFacade, User $user)
	{
		$this->userFacade = $userFacade;
		$this->user = $user;
	}

	/**
	 * Vytvorenie formulara pre registrovanie uzivatela.
	 * @return Form registracny formular
	 */
	public function createSignUp()
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

		$form->addPassword("password", "Heslo")
			->setRequired("Musí byť zadané heslo.")
			->addRule(Form::MIN_LENGTH, "Položka %label musí obsahovať min. %d znakov.", 5);

		$form->addPassword("password_repeat", "Heslo znova")
			->setRequired("Musí byť zadané heslo.")
			->addRule(Form::EQUAL, "Heslá sa nezhodujú", $form["password"])
			->addRule(Form::MIN_LENGTH, "Položka %label musí obsahovať min. %d znakov.", 5);

		$form->addSubmit("signUp", "Registrovať");
		$form->onSuccess[] = array($this, "signUpSubmitted");

		return UtilForm::toBootstrapForm($form);
	}

	/**
	 * Funkcia sa vykona po uspesnom odoslani formularu pre registrovanie a
	 * pokusi sa registrovat uzivatela.
	 * @param Form $form Formular pre registrovanie.
	 * @param ArrayHash $values Hodnoty, ktore boli sa vyplnili vo formulari.
	 * @throws \Exception
	 */
	public function signUpSubmitted(Form $form, ArrayHash $values)
	{
		try {
			$this->userFacade->registerUser($values);
		}
		catch (UniqueConstraintViolationException $ex) {
			$form->addError($ex->getMessage());
		}
	}

	/**
	 * Vytvorenie formulara pre prihlasenie uzivatela.
	 * @return Form Formular pre prihlasenie.
	 */
	public function createSignIn()
	{
		$form = new Form;
		$form->addText("email", "E-mail")
			->setRequired("Musí byť zadaný email.")
			->setAttribute("placeholder", "user@example.com");

		$form->addPassword("password", "Heslo")
			->setRequired("Musí byť zadané heslo.")
			->setAttribute("placeholder", "*******");

		$form->addCheckbox("remember", "Zapamätať si prihlásenie")
			->setAttribute("class", "checkbox login-remember");

		$form->addSubmit("signIn", "Prihlásiť");

		$form->onSuccess[] = array($this, "signInSubmitted");

		return UtilForm::toBootstrapForm($form);
	}

	/**
	 * Funkcia sa vykona po uspesnom odoslani formularu pre prihlasenie a
	 * pokus sa prihlasit uzivatela.
	 * @param Form $form Formular pre prihlasenie.
	 * @param ArrayHash $values Hodnoty, ktore boli sa vyplnili vo formulari.
	 */
	public function signInSubmitted($form, $values)
	{
		try {
			$this->user->login($values->email, $values->password);
			if ($values->remember)
				$this->user->setExpiration("14 days", FALSE);
			else
				$this->user->setExpiration("20 minutes", TRUE);
		}
		catch (AuthenticationException $ex) {
			$form->addError("Užívateľské meno a heslo sa nezhodujú.");
		}
	}

}
