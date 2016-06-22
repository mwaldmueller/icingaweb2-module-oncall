<?php
/* Icinga Web 2 | (c) 2016 Icinga Development Team | GPLv2+ */

namespace Icinga\Module\Oncall\Forms\Config;

use Icinga\Forms\ConfigForm;

/**
 * Form for managing the OnCall username
 */
class OnCallUserForm extends ConfigForm
{
    /**
     * {@inheritdoc}
     */
    public function init()
    {
        $this->setSubmitLabel($this->translate('Save'));
    }

    /**
     * {@inheritdoc}
     */
    public function createElements(array $formData)
    {
        $this->addElement(
            'text',
            'oncall_username',
            array(
                'description'   => $this->translate('The OnCall username'),
                'label'         => $this->translate('OnCall username'),
                'required'      => true,
                'value'         => 'icingaadmin'
            )
        );

        $this->addElement(
            'text',
            'oncall_pattern',
            array(
                'description'   => $this->translate('The contact search pattern'),
                'label'         => $this->translate('Search pattern'),
                'required'      => true,
                'value'         => 'user*'
            )
        );
    }
}
