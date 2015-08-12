<?php

namespace Icinga\Module\Oncall\Forms;

use Exception;
use Icinga\Module\Monitoring\Backend\MonitoringBackend;
use Icinga\Module\Monitoring\Command\Transport\CommandTransport;
use Icinga\Web\Form;
use Icinga\Web\Notification;

class OncallForm extends Form
{
    const ONCALL_USER = 'icingaadmin';

    protected $contacts;

    protected $onCallUser;

    protected $actualOnCallUser;

    public function init()
    {
        $this->setName('form_oncall');
    	$this->setSubmitLabel($this->translate('Save Changes'));
    
    	$this->onCallUser = MonitoringBackend::instance()->select()->from('contact', array(
        	'contact_name',
        	'contact_alias',
        	'contact_pager'
    	))->where('contact_name', static::ONCALL_USER)->fetchRow();

    	$contacts = array();

    	$query = MonitoringBackend::instance()->select()->from('contact', array(
        	'contact_name',
        	'contact_alias',
        	'contact_pager'
    	));

    	$query->where('contact_name', 'test*');

        foreach($query as $contact) {
        	$contacts[$contact->contact_name] = $contact;
        	if ($this->onCallUser !== null
        	    && $contact->contact_pager === $this->onCallUser->contact_pager
        	) {
        	    $this->actualOnCallUser = $contact;
        	}
    	}
    	$this->contacts = $contacts;
    }

    public function createElements(array $formData)
    {
    	$this->addElement(
        	'radio',
        	'oncall',
        	array (
            		'multiOptions'	=> array_combine(array_keys($this->contacts), array_keys($this->contacts)),
            		'value'		=> $this->actualOnCallUser !== null ? $this->actualOnCallUser->contact_name : null
        	)
    	);
    }

    public function onSuccess()
    {
    	$contactName = $this->getElement('oncall')->getValue();
    	$contact = $this->contacts[$contactName];
    	$pathToCmdFile = CommandTransport::first()->getPath();

    	$command1 = sprintf('[%u] CHANGE_CUSTOM_USER_VAR;%s;pager;%s', time(), static::ONCALL_USER, $contact->contact_pager) . "\n";
    	//$command2=
    
	$fp = fopen($pathToCmdFile, 'w');

	fwrite($fp, $command1);
	//fwrite($fp, $command2);

	fflush($fp);
	fclose($fp);

	Notification::success('Change OnCall user...');
    }
}
