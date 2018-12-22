#!/usr/bin/php
<?php
/**
 * Commande pour connaitre l'état d'une barre se son Bose SoundTouch ou bien envoyer une commande
 * Usage :
 *
 *    bose_soundtouch_command.php <host> <commnd>
 *
 * host     : Host ou IP de l'enceinte Bose
 * command  : commande à envoyer à l'enceinte
 * Liste des commandes :
 *    playing, volume, bass, source : pour récupérer les infos de l'enceinte
 *    key:KEY : Appui touche télécommande avec KEY= POWER MUTE VOLUME_UP VOLUME_DOWN ...
 *    volume:VALUE : Mettre le niveu de vlume à la valeur VALUE
 * Exemples :
 *    bose_soundtouch_command.php mybose key:POWER   pour allumer ou éteindre l'enceinte
 *    bose_soundtouch_command.php mybose volume:25   pour le volume à 25%
 *    bose_soundtouch_command.php mybose volume      pour connaitre le niveau de volume actuel
 */
if (!isset($argv[2])) die('Manque paramètres');
$hostname = $argv[1];
$param  = $argv[2];



class SoundTouchCommand
{

    const BASE_URI = 'http://%s:8090/';

    private $baseUri;

    public function __construct($hostname) {
        $this->baseUri = sprintf(self::BASE_URI, $hostname);
    }

    private function get($path) {
        $curl = curl_init($this->baseUri.$path);
        curl_setopt($curl, CURLOPT_HEADER, false);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        $response = curl_exec($curl);
        curl_close($curl);
        return $response;
    }

    private function post($path, $body) {
        $curl = curl_init($this->baseUri.$path);
        curl_setopt($curl, CURLOPT_HEADER, false);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_POST, true);
        curl_setopt($curl, CURLOPT_HTTPHEADER, array('Content-type: text/xml'));
        curl_setopt($curl, CURLOPT_POSTFIELDS, $body);
        $response = curl_exec($curl);
        curl_close($curl);
        return $response;
    }

    public function sendCommand($command) {
        $this->post('key', '<key state="press" sender="Gabbo">'.$command.'</key>');
        return $this->post('key', '<key state="release" sender="Gabbo">'.$command.'</key>');
    }

    public function setVolume($value) {
        return $this->post('volume', '<volume>'.intval($value).'</volume>');
    }

    public function getResponse($command) {
        $response = $this->get($command);
        return simplexml_load_string($response);
    }
}



$bose = new SoundTouchCommand($hostname);

$params = explode(':', $param);
if (count($params) == 2) {
    list($command, $value) = $params;
}
else {
    $command = $params[0];
    $value = null;
}

switch ($command) {
    case 'playing':
        $response =  $bose->getResponse('now_playing');
        print $response->playStatus;
        break;
    case 'source':
        $response =  $bose->getResponse('now_playing');
        print $response->ContentItem['sourceAccount'];
        break;
    case 'volume':
        if ($value) {
            print $bose->setVolume($value);
        }
        else {
            $response = $bose->getResponse('volume');
            print $response->actualvolume;
        }
        break;
    case 'bass':
        $response = $bose->getResponse('bass');
        print $response->actualbass;
        break;
    case 'key':
        print $bose->sendCommand($value);
        break;
    default:
        print 'unknow';
        break;

    // Retourne que du XML actuellement
    /*case 'info':
        print $bose->getResponse('info');
        break;
    case 'presets':
        print $bose->getResponse('presets');
        break;
    case 'sources':
        print $bose->getResponse('presets');
        break;*/

}

die("\n");
