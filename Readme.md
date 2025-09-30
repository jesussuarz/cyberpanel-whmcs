# CyberPanel WHMCS Module

This WHMCS module allows you to integrate CyberPanel with WHMCS to sell and automate hosting services. Using the module, you can provision CyberPanel accounts, manage customer websites, and give clients convenient access to their control panel.

The distribution consists of two parts:

1. Server Module (modules/servers/cyberpanel/) → Handles account provisioning and automation.
2. Addon Module (modules/addons/cyberpanel_extra/) → Creates and manages the extra database table for storing SSH credentials (user, password, port).

### Requirements
- A Dedicated Server Product in WHMCS must be created and configured to use the CyberPanel module.
- You must also configure a Server in WHMCS (System Settings → Servers) and assign it to the product.
- Important: This module does not install or configure CyberPanel on the server automatically. CyberPanel must already be installed on the dedicated server.
- The admin user in CyberPanel must have API Access enabled:

### Enable API Access in CyberPanel
1. Log in to CyberPanel.
2. Go to Users → API Access.
3. Select the user (e.g. admin).
4. Select Enable and click Save.
5. Or go directly to:
```
https://<your-ip-or-domain>:8090/users/apiAccess
```

### Installation
### Step 1: Server Module

Follow the standard WHMCS server module installation steps.

**Option 1 (Recommended)**
1. SSH into your WHMCS server.
2. Navigate to the WHMCS server modules directory:
```bash
cd path_to_whmcs/modules/servers
```

3. Clone the repository into the **cyberpanel** folder:
```bash
git clone https://github.com/jetchirag/cyberpanel-whmcs.git cyberpanel
```

**Option 2**
1. Download the repository as a zip file.
2. Navigate to the WHMCS server modules directory:
```bash
cd path_to_whmcs/modules/servers
```
3. Create a folder named cyberpanel, then upload and extract the files there.


### Step 2: Addon Module

The addon module is required to create the database table mod_cyberpanel_extra, which stores SSH access details per service.

1. Copy the addon folder into the WHMCS addons directory:
```bash
path_to_whmcs/modules/addons/cyberpanel_extra
```
2. Log in to the WHMCS admin area.
3. Go to System Settings → Addon Modules.
4. Activate CyberPanel Extra.
- On activation, it will create/update the table mod_cyberpanel_extra.

### Module Functions

The CyberPanel WHMCS module provides the following functionality:
- Provisioning:
  - Create new website or user account
  - Terminate website
  - Suspend or unsuspend website
  - Change hosting package
  - Change user password
- Convenience Features:
  - Auto-login to CyberPanel (Admin or Customer)
  - Store and display SSH credentials (host, port, username, password) to clients inside WHMCS
 
### Directory Structure:
```
modules
├── addons
│   └── cyberpanel_extra
│       └── cyberpanel_extra.php
└── servers
    └── cyberpanel
        ├── LICENSE
        ├── Readme.md
        ├── api.php
        └── cyberpanel.php
```

## Common Errors
### Error:
```
 Data supplied is not accepted, following characters are not allowed in the input ` $ & ( ) [ ] { } ; : ‘ < >.
```
**Solution**: Remove those special characters from the service’s password.

### Error:
```
API Access Disabled.
```
**Solution**: Enable API Access for the user in CyberPanel (see instructions above).
 
### Error:
```
Unknown Error Occurred
```
**Solution**: Usually indicates WHMCS is unable to reach the CyberPanel API. Verify that the hostname/IP address is correct and port 8090 is open in the firewall.
