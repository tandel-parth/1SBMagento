<?php
require_once('../app/Mage.php'); //Path to Magento
Mage::app();
echo "<pre>";

// FTP server details
$ftp_server = "127.0.0.1";
$ftp_username = "cccadmin";
$ftp_password = "cccadmin123";
$remote_file = "path/to/remote/file.txt";

// Establish a connection
$conn_id = ftp_connect($ftp_server);

if (!$conn_id) {
    die("Couldn't connect to FTP server");
}

// Login with username and password
if (@ftp_login($conn_id, $ftp_username, $ftp_password)) {
    echo "Connected as $ftp_username @$ftp_server\n";
} else {
    die("Couldn't connect as $ftp_username");
}

ftp_close($conn_id);
