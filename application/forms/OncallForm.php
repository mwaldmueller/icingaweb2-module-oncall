<?php

namespace Icinga\Module\Oncall\Forms;

use Exception;
use Icinga\Application\Config;
use Icinga\Exception\ConfigurationError;
use Icinga\Module\Director\Cli\Command;
use Icinga\Module\Monitoring\Backend\MonitoringBackend;
use Icinga\Module\Monitoring\Command\Transport\CommandTransport;
use Icinga\Module\Monitoring\Command\Transport\LocalCommandFile;
use Icinga\Util\File;
use Icinga\Web\Form;
use Icinga\Web\Notification;

class OncallForm extends Form
{
    /**
     * The monitoring backend
     *
     * @var \Icinga\Module\Monitoring\Backend\MonitoringBackend
     */
    protected $backend;

    /**
     * The OnCall module configuration
     *
     * @var Config
     */
    protected $config;

    protected $contacts;

    protected $contactSearchPattern;

    protected $onCallUsername;

    protected $actualOnCallUser;

    protected $onCallUser;

    public function init()
    {
        $this->setSubmitLabel($this->translate('Save Changes'));

        $this->onCallUser = $this->getBackend()->select()->from('contact', array(
            'contact_name',
            'contact_alias',
            'contact_pager'
        ))->where('contact_name', $this->getOnCallUsername())->fetchRow();

        if ($this->onCallUser === false) {
            throw new ConfigurationError('No OnCall user w/ name %s found', $this->getOnCallUsername());
        }

        $contacts = array();

        $query = $this->getBackend()->select()->from('contact', array(
            'contact_name',
            'contact_alias',
            'contact_pager'
        ));
        $query->where('contact_name', $this->getContactSearchPattern());

        foreach ($query as $contact) {
            $contacts[$contact->contact_name] = $contact;
        }
        $this->contacts = $contacts;
    }

    public function createElements(array $formData)
    {
        $this->addElement(
            'radio',
            'oncall',
            array (
                'multiOptions'  => array_combine(
                    array_keys($this->contacts), array_keys($this->contacts)
                ),
                'value'         => $this->getModuleConfig()->get('oncall', 'active_username')
            )
        );
    }

    /**
     * Update pager number of the OnCall user
     *
     * @throws ConfigurationError   If no local command transport configured
     */
    public function onSuccess()
    {
        $commandFilePath = null;
        foreach (CommandTransport::getConfig() as $name => $config) {
            if ($config->transport === LocalCommandFile::TRANSPORT || empty($config->transport)) {
                $commandFilePath = $config->path;
                break;
            }
        }
        if ($commandFilePath === null) {
            throw new ConfigurationError('No local command transport found');
        }

        $contactName = $this->getElement('oncall')->getValue();
        $contact = $this->contacts[$contactName];

        $commandString1 = sprintf(
                '[%u] CHANGE_CUSTOM_USER_VAR;%s;pager;%s',
                time(),
                $this->getOnCallUsername(),
                $contact->contact_pager
        );

        $commandFile = new File($commandFilePath, 'wn');
        $commandFile->fwrite($commandString1 . "\n");

        $config = $this->getModuleConfig();
        $configSection = $config->getSection('oncall');
        $configSection->active_username = $contactName;
        $config->saveIni();

        Notification::success('OnCall user changed');
    }

    /**
     * @return mixed
     */
    public function getBackend()
    {
        return $this->backend;
    }

    /**
     * @param mixed $backend
     * @return OncallForm
     */
    public function setBackend($backend)
    {
        $this->backend = $backend;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getContactSearchPattern()
    {
        return $this->contactSearchPattern;
    }

    /**
     * @param mixed $contactSearchPattern
     */
    public function setContactSearchPattern($contactSearchPattern)
    {
        $this->contactSearchPattern = $contactSearchPattern;
    }

    /**
     * Get the OnCall module configuration
     *
     * @return  Config
     */
    public function getModuleConfig()
    {
        return $this->config;
    }

    /**
     * Set the OnCall module configuration
     *
     * @param   Config  $config
     *
     * @return  $this
     */
    public function setModuleConfig(Config $config)
    {
        $this->config = $config;
        return $this;
    }

    /**
     * Get the OnCall username
     *
     * @return string
     */
    public function getOnCallUsername()
    {
        return $this->onCallUsername;
    }

    /**
     * Set the OnCall username
     *
     * @param   string  $onCallUsername
     *
     * @return  $this
     */
    public function setOnCallUsername($onCallUsername)
    {
        $this->onCallUsername = $onCallUsername;
        return $this;
    }


}
