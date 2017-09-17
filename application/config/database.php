<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/*
| -------------------------------------------------------------------
| DATABASE CONNECTIVITY SETTINGS
| -------------------------------------------------------------------
| This file will contain the settings needed to access your database.
|
| For complete instructions please consult the 'Database Connection'
| page of the User Guide.
|
| -------------------------------------------------------------------
| EXPLANATION OF VARIABLES
| -------------------------------------------------------------------
|
|	['hostname'] The hostname of your database server.
|	['username'] The username used to connect to the database
|	['password'] The password used to connect to the database
|	['database'] The name of the database you want to connect to
|	['dbdriver'] The database type. ie: mysql.  Currently supported:
				 mysql, mysqli, postgre, odbc, mssql, sqlite, oci8
|	['dbprefix'] You can add an optional prefix, which will be added
|				 to the table name when using the  Active Record class
|	['pconnect'] TRUE/FALSE - Whether to use a persistent connection
|	['db_debug'] TRUE/FALSE - Whether database errors should be displayed.
|	['cache_on'] TRUE/FALSE - Enables/disables query caching
|	['cachedir'] The path to the folder where cache files should be stored
|	['char_set'] The character set used in communicating with the database
|	['dbcollat'] The character collation used in communicating with the database
|				 NOTE: For MySQL and MySQLi databases, this setting is only used
| 				 as a backup if your server is running PHP < 5.2.3 or MySQL < 5.0.7
|				 (and in table creation queries made with DB Forge).
| 				 There is an incompatibility in PHP with mysql_real_escape_string() which
| 				 can make your site vulnerable to SQL injection if you are using a
| 				 multi-byte character set and are running versions lower than these.
| 				 Sites using Latin-1 or UTF-8 database character set and collation are unaffected.
|	['swap_pre'] A default table prefix that should be swapped with the dbprefix
|	['autoinit'] Whether or not to automatically initialize the database.
|	['stricton'] TRUE/FALSE - forces 'Strict Mode' connections
|							- good for ensuring strict SQL while developing
|
| The $active_group variable lets you choose which connection group to
| make active.  By default there is only one group (the 'default' group).
|
| The $active_record variables lets you determine whether or not to load
| the active record class
*/

$active_group = 'default';
$active_record = TRUE;

/*
## LOCAL
$db['default']['hostname'] = 'localhost';
$db['default']['username'] = 'root';
$db['default']['password'] = '';
$db['default']['database'] = 'pmrs';
*/

## PMRS SERVER
//$db['default']['hostname'] =  '119.148.18.131';
////$db['default']['hostname'] = '192.168.1.245';
////$db['default']['hostname'] = 'localhost';
//$db['default']['username'] = 'pmrsuser';
//$db['default']['password'] = 'InterfaceOfPmrs';
//$db['default']['database'] = 'pmrs';
$db['default']['hostname'] =  '192.168.3.245';
$db['default']['username'] = 'aponjonforms';
$db['default']['password'] = '@p0nj0n@f0rm3';
$db['default']['database'] = 'pmrs_test';
$db['default']['dbdriver'] = 'mysql';
$db['default']['dbprefix'] = '';
$db['default']['pconnect'] = FALSE;
$db['default']['db_debug'] = TRUE;
$db['default']['cache_on'] = FALSE;
$db['default']['cachedir'] = '';
$db['default']['char_set'] = 'utf8';
$db['default']['dbcollat'] = 'utf8_general_ci';
$db['default']['swap_pre'] = '';
$db['default']['autoinit'] = TRUE;
$db['default']['stricton'] = FALSE;

## M4B SERVER
//$db['m4b']['hostname'] = '202.22.194.67';
$db['m4b']['hostname'] = '103.239.252.103'; // Change Request by Rahat Bhai (05 Feb 2015)
$db['m4b']['username'] = 'dnet';
$db['m4b']['password'] = 'dnet123';
$db['m4b']['database'] = 'M4B';
$db['m4b']['dbdriver'] = 'mysql';
$db['m4b']['dbprefix'] = '';
$db['m4b']['pconnect'] = FALSE;
$db['m4b']['db_debug'] = FALSE;
$db['m4b']['cache_on'] = FALSE;
$db['m4b']['cachedir'] = '';
$db['m4b']['char_set'] = 'utf8';
$db['m4b']['dbcollat'] = 'utf8_general_ci';
$db['m4b']['swap_pre'] = '';
$db['m4b']['autoinit'] = TRUE;
$db['m4b']['stricton'] = FALSE;

/* End of file database.php */
/* Location: ./application/config/database.php */