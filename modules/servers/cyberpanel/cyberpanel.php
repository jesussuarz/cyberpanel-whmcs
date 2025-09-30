<?php
/**
 *
 * CyberPanel whmcs module
 * @jetchirag
 * Adjusted by Jesus Suarez 
 */

if (!defined("WHMCS")) {
    die("This file cannot be accessed directly");
}

// Include API Class
include("api.php");

function cyberpanel_MetaData()
{
    return array(
        'DisplayName' => 'CyberPanel',
        'APIVersion' => '2.0',
        'RequiresServer' => true,
        'DefaultNonSSLPort' => '8090',
        'DefaultSSLPort' => '8090',
        'ServiceSingleSignOnLabel' => 'Login as User',
        'AdminSingleSignOnLabel' => 'Login as Admin',
    );
}

function cyberpanel_ConfigOptions()
{
    return array(
        'Package Name' => array(
            'Type' => 'text',
            'Default' => 'Default',
            'Description' => 'Enter package name for this product',
        ),
        'ACL' => array(
            'Type' => 'text',
            'Default' => 'user',
            'Description' => 'ACL to be assigned to the new user',
        )
    );
}


function cyberpanel_CreateAccount(array $params)
{
    try {
        
        $api = new CyberApi();
        $response = $api->create_new_account($params);
        
        logModuleCall(
            'cyberpanel',
            __FUNCTION__,
            $params,
            $response
        );

        // Checking for errors
        if (!$response["createWebSiteStatus"]){
        	return $response["error_message"];
        }
    } catch (Exception $e) {
        // Record the error in WHMCS's module log.
        logModuleCall(
            'cyberpanel',
            __FUNCTION__,
            $params,
            $e->getMessage(),
            $e->getTraceAsString()
        );

        return $e->getMessage();
    }

    return 'success';
}

function cyberpanel_SuspendAccount(array $params)
{
    try {
        
        $params['status'] = "Suspend";
        $api = new CyberApi();
        $response = $api->change_account_status($params);
        
        logModuleCall(
            'cyberpanel',
            __FUNCTION__,
            $params,
            $response
        );

        // Checking for errors
        if (!$response["websiteStatus"]){
        	return $response["error_message"];
        }
    } catch (Exception $e) {
        // Record the error in WHMCS's module log.
        logModuleCall(
            'cyberpanel',
            __FUNCTION__,
            $params,
            $e->getMessage(),
            $e->getTraceAsString()
        );

        return $e->getMessage();
    }

    return 'success';
}

function cyberpanel_UnsuspendAccount(array $params)
{
    try {
        $status = "Unsuspend";

        $api = new CyberApi();
        $response = $api->change_account_status($params, $status);
        
        logModuleCall(
            'cyberpanel',
            __FUNCTION__,
            $params,
            $response
        );

        // Checking for errors
        if (!$response["websiteStatus"]){
        	return $response["error_message"];
        }
    } catch (Exception $e) {
        // Record the error in WHMCS's module log.
        logModuleCall(
            'cyberpanel',
            __FUNCTION__,
            $params,
            $e->getMessage(),
            $e->getTraceAsString()
        );

        return $e->getMessage();
    }

    return 'success';
}
function cyberpanel_TerminateAccount(array $params)
{
    try {
        
        $api = new CyberApi();
        $response = $api->terminate_account($params);
        
        logModuleCall(
            'cyberpanel',
            __FUNCTION__,
            $params,
            $response
        );

        // Checking for errors
        if (!$response["websiteDeleteStatus"]){
        	return $response["error_message"];
        }        
    } catch (Exception $e) {
        // Record the error in WHMCS's module log.
        logModuleCall(
            'cyberpanel',
            __FUNCTION__,
            $params,
            $e->getMessage(),
            $e->getTraceAsString()
        );

        return $e->getMessage();
    }

    return 'success';
}

function cyberpanel_ChangePassword(array $params)
{
    try {

        $api = new CyberApi();
        $response = $api->change_account_password($params);
        
        logModuleCall(
            'cyberpanel',
            __FUNCTION__,
            $params,
            $response
        );

        // Checking for errors
        if (!$response["changeStatus"]){
        	return $response["error_message"];
        }        
    } catch (Exception $e) {
        // Record the error in WHMCS's module log.
        logModuleCall(
            'cyberpanel',
            __FUNCTION__,
            $params,
            $e->getMessage(),
            $e->getTraceAsString()
        );

        return $e->getMessage();
    }

    return 'success';
}


function cyberpanel_ChangePackage(array $params)
{
    try {

        $api = new CyberApi();
        $response = $api->change_account_package($params);
        
        logModuleCall(
            'cyberpanel',
            __FUNCTION__,
            $params,
            $response
        );

        // Checking for errors
        if (!$response["changePackage"]){
        	return $response["error_message"];
        }        
    } catch (Exception $e) {
        // Record the error in WHMCS's module log.
        logModuleCall(
            'cyberpanel',
            __FUNCTION__,
            $params,
            $e->getMessage(),
            $e->getTraceAsString()
        );

        return $e->getMessage();
    }

    return 'success';
}

function cyberpanel_TestConnection(array $params)
{
    try {

        $api = new CyberApi();
        $response = $api->verify_connection($params);
        
        logModuleCall(
            'cyberpanel',
            __FUNCTION__,
            $params,
            $response
        );

        // Checking for errors
        $errorMsg = '';
        if (!$response["verifyConn"]){
        	$errorMsg =  $response["error_message"];
        	$success = false;
        }
        else {
        	$success = true;
        	$errorMsg = '';
        }
    } catch (Exception $e) {
        // Record the error in WHMCS's module log.
        logModuleCall(
            'cyberpanel',
            __FUNCTION__,
            $params,
            $e->getMessage(),
            $e->getTraceAsString()
        );

        $success = false;
        $errorMsg = $e->getMessage();
    }

    return array(
        'success' => $success,
        'error' => $errorMsg,
    );
}

function cyberpanel_ClientArea($params) {
    $result = select_query("mod_cyberpanel_extra", "", ["serviceid" => $params['serviceid']]);
    $data   = mysql_fetch_array($result);

    $sshUser = $data['ssh_user'] ?? '';
    $sshPass = $data['ssh_pass'] ?? '';
    $sshPort = $data['ssh_port'] ?? '22';

    $panelURL = (($params["serversecure"]) ? "https" : "http") . "://{$params['serverhostname']}:{$params['serverport']}";
    $sshHost  = $params['serverip'] ?: $params['serverhostname'];

    $html = <<<HTML
<style>
.section {
    display: none !important;
}
.cyberpanel-box {
    border: 1px solid #ddd;
    border-radius: 8px;
    padding: 15px;
    margin: 15px 0;
    background: #fafafa;
    font-family: Arial, sans-serif;
}
.cyberpanel-box h3 {
    margin-top: 0;
    border-bottom: 1px solid #eee;
    padding-bottom: 5px;
}
.cyberpanel-row {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin: 6px 0;
}
.cyberpanel-row code {
    background: #f1f1f1;
    padding: 3px 6px;
    border-radius: 4px;
}
.cyberpanel-btn {
    padding: 4px 8px;
    background: #007bff;
    color: white;
    border: none;
    border-radius: 4px;
    cursor: pointer;
    margin-left: 5px;
}
.cyberpanel-btn:hover {
    background: #0056b3;
}
.hidden-pass {
    font-family: monospace;
    letter-spacing: 2px;
}
</style>

<div class="cyberpanel-box">
  <h3>CyberPanel Access</h3>
  <div class="cyberpanel-row">
    <span><strong>URL:</strong> <a href="{$panelURL}" target="_blank">{$panelURL}</a></span>
    <button class="cyberpanel-btn" onclick="copyToClipboard('{$panelURL}')">Copy</button>
  </div>
  <div class="cyberpanel-row">
    <span><strong>User:</strong> <code>{$params['username']}</code></span>
    <button class="cyberpanel-btn" onclick="copyToClipboard('{$params['username']}')">Copy</button>
  </div>
  <div class="cyberpanel-row">
    <span><strong>Password:</strong> <code id="panelPass" class="hidden-pass">••••••</code></span>
    <div>
      <button class="cyberpanel-btn" onclick="toggleVisibility('panelPass','{$params['password']}')">Show/Hide</button>
      <button class="cyberpanel-btn" onclick="copyToClipboard('{$params['password']}')">Copy</button>
    </div>
  </div>
  <form action="{$panelURL}/api/loginAPI" method="post" target="_blank">
    <input type="hidden" name="username" value="{$params['username']}">
    <input type="hidden" name="password" value="{$params['password']}">
    <input type="submit" class="cyberpanel-btn" value="Login to Control Panel">
  </form>
</div>

<div class="cyberpanel-box">
  <h3>SSH Access</h3>
  <div class="cyberpanel-row">
    <span><strong>Host:</strong> <code>{$sshHost}</code></span>
    <button class="cyberpanel-btn" onclick="copyToClipboard('{$sshHost}')">Copy</button>
  </div>
  <div class="cyberpanel-row">
    <span><strong>Port:</strong> <code>{$sshPort}</code></span>
    <button class="cyberpanel-btn" onclick="copyToClipboard('{$sshPort}')">Copy</button>
  </div>
  <div class="cyberpanel-row">
    <span><strong>User:</strong> <code>{$sshUser}</code></span>
    <button class="cyberpanel-btn" onclick="copyToClipboard('{$sshUser}')">Copy</button>
  </div>
  <div class="cyberpanel-row">
    <span><strong>Password:</strong> <code id="sshPass" class="hidden-pass">••••••</code></span>
    <div>
      <button class="cyberpanel-btn" onclick="toggleVisibility('sshPass','{$sshPass}')">Show/Hide</button>
      <button class="cyberpanel-btn" onclick="copyToClipboard('{$sshPass}')">Copy</button>
    </div>
  </div>
</div>

<script>
function copyToClipboard(text) {
  navigator.clipboard.writeText(text).then(function() {
    alert('Copied: ' + text);
  }, function(err) {
    alert('Error copying text');
  });
}

function toggleVisibility(elementId, realValue) {
  var el = document.getElementById(elementId);
  if (el.textContent === '••••••') {
    el.textContent = realValue;
  } else {
    el.textContent = '••••••';
  }
}
</script>
HTML;

    return $html;
}



function cyberpanel_AdminLink($params) {

    $loginform = '<form class="cyberpanel" action="' . (($params["serversecure"]) ? "https" : "http") . '://'.$params["serverhostname"].':'.$params['serverport'].'/api/loginAPI" method="post" target="_blank">
<input type="hidden" name="username" value="'.$params["serverusername"].'" />
<input type="hidden" name="password" value="'.$params["serverpassword"].'" />
<input type="submit" value="Login to Control Panel" />
</form>';
    return $loginform;

}

function cyberpanel_AdminServicesTabFields($params) {
    $result = select_query(
        "mod_cyberpanel_extra",
        "",
        ["serviceid" => $params['serviceid']]
    );
    $data = mysql_fetch_array($result);

    $sshUser = $data['ssh_user'] ?? '';
    $sshPass = $data['ssh_pass'] ?? '';
    $sshPort = $data['ssh_port'] ?? '22';

    return [
        'SSH Username' => '<input type="text" name="modulefields[ssh_user]" value="' . htmlspecialchars($sshUser) . '" />',
        'SSH Password' => '<input type="password" name="modulefields[ssh_pass]" value="' . htmlspecialchars($sshPass) . '" />',
        'SSH Port'     => '<input type="number" name="modulefields[ssh_port]" value="' . htmlspecialchars($sshPort) . '" min="1" max="65535" />',
    ];
}

function cyberpanel_AdminServicesTabFieldsSave($params) {
    $sshUser = $_POST['modulefields']['ssh_user'] ?? '';
    $sshPass = $_POST['modulefields']['ssh_pass'] ?? '';
    $sshPort = $_POST['modulefields']['ssh_port'] ?? '22';

    $result = select_query("mod_cyberpanel_extra", "id", ["serviceid" => $params['serviceid']]);
    $data = mysql_fetch_array($result);

    if ($data && $data['id']) {
        update_query("mod_cyberpanel_extra", [
            "ssh_user" => $sshUser,
            "ssh_pass" => $sshPass,
            "ssh_port" => (int)$sshPort,
        ], ["serviceid" => $params['serviceid']]);
    } else {
        insert_query("mod_cyberpanel_extra", [
            "serviceid" => $params['serviceid'],
            "ssh_user"  => $sshUser,
            "ssh_pass"  => $sshPass,
            "ssh_port"  => (int)$sshPort,
        ]);
    }
}