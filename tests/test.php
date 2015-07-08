<?php
error_reporting(E_ALL);
define('TEST_FOLDER', dirname(__FILE__));
include TEST_FOLDER . '/../Files.php';
include TEST_FOLDER . '/../Mysql.php';

// Test Backup file -> folder
// test 1
echo "starting test 1 \n";
$file = TEST_FOLDER.'/test1';
file_put_contents($file, 'test1');
echo file_exists($file) ? 'OK' : 'KO';
echo "\n";
$backup = new Shikiryu_Backup_Files(array($file));
$date = date('Ydm');
$backup->addDate($date)->backupToFolder('bu');
$backupFile = TEST_FOLDER.'/bu/'.$date.'.test1';
if (file_exists($backupFile)) {
    echo 'OK';
    unlink($backupFile);
} else {
    die('KO - see what\'s in bu');
}

/*
$backup->addDate()->addTime()->backupToEmail('from@gmail.com', 'to@gmail.com', 'test class', 'coucou');
$backup->addDate()->addTime()->backupToFTP('ftp.domain.com', 'login', 'password', '/folder');

$backup2 = new Shikiryu_Backup_MYSQL('localhost', 'login', 'password', 'db');
$backup2->fromTables(array('table1'))->addDate()->addTime();
$backup2->backupToFolder('bu');
?>
*/