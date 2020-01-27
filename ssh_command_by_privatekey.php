<?php
/**
 * Commande pour executer une commande distante sur un serveur via une connexion SSH par clé privée
 * 
 * Usage :
 *
 *    ssh_command_by_privatekey.php HOST USERNAME PRIVATEKEY COMMAND
 *
 * Voir ssh_command.md pour plus d'infos
 */

$hostname   = $argv[1];
$username   = $argv[2];
$privatekey = $argv[3];
$command    = $argv[4];

// Connexion au serveur
$ssh = ssh2_connect($hostname, 22);
if ( !ssh2_auth_pubkey_file($ssh, $username, $privatekey.'.pub', $privatekey) ) {
    die('Connexion failed in '.$hostname);
}

// Envoi de la commande
$stream = ssh2_exec($ssh, $command);

// Retourne la sortie de la commande
stream_set_blocking($stream, true);
$response = '';
while ($buffer = fread($stream, 4096)) $response .= $buffer;
echo $response;
fclose($stream);
