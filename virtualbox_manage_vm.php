#!/usr/bin/php
<?php
/**
 * Commande pour démarrer des machines virtuelles VirtualBox distantes sur un serveur linux
 * Usage :
 *
 *    virtualbox_manage_vm.php <host> <login> <password> <action> <vm_name>
 *
 * host     : Host ou IP de l'hôte où se trouve la VM
 * login    : Login SSH de connexion de l'hôte où se trouve la VM
 * password : Mot de passe de connexion de l'hôte où se trouve la VM
 * action   : Action à effectuer (start|poweroff|running)
 * vm_name  : Nom de la machine virtuelle à démarrer
 */
$hostname = $argv[1];
$username = $argv[2];
$password = $argv[3];
$action   = $argv[4];
$vmname   = $argv[5];

// Commande à executer
switch($action) {
    case 'start' : 
    	$info = false;
        $command = 'VBoxManage startvm "'.$vmname.'" --type headless';
        break;
    case 'poweroff' :
    	$info = false;
    	$command = 'VBoxManage controlvm "'.$vmname.'" acpipowerbutton';
    	break;
    case 'running' :
    	$info = true;
    	$command = 'VBoxManage showvminfo "'.$vmname.'" | grep "^State:\s*running"';
    	break;
    default : exit();
}

// Connexion au serveur
$ssh = ssh2_connect($hostname, 22);
ssh2_auth_password($ssh, $username, $password);

// Envoi de la commande
$stream = ssh2_exec($ssh, $command);
stream_set_blocking($stream, true);

// Retourne la sortie de la commande
if ($info) {
	$response = '';
	while ($buffer = fread($stream, 4096))
    	$response .= $buffer;
  	switch ($action) {
      	case 'running' :
            $response = (preg_match('/running/', $response)) ? 1 : 0;
          	break;
    }
    echo $response;
}
fclose($stream);

?>