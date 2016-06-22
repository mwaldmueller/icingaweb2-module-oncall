<?php

$section = $this->menuSection('OnCall', array(
    'icon' => 'users',
    'url' => 'oncall',
));

$this->provideConfigTab('oncall', array(
    'label' => $this->translate('Configuration'),
    'title' => $this->translate('Configure the OnCall username'),
    'url'   => 'config'
));
