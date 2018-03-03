<?php

namespace App\Forms;

use Nette\Application\UI\Form;

/**
 * Pomocne metody pre formulare.
 * @package App\Forms
 */
class UtilForm
{
	/**
	 * Nastavenie stylu formulara, aby zodpovedal bootstrap definiciu formulara.
	 * @param Form $form Formular, ktoremu sa ma nastavit bootstrap style.
	 * @return Form Upraveny formular s bootstrap renderovacimi prvkami.
	 */
	public static function toBootstrapForm($form)
	{
		$form->elementPrototype->addAttributes(array('novalidate' => 'novalidate'));
		$form->elementPrototype->addAttributes(array('class' => 'form-horizontal'));

		$renderer = $form->getRenderer();
		$renderer->wrappers['controls']['container'] = '';
		$renderer->wrappers['pair']['container'] = "div class='form-group'";
		$renderer->wrappers['label']['container'] = "label class='control-label col-xs-4'";
		$renderer->wrappers['control']['container'] = "div class='col-xs-6'";
		$renderer->wrappers['control']['.text'] = 'form-control';
		$renderer->wrappers['control']['.password'] = 'form-control';
		$renderer->wrappers['control']['.email'] = 'form-control';
		$renderer->wrappers['control']['.number'] = 'form-control';
		$renderer->wrappers['control']['.submit'] = 'btn';

		return $form;
	}
}
