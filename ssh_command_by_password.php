<?php
/**
 * Commande pour executer une commande distante sur un serveur via une connexion SSH par mot de passe
 * 
 * Usage :
 *
 *    ssh_command_by_password.php HOST USERNAME PASSWORD COMMAND
 *
 * Voir ssh_command.md pour plus d'infos
 */

$hostname = $argv[1];
$username = $argv[2];
$password = $argv[3];
$command  = $argv[4];

// Connexion au serveur
$ssh = ssh2_connect($hostname, 22);
if ( !ssh2_auth_password($ssh, $username, $password) ) {
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
