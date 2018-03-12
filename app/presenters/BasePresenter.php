<?php

namespace App\Presenters;

use App\Model\Entities\User as UserEntity;
use App\Model\Facades\UserFacade;
use Nette\Application\UI\Presenter;

/**
 * Base presenter for all application presenters.
 */
abstract class BasePresenter extends Presenter
{
	/**
	 * @var UserFacade Fasada pre pracu s uzivatelmi.
	 * @inject
	 */
	public $userFacade;

	/** @var UserEntity Entita pre aktualneho uzivatela. */
	protected $userEntity;

	/**
	 * Metoda volana pred kazdou akciou, nastavi entitu uzivate,
	 * nasledne je ju mozne pouzit v sablone.
	 */
	public function startup()
	{
		parent::startup();

		if (!$this->getUser()->isAllowed($this->getName(), $this->getAction())) {
			$this->flashMessage('Daná sekcia alebo akcia je dostupná len po prihlásení.
				Ak ste prihlásený požiadajte administrátora o pridelenie
				oprávnení pre túto sekciu.');

			$this->redirect('Homepage:default');
		}

		if ($this->getUser()->isLoggedIn()) {
			$this->userEntity = $this->userFacade->getUser($this->getUser()->getId());
		}
		else {
			// ak nie je uzivatel prihalseny vytvorime prazdnu entitu
			$entity = new UserEntity();
			$this->userEntity = $entity;
		}
	}

	/**
	 * Metoda volana pred vykreslenim kazdeho presenteru a do template sa
	 * predavaju spolocne pre cely layot webu.
	 */
	public function beforeRender()
	{
		parent::beforeRender();
		$this->template->userEntity = $this->userEntity;
	}

	/**
	 * Odhlasenie uzivatela a znamazanie jeho identity.
	 * @throws \Nette\Application\AbortException
	 */
	public function handleLogout()
	{
		$this->getUser()->logout(TRUE);
		$this->flashMessage("Užívateľ bol úspešne odhlásený.");
		$this->redirect("Homepage:default");
	}
}
