<?php
/* Icinga Web 2 | (c) 2016 Icinga Development Team | GPLv2+ */

namespace Icinga\Module\Oncall\Controllers;

use Icinga\Module\Monitoring\Backend\MonitoringBackend;
use Icinga\Module\Monitoring\Controller;
use Icinga\Module\Oncall\Forms\OncallForm;

class IndexController extends Controller
{
    public function indexAction()
    {
        $this->getTabs()->activate('oncall');

        $form = new OncallForm(array(
            'backend'               => MonitoringBackend::instance(),
            'class'                 => 'oncall-form',
            'contactSearchPattern'  => $this->Config()->get('oncall', 'pattern', 'user*'),
            'moduleConfig'          => $this->Config(),
            'onCallUsername'        => $this->Config()->get('oncall', 'username', 'icingaadmin')
        ));

        $this->view->form = $form;
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
