# OnCall - Icinga Web 2 module

Icinga Web 2 Module that changes the pager number of an OnCall user for emergency services.

## Installation

Like with any other Icinga Web 2 module just drop `oncall` into the modules directory and enable
the module in your web frontend or via Web 2's CLI.

This module has no dependencies.

## Configuration

After you've enabled `oncall` you reach its configuration in Icinga Web 2 via the module's configuration tab.
But you may also change its configuration manually.
`oncall` maintains a configuration file which is normally located at:

```
/etc/icingaweb2/modules/oncall/config.ini
```

The following sample should perfectly explain all the available settings:

```
[oncall]
username = "icingaadmin"
pattern = "user-*"
```
