<?php
/* Icinga Web 2 | (c) 2016 Icinga Development Team | GPLv2+ */

namespace Icinga\Module\Oncall\Controllers;

use Icinga\Module\Oncall\Forms\Config\OnCallUserForm;
use Icinga\Web\Controller;
use Icinga\Web\Url;

/**
 * Manage OnCall module configuration
 */
class ConfigController extends Controller
{
    public function indexAction()
    {
        $this->getTabs()->add('oncall', array(
            'active'    => true,
            'label'     => $this->translate('OnCall Username'),
            'url'       => Url::fromRequest()
        ));

        $form = new OnCallUserForm();
        $form
            ->setIniConfig($this->Config())
            ->handleRequest();

        $this->view->form = $form;
    }
}
