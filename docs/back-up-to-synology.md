# Back up to a Synology NAS

## Part 1. NAS

### 1.1. User

You'll need to create a special user which will have very restricted access to your NAS or a group with multiple users (if you need to separate different backup for example).
For that, go to your control panel and add a group, call it `External` for example. 
  - Restrict access, with `no access` on every folder you got.
  - In `Applications`, just give access to `FTP` (we're not going to use FTP but SFTP)
  
Still in your control panel, go to `users` to create a new one.
  - Give it a name (called it `Backup` for example), an email (choose one of yours) and a very effective password (you can let your synology choose one for you)
  - You don't have to create a user "home"
  - Put it in your previous group (`External`)
  - Don't let the user an option to change password itself.
  
### 1.2. Folder

Now that we have our user(s), we'll need a place to put our backups. For that, we won't be using an existing folder, because rights are pretty tough to manage in `File Station` and because even with good rights, the new user could still download the backups which you don't want to happen in case of hack (I'll explain later)

So, open `File Station` and click on `create`, `new shared folder`
  - Give it a name ( `backup` for example )and eventually a description.
  - Click on the option `Hide sub-folder and files from users without authorization`
  - For more security, you can cypher the backups (option you don't have in an existing folder by default)
  - Restrict access to all user but the new created user and the one you use (and who will be the only one with download and read access)
  - In `advanced`, click on `disable file download` (option you don't have in an existing folder by default)
  - You don't have to index the file, IMHO.
  
### 1.2. SFTP

Still in the control panel, go to `Files services`, then `ftp` but **don't** activate FTP. Then activate `SFTP`, if possible on a different port than the one by default.
You may have some configurations to do depending of your existing security workflow. Do it, I'll wait.
  
## Part 2. Shared server

### 2.1. Script installation

Alright, now, we have a user who can write files in a single crypted folder only by SFTP, without download access. Seems pretty secured.

Now, on the shared server, install this project. If possible not within the `docroot` (`public_html`, `www`, etc.), but in a place not accessible from the Internet.
In `app/scenario/`, install a new JSON file named `backup.json` for example (if you need more than one backup on this server, name it more specifically, it'll help ;-) )
I'll let you see into this doc on **how to use a scenario**, but for example, that's what inside a json to backup a **whole MySQL Database to your Synology NAS**

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

To save periodically, you may have to do it yourself, but most shared server propose CRON job (more or less limited).

#### 2.2.1. You can use CRON (yay)

Go to your hosting platform and configure it. All interfaces are different and I can't do a tutorial for all of them.
Just now that, you need to create a .php file with this in it :

```
<?php
include_once 'path/to/Scenario.php';
try {
    \Shikiryu\Backup\Scenario::launch('backup.json'); // whatever the file name you gave previously
} catch (\Exception $e) {
    echo $e->getMessage();
}
```

Your CRON job must activate this file on the period you want. And, that's it.

#### 2.2.2. You can't use CRON (oh)

Then, the only solutions are:

  - Do it yourself, without scripts.
  - Do it yourself with this script. You only need to put the same php file that you need with CRON but within the docroot in a URL known only by yourself. The scenario and this project files can stay outside the docroot.


## Hacks

### Shared server

Oops, your website has been hacked, you must be pretty scared for your personal files on your NAS!
Don't worry. If you did everything right, the only thing hackers will have access to is the list of your backup files (but not their contents!)

Change your user's password on your NAS and problem solved (for your NAS ; I'm sorry for your website though)

### NAS

#### Case 1
Oh no, someone brute-forced my `backup` account on my NAS! They can have my database!
Huh, nope. They can only have access to the list of your backup files and can "only" upload things on your NAS.
The only harm they can do then is to fill your hard drive.

Change your user password (to something REALLY difficult this time), delete those files (and install a AV?)

#### Case 2
Oooooh nnoooo, someone accessed my regular NAS account! They have all my NAS and so, my backups too! I'm screwed, right?
Yup. Pretty much screwed. I feel sorry for you but hey, at least, this is not because of this project :-)

You should have a better password for your regular user, a better security policy on your NAS (and Synology offers many options) AND a 2-step login (seriously, that's the best)
