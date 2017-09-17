<?php 
$from = $_GET['fr'];//'2016-09-01';
$to   = $_GET['to'];//date('Y-m-d H:i:s');

if(empty($from)){
	$from = '2016-09-24';
}
if(empty($to)){
	$to = date('Y-m-d');
}

function connectDb($host, $username, $password, $dbname)
{
	$dbcon = null;
	try{
		$dbcon = new PDO("mysql:host={$host};dbname={$dbname}", $username, $password);
		$dbcon->exec("SET time_zone='Asia/Dhaka';");
		$dbcon->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
		// set the PDO error mode to exception
		$dbcon->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
	} catch (Exception $e) {
		 echo "Database Connection Failed: " . $e->getMessage();
	}
	return $dbcon;
}


$databases = [
	'PMRS' => [
		'hostname'	=> '192.168.3.245',
		'username' 	=> 'root',
		'password' 	=> '',
		'database'	=> 'pmrs',
	],
	'PMRS_TEST' => [
		'hostname'	=> '192.168.3.245',
		'username' 	=> 'root',
		'password' 	=> '',
		'database'	=> 'pmrs_test',
	],
	'OPERATIONS' => [
		'hostname'	=> '192.168.3.245',
		'username' 	=> 'root',
		'password' 	=> '',
		'database'	=> 'aponjon-operations',
	],
	'BRIDGE' => [
		'hostname'	=> '172.16.11.6',
		'username' 	=> 'dnettechapi',
		'password' 	=> 'dnet73chap1',
		'database'	=> 'aponjon_service',
	],
];


$operations = connectDb($databases['OPERATIONS']['hostname'], $databases['OPERATIONS']['username'], $databases['OPERATIONS']['password'], $databases['OPERATIONS']['database']);
$pmrs = connectDb($databases['PMRS']['hostname'], $databases['PMRS']['username'], $databases['PMRS']['password'], $databases['PMRS']['database']);



## OPERATIONS
$sql = "SELECT COUNT(1) AS total
		FROM subscribers s
		WHERE s.`last_sync_time` BETWEEN '{$from} 00:00:00' AND '{$to} 23:59:59'
		AND s.`sync_status` = 'SUCCESS'
		AND s.`is_free` = 1";
$query = $operations->prepare($sql);
$query->execute();
$results = $query->fetchAll();
$operations_total =  isset($results[0]['total']) ? $results[0]['total'] : 0;
		
		
## PMRS
$sql = "SELECT COUNT(1) AS total
		FROM e_subscriber s
		WHERE s.`dtt_mod` BETWEEN '{$from} 00:00:00' AND '{$to} 23:59:59'
		AND s.`Status` = 'Synced'
		AND s.`is_free` = 1";
$query = $pmrs->prepare($sql);
$query->execute();
$results = $query->fetchAll();
$pmrs_total =  isset($results[0]['total']) ? $results[0]['total'] : 0;