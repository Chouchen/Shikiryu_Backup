# Mysql backup configuration

 ## JSON
```
"Mysql": {
  "host"  : "mysql address",
  "login" : "mysql login",
  "pwd"   : "mysql password",
  "db"    : "mysql database",
  "tables": [
    ""
  ]
}
```
 ## General 
You can indicate "*" or nothing into tables to save every tables in the selected database.
 
 ## Options
host (mandatory)     = mysql host
login (mandatory)    = mysql login
pwd (mandatory)      = mysql password
db (mandatory)       = database where the tables you want to save are in.
tables (mandatory)   = array of tables


