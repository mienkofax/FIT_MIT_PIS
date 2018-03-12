<?php

namespace App\Presenters;

use App\Forms\SupplierFormFactory;
use App\Forms\UserFormFactory;
use App\Model\Facades\SupplierFacade;
use Nette\Application\UI\Form;

/**
 * Presenter pre pracu s uzivatelmi.
 * @package App\Presenters
 */
class UserPresenter extends BasePresenter
{
	/** @var UserFormFactory */
	private $formFactory;

	private $searchedUser;

	/**
	 * Konstruktor s injektovanymi triedami pre pracu s uzivatelmi.
	 * @param UserFormFactory $userFormFactory
	 */
	public function __construct(
		UserFormFactory $userFormFactory
	)
	{
		parent::__construct();
		$this->formFactory = $userFormFactory;
	}

	public function renderManage($column, $sort)
	{
		$this->template->users =
			$this->userFacade->getAllasArray($column, $sort);
	}

	public function renderProfil($id)
	{
		$this->template->userData =
			$this->userFacade->getUser($id);
	}

	/**
	 * Vytvorenie komponenty a vratenie komponenty pre pridanie
	 * dodavatela.
	 * @return Form
	 */
	public function createComponentCreateUserForm()
	{
		$form = $this->formFactory->createUserForm();
		$form->onSuccess[] = function (Form $form) {
			$tmp = $form->getPresenter();
			$tmp->flashMessage("Užívateľ bol úspešne vytvorený.");
			$tmp->redirect("User:manage");
		};

		return $form;
	}

	public function createComponentEditUserForm()
	{
		$form = $this->formFactory->editUserForm();
		$form->onSuccess[] = function (Form $form) {
			$tmp = $form->getPresenter();
			$tmp->flashMessage("Užívateľ bol úspešne upravený.");
			$tmp->redirect("User:manage");
		};

		return $form;
	}

	public function createComponentChangeUserPasswordForm()
	{
		$form = $this->formFactory->changeUserPasswordForm();
		$form->onSuccess[] = function (Form $form) {
			$tmp = $form->getPresenter();
			$tmp->flashMessage("Heslo bolo úspešne zmenené.");
			$tmp->redirect("Homepage:default");
		};

		return $form;
	}

	public function actionEdit($id = NULL)
	{
		$this->searchedUser = $tmp = $this->userFacade->getUser($id);
		if (is_null($tmp))
			return;

		$this["editUserForm"]->setDefaults(
			array(
				"userId" => $id,
				"name" => $tmp->name,
				"surname" => $tmp->surname,
				"email" => $tmp->email,
				"role" => $tmp->role
			)
		);
	}

	public function actionChangePassword($id = NULL)
	{
		$this->searchedUser = $tmp = $this->userFacade->getUser($id);
		if (is_null($tmp))
			return;

		$this["changeUserPasswordForm"]->setDefaults(
			array(
				"userId" => $id
			)
		);
	}
}
