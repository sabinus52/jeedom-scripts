# Script de commande SSH par mot de passe ou clé privée

Exécution d'une commande SSH sur un serveur distant.


## Par mot de passe

Usage :

~~~ bash
ssh_command_by_password.php HOST USERNAME PASSWORD COMMAND
~~~

- **USER** : Utilisateur de connexion
- **HOST** : Hostname du serveur
- **PASSWORD** : Mot de passe de connexion
- **COMMAND** : Commande shell à lancer


## Par clé privée

Usage :

~~~ bash
ssh_command_by_privatekey.php HOST USERNAME PRIVATEKEY COMMAND
~~~

- **USER** : Utilisateur de connexion
- **HOST** : Hostname du serveur
- **PRIVATEKEY** : Chemin de la clé privée
- **COMMAND** : Commande shell à lancer


## Exemple

~~~ bash
ssh_command_by_password.php 192.168.0.100 toto secret "sudo /sbin/poweroff"
ssh_command_by_privatekey.php 192.168.0.100 toto /home/toto/.ssh/id_rsa "sudo /sbin/poweroff"
~~~


## Exécuter des tâches d'administration sans mot de passe 

Changement des droits avec la commande `visudo`

~~~
toto ALL=(ALL) NOPASSWD: /sbin/poweroff,/chemin/complet/autrecommande
~~~

L'utilisateur *toto* a le droit de lancer la commande `poweroff` sans mot de passe
