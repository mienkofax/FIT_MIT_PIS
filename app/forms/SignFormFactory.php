<?php

namespace App\Forms;

use Nette;
use Nette\Application\UI\Form;
use Nette\Security\AuthenticationException;
use Nette\Utils\ArrayHash;

/**
 * Tovaren pre vytvorenie formulara na prihlasenie a registrovanie.
 * @package App\Forms
 */
class SignFormFactory extends BaseFormFactory
{
	use Nette\SmartObject;

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

		$form->addSubmit("signIn", "Prihlásiť")
			->setAttribute("class", "btn-primary");

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
			$form->addError($ex->getMessage());
		}
	}

}
