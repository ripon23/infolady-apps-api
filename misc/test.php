<?php
include 'DbConfig.php';

try {
    $db = new mysqli(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME);

    if ($db->connect_errno) {
        throw new Exception("Failed to connect to MySQL: [" . $db->connect_errno . "] " . $db->connect_error);
    }
    $db->set_charset("utf8");
} 
catch (Exception $e) {
    die("Could not connect to database:" . $e->getMessage());
}

/*
$divisions = array();
$sql = "SELECT * FROM division";
$query = $db->query($sql) or die('ERROR: '.$sql.' > '.$db->error);

while ($row = $query->fetch_assoc()) {
    $divisions[$row['did']] = $row['division'];
    //echo '<pre>'; print_r($row); echo '</pre>'; die;
}
echo '<pre>'; var_export($divisions); echo '</pre>';
*/

/*
$districts = array();
$sql = "SELECT * FROM District ORDER BY divid, district ASC";
$query = $db->query($sql) or die('ERROR: '.$sql.' > '.$db->error);

while ($row = $query->fetch_assoc()) {
    //echo '<pre>'; print_r($row); echo '</pre>'; die;
    $districts[$row['divid']][$row['dsid']] = $row['district'];    
}
echo '<pre>'; var_export($districts); echo '</pre>'; die;
*/

// Upazilla
$upazillas = array();
$sql = "SELECT * FROM Upazilla ORDER BY disid, upazilla ASC";
$query = $db->query($sql) or die('ERROR: '.$sql.' > '.$db->error);

while ($row = $query->fetch_assoc()) {
    //echo '<pre>'; print_r($row); echo '</pre>'; die;
    $upazillas[$row['disid']][$row['uid']] = $row['upazilla'];    
}
echo '<pre>'; var_export($upazillas); echo '</pre>'; die;