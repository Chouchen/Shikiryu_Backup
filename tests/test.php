<?php
error_reporting(E_ALL);
include dirname(__FILE__) . '/Files.php';
include dirname(__FILE__) . '/Mysql.php';

$backup = new Shikiryu_Backup_Files(array(dirname(__FILE__).'/newhtml.html'));
$backup->addDate()->addTime()->backupToEmail('from@gmail.com', 'to@gmail.com', 'test class', 'coucou');
$backup->addDate()->addTime()->backupToFTP('ftp.domain.com', 'login', 'password', '/folder');
$backup->backupToFolder('bu');

$backup2 = new Shikiryu_Backup_MYSQL('localhost', 'login', 'password', 'db');
$backup2->fromTables(array('table1'))->addDate()->addTime();
$backup2->backupToFolder('bu');
?>
