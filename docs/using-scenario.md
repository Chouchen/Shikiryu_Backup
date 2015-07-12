Scenario is based on a json explaining how the backup will work.
It contains 2 bases :
  - backup (what you're going to backup)
  - transport (how and where you're going to backup)

# backup
  - must begin with an uppercase
  - a PHP class with the same name must exists in the Backup folder

# transport
  - must begin with an uppercase
  - a PHP class with the same name must exists in the Transport folder

# configuration of backup and transport
  - all protected variable of the aimed class can be used as a configuration
  
# tips 
## JSON
The JSON must be compatible with the [PHP `json_decode`](http://php.net/manual/en/function.json-encode.php) implementation which correspond to [this one](http://www.faqs.org/rfcs/rfc4627.html)

Basically :
  - everything surrounded by " and no '
  - no comments
  - escape escape escape
  


