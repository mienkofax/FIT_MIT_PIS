<?php

namespace App\Presenters;

use App\Forms\SignFormFactory;
use Nette\Application\UI\Form;

/**
 * Presenter pre registrovanie a prihlasovanie uzivatelov.
 * @package App\Presenters
 */
class SignPresenter extends BasePresenter
{
	/**
	 * @var SignFormFactory Tovaren pre vytvorenie formularu pre
	 * registrovanie a prihlasenie.
	 * @inject
	 */
	public $formFactory;

	/**
	 * Vytvorenie a vratenie prihlasovacieho formulara.
	 * @return Form komponenta sprihlasovacim formularom
	 */
	protected function createComponentSignInForm()
	{
		$form = $this->formFactory->createSignIn();
		$form->onSuccess[] = function (Form $form) {
			$tmp = $form ->getPresenter();
			$tmp->flashMessage("Užívateľ bol úspešene prihlásený.");
			$tmp->redirect("this");
		};

		return $form;
	}

	/**
	 * Presmerovanie uzivatela na domovsku stranku po prihlaseni.
	 * @throws \Nette\Application\AbortException
	 */
	public function actionIn()
	{
		if ($this->getUser()->isLoggedIn())
			$this->redirect("Homepage:default");
	}
}