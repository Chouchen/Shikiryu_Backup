# Back up vers un NAS Synology

## Part 1. NAS

### 1.1. Utilisateur

Vous allez avoir besoin d'un utilisateur spécial qui aura des accès très restreint à votre NAS ou un groupe avec plusieurs utilisateurs avec les mêmes restrictions (si vous avez besoin de séparer différents backups par exemple).
Pour ça, allez dans votre *panneau de configuration* et *ajouter un groupe*, appelez le `External` par exemple. 
  - Restreignez les accès, avec `aucun accès` sur tous les dossiers déjà présents.
  - Dans `Applications`, donnez juste un accès à `FTP` (ne vous inquiétez pas, nous n'allons pas utiliser FTP mais SFTP)
  
Toujours dans votre *panneau de configuration*, allez dans `utilisateurs` pour en créer un nouveau.
  - Donnez lui un nom (`Backup` par exemple), un email (utilisez l'un des vôtre) et un mot de passe *puissant* (vous pouvez laisser votre Synology en choisir un pour vous)
  - Vous n'avez pas à lui créer une "home"
  - Mettez le dans le groupe précédemment créé (`External`)
  - Ne laissez pas l'option permettant à l'utilisateur de changer son mot de passe lui-même.
  
### 1.2. Dossier

Une fois votre/vos utilisateur/s créé/s, vous allez devoir mettre en place vos backups. Pour ça, vous n'allez pas utiliser un dossier existant, parce que les droits ne sont pas facilement gérables depuis `File Station` et parce que, même avec les bons droits, le nouvel utilisateur pourra toujours télécharger les fichiers de backup ce dont on se passerait bien en cas de hack (j'en reparle plus tard).

Donc, ouvez `File Station` et cliquez sur `créer`, `nouveau dossier partagé`
  - Donnez lui un nom ( `backup` par exemple ;-) ) et éventuellement une description.
  - Cliquez sur l'option `Cacher les sous-dossiers et les fichiers des utilisateurs sans autorisation`
  - Pour plus de sécurité, vous pouvez chiffrer ces fichiers de backup (option que vous n'avez pas dans un dossier par défaut)
  - Restreignez les accès à tous les utilisateurs sauf celui nouvellement créé et celui que vous utilisez par défaut (et qui sera le seul à pouvoir lire et télécharger ces fichiers)
  - Dans `avancé`, cliquez sur `désactivé le téléchargement des fichiers` (option que vous n'avez pas dans un dossier par défaut)
  - Vous n'avez pas à indexer le dossier, AMHA.
  
### 1.2. SFTP

Toujours dans le *panneau de configuration*, allez dans `Files services`, puis `ftp` mais n'activez **pas** FTP. Activez `SFTP`, et, si possible, utilisez un port différent de celui par défaut.
Vous aurez sans doute des configurations supplémentaires à effectuer selon votre workflow de sécurité. Allez-y, faites les, j'attends.
  
## Part 2. Serveur mutualisé

### 2.1. Installation du script

Bon, maintenant, nous avons un utilisateur qui peut écrire des fichiers dans un seul dossier crypté avec un accès par SFTP uniquement et sans permission de téléchargement. ça semble plutôt sécurisé.

Côté serveur mutualisé, installez ce projet. Si possible, ne le mettez pas dans le `docroot` (`public_html`, `www`, etc.), mais dans un endroit non accessible depuis une URL.
Dans `app/scenario/`, placez un nouveau fichier JSON appelé `backup.json` par exemple (si vous avez besoin de plus d'un backup sur ce serveur, utilisez un nom plus spécifique, ça aide ;-) )
Je vous laisse regarder la documentation de **how to use a scenario** (à traduire), mais par exemple, voici ce que donnerait le JSON pour sauvegarder **votre base de donnée MySQL vers votre NAS Synology**

```
{
  "backup": {
    "Mysql": {
      "host"  : "mysql host",
      "login" : "mysql login",
      "pwd"   : "mysql password",
      "db"    : "mysql database",
      "tables": "*"
    }
  },
  "transport": {
    "Sftp": {
      "host"      : "your nas URL or IP",
	  "port"      : 22,
      "login"     : "your new user name",
      "password"  : "your new user password",
      "folder"    : "/backup"
    }
  }
}
```

### 2.2. CRON

Pour sauvegarder régulièrement, vous devrez le faire manuellement à moins que votre serveur mutualisé propose des CRON jobs (plus ou moins limités).

#### 2.2.1. Vous avez la possibilité de faire des CRON (yay)

Allez sur votre plate-forme de gestion des CRONs et configurez le. Toutes les interfaces sont différentes et je ne pourrais pas faire de tutoriels pour chacune d'en elles.
Sachez seulement que vous aurez besoin de créer un fichier php avec ceci :

```
<?php
include_once 'path/to/Scenario.php'; // chemin vers ce fichier du projet
try {
    \Shikiryu\Backup\Scenario::launch('backup.json'); // le nom du fichier json créé précédemment
} catch (\Exception $e) {
    echo $e->getMessage();
}
```

Votre CRON job doit excuter ce fichier à la période souhaitée. Et, c'est tout.

#### 2.2.2. Vous ne pouvez pas faire de CRON job (oh)

Alors, les seules solutions sont :

  - Faites le vous-même, sans scripts
  - Faites le vous-même avec ce script. Vous aurez juste besoin du fichier PHP indispensable au CRON (l'exemple ci-dessus) mais dans le `docroot` et sur une URL connue de vous seul. Le scenario et ce projet peut être hébergé en dehors du docroot.

## Hacks

### Serveur mutualisé

Oops, votre site web a été hacké, vous devez être plutôt inquiet pour vos fichiers personnels hébergés sur votre NAS !
Ne vous inquiétez pas. Si vous avez tout bien fait, la seule chose à laquelle les hackeurs auront accès est la liste de vos fichiers de backup (mais pas leur contenu !) 

Changez le mot de passe de votre utilisateur `backup` sur votre NAS et ils n'auront plus accès du tout. Problème résolu (pour votre NAS en tout cas ; désolé pour votre site web quand même :/ )

### NAS

#### Cas 1
Oh non ! Quelqu'un a brute-forcé le compte `backup` de mon NAS ! Ils peuvent avoir accès aux backup (potentiellement la base de données !)
Heu, non. Ils ont seulement accès à la liste de fichiers sauvegardés mais pas leur contenu. Ils peuvent "juste" téléverser des choses sur votre NAS. 
Le seul danger c'est qu'ils remplissent votre disque dur.

Changez le mot de passe (pour quelque chose de VRAIMENT plus difficile cette fois) et effacez les fichiers qu'ils auraient pu envoyer. Vous pouvez aussi prévoir un quota pour cet utilisateur.

#### Cas 2
Oooooh nnoooonnnn, quelqu'un a accès à mon compte usuel sur mon NAS ! Ils ont pouvoir sur mon NAS et donc sur mes backups ! Je suis foutu, hein ?
Oui. Vraiment foutu. Je suis désolé pour vous mais bon, au moins, ce n'est pas de la faute de ce projet :-)

Vous devriez avoir un meilleur mot de passe pour votre utilisateur usuel, une meilleure politique de sécurité pour votre NAS (et Synology propose plusieurs solutions) ET un double authentification. (vraiment, c'est le best)
