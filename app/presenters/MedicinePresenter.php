<?php

namespace App\Presenters;

use App\Forms\MedicineFormFactory;
use App\Model\Facades\MedicineFacade;
use Nette\Application\UI\Form;

/**
 * Presenter pre pracu s liekmi.
 * @package App\Presenters
 */
class MedicinePresenter extends BasePresenter
{
	/** @var MedicineFacade Fasada pre pracu s liekmi. */
	private $medicineFacade;

	/**
	 * @var MedicineFormFactory Tovaren pre tvorbu formularov pre
	 * pridanie liekov.
	 */
	private $formFactory;

	private $searchedMedicine;

	/**
	 * Konstruktor s injektovanymi triedami pre pracu s liekmi.
	 * @param MedicineFacade $medicineFacade
	 * @param MedicineFormFactory $formFactory
	 */
	public function __construct(
		MedicineFacade $medicineFacade,
		MedicineFormFactory $formFactory
		)
	{
		parent::__construct();
		$this->medicineFacade = $medicineFacade;
		$this->formFactory = $formFactory;
	}

	/**
	 * Vytvorenie komponenty a vratenie komponenty pre pridanie lieku.
	 * @return Form
	 */
	public function createComponentCreateMedicineForm()
	{
		$form = $this->formFactory->createCreateMedicine();
		$form->onSuccess[] = function (Form $form) {
			$tmp = $form->getPresenter();
			$tmp->flashMessage("Liek bol úspešne vytvorený.");
			$tmp->redirect("this");
		};

		return $form;
	}

	public function createComponentEditMedicineForm()
	{
		$form = $this->formFactory->createEditMedicineForm();
		$form->onSuccess[] = function (Form $form) {
			$tmp = $form->getPresenter();
			$tmp->flashMessage("Liek bol úspešne upravený.");
			$tmp->redirect("this");
		};

		return $form;
	}

    /**
     * Nastavenie premennej do sablony.
     * @param $column string
     * @param $sort string
     */
    public function renderManage($column, $sort)
    {
        $this->template->medicines =
            $this->medicineFacade->getAllAsArray($column, $sort);
    }

	public function renderEdit()
	{
		$this->template->medicine = $this->searchedMedicine;
	}

	public function actionEdit($id = NULL)
	{
		$this->searchedMedicine = $tmp = $this->medicineFacade->getMedicine($id);
		if (is_null($tmp))
			return;

		$this["editMedicineForm"]->setDefaults(
			array(
				"id" => $id,
				"name" => $tmp->name,
				"description" => $tmp->description,
				"type" => (int) $tmp->type
			)
		);
	}

	public function handleRemoveMedicine($id)
	{
		try {
			$this->medicineFacade->deleteMedicine($id);
			$this->flashMessage("Liek bol odstránený.");
		}
		catch (\InvalidArgumentException $ex) {
			$this->flashMessage($ex->getMessage());
		}
		catch (\Doctrine\DBAL\Exception\ForeignKeyConstraintViolationException $ex) {
			$this->flashMessage("Liek obsahuje skladové zásoby,
			pred jeho odstránením zmažte skladové zásoby.", "danger");
		}

		$this->redirect("Medicine:manage");
	}
}
