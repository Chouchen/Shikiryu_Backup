# SFTP transport configuration

 ## JSON
```
"Sftp": {
  "host"      : "ftp.domain.com",
  "port"      : 22,
  "login"     : "login",
  "password"  : "password",
  "folder"    : "/folder"
},
```
 ## General 
-
 
 ## Options
host (mandatory)    = sftp host without schema ("ftp://")
port                = sftp port (default: 22)
login (mandatory)   = sftp login (mandatory if not anonymous)
password (mandatory)= sftp password (mandatory if not anonymous)
folder              = the folder you want to put the backup in. If not set, the backup will be put in the ftp root


