# FTP transport configuration

 ## JSON
```
"Ftp": {
  "host"      : "ftp.domain.com",
  "login"     : "login",
  "password"  : "password",
  "folder"    : "/folder"
},
```
 ## General 
-
 
 ## Options
host (mandatory)   = ftp host without schema ("ftp://")
login              = ftp login (mandatory if not anonymous)
password           = ftp password (mandatory if not anonymous)
folder             = the folder you want to put the backup in. If not set, the backup will be put in the ftp root


