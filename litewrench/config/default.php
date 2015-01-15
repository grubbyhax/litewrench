<?php
//config.php
//Domain configuration file

//*!*NB: Currently these variables are available in the GLOBAL space!

//*** SERVER SETTINGS ***//
//Plugin file
//$config['plugin'] = '../monkeywrench/mw_plugin.php';

//Install password
$config['x_install'] = '';

//Domain name.
$config['root_url'] = 'http://127.0.0.1';
$config['root_url'] = 'http://www.somename.org';

//Name of base sitemap file.
$config['sitemap'] = 'root';

//Name of the default theme
$config['theme'] = 'default';

//Publish version.
$config['version'] = '1.0';

//Default language
$config['language'] = 'en';

//Character encoding type.
$config['encoding'] = 'utf-8';

//Default timezone
$config['timezone'] = 'Australia/Sydney';

//Debug switch.
$config['debug'] = false;

//Email switch.
//*!*This turns on the mailer helper, I'll have to comment on this further
$config['email'] = false;

//Email inbox/reply address
$config['inbox'] = 'email@myinbox.com';

//Default data settings
$config['view'] = '1xxx-xxxx';
$config['edit'] = '0xxx-xxxx';
$config['lock'] = '1111-1111';
$config['status'] = 'active';

$config['access'] = 8;

//*** DATABASE SETTINGS ***//
//Data storage model.
//Params(lite, full)
// file - uses files to store data in data/ folder
// full - uses database to store data.
// file storage is for rapid development/deployment web applications using a custom data model.
// full is the default model which uses the standard data handling methods.
$config['storage'] = 'lite';
$config['driver'] = 'file';

//If data storage model is on full these are the database settings.

/* LOCAL */
//We're doing multiple databsase connections here, not sure how I'm going to rout them.
$config['x_database'] = 'myname';
$config['x_username'] = 'root';
$config['x_password'] = '';
$config['x_location'] = 'localhost';
/**/

/* LIVE *
//We're doing multiple databsase connections here, not sure how I'm going to rout them.
$config['x_database'] = 'myname';
$config['x_username'] = 'root';
$config['x_password'] = '';
$config['x_location'] = 'localhost';
/**/


//*** GLOBAL TEXT ***//
$config['organisation'] = 'David Thomson';

?>