<?php

use Icinga\Module\Monitoring\Controller;
use Icinga\Module\Oncall\Forms\OncallForm;

class Oncall_IndexController extends Controller
{
    public function indexAction()
    {
        $this->getTabs()->activate('oncall');

	$form = new OncallForm();
	$this->view->form = $form;
        $form->setTitle($this->translate('OnCall'));
        $form->setRedirectUrl('oncall');
        $form->handleRequest();
    }

    public function getTabs()
    {
        $tabs = parent::getTabs();
        $tabs->add(
            'oncall',
            array(
                'title' => $this->translate('OnCall'),
                'url'   => 'oncall',
                'tip'  => $this->translate('Overview')
            )
        );

        return $tabs;
    }
}
