# Email transport configuration

 ## JSON
```
"Email": {
  "to"        : "recipient@example.net;recipient2@example.net",
  "from"      : "<name>from@example.net",
  "encoding"  : "UTF-8",
  "subject"   : "[Example.net] backup of the day",
  "message"   : "Hi,\n\nYou can find your backup in this mail.\n\nRegards,\nMyself."
}
```
 ## General 
Emails are formated like this "email@domain.com" or like this "<name>email@domain.com"
 
 ## Options
to (mandatory)     = email addresses you want to send the backup to. Separated by semi-colon if more than one
from (mandatory)   = person who send the backup by email.
encoding           = email text encoding. Should stay with "UTF-8" or removed.
subject (mandatory)= email subject
message (mandatory)= email content sent as txt 