<?php

//die('made it');
//exit;

$databaseName = "wes-shebeads";

######################################################################
## MySQL Backup Script Configuration File
##
## Use this file to configure your settings for the script.
######################################################################
## For more documentation and new versions, please visit:
## http://www.dagondesign.com/articles/automatic-mysql-backup-script/
## -------------------------------------------------------------------
## Created by Dagon Design (www.dagondesign.com).
## Much credit goes to Oliver Mueller (oliver@teqneers.de)
## for contributing additional features, fixes, and testing.
######################################################################

######################################################################
## General Options
######################################################################

// Remember to always use absolute paths without trailing slashes!
// On Windows Systems: Don't forget volume character (e.g. C:).

// Path to the mysql commands (mysqldump, mysqladmin, etc..)
$MYSQL_PATH = '/usr/bin';

// Mysql connection settings (must have root access to get all DBs)
$MYSQL_HOST = 'localhost';
$MYSQL_USER = 'root';
$MYSQL_PASSWD = 'Karlkarl1';

// Backup destination (will be created if not already existing)
$BACKUP_DEST = '/home/shebeads2/public_html/backup/backups';

// Temporary location (will be created if not already existing)
$BACKUP_TEMP = '/home/shebeads2/public_html/backup/backups_tmp';

// Show script status on screen while processing
// (Does not effect log file creation)
$VERBOSE = true;

// Name of the created backup file (you can use PHP's date function)
// Omit file suffixes like .tar or .zip (will be set automatically)
$BACKUP_NAME = 'ksdvd1_'.$databaseName.'_mysql_backup_' . date('Y-m-d');

// Name of the standard log file
$LOG_FILE = $BACKUP_NAME . '.log';

// Name of the error log file
$ERR_FILE = $BACKUP_NAME . '.err';

// Which compression program to use
// Only relevant on unix based systems. Windows system will use zip command.
$COMPRESSOR = 'bzip2';


######################################################################
## Email Options
######################################################################


// Email the backup file when finished?
$EMAIL_BACKUP = true;

// If using email backup, delete from server afterwards?
$DEL_AFTER = false;

// The backup email's 'FROM' field
$EMAIL_FROM = ''.$databaseName.' Mysql Backup';

// The backup email's subject line
$EMAIL_SUBJECT = 'SQL Backup for '.$databaseName.' KSDVD1 Server ' . date('Y-m-d') . ' at ' . date('H:i');

// The destination address for the backup email
$EMAIL_ADDR = 'karl@webksd.com';


######################################################################
## Error Options
######################################################################

// Email error log to specified email address
// (Will only send if an email address is given)
$ERROR_EMAIL = $EMAIL_ADDR;

// Subject line for error email
$ERROR_SUBJECT = 'KSDVD1 SQL '.$databaseName.' BACKUP ERROR: ' . $EMAIL_SUBJECT;


######################################################################
## Advanced Options
## Be sure you know what you are doing before making changes here!
######################################################################
// A comma separated list of databases, which should be excluded
// from backup
// information_schema is a default exclude, because it is a read-only DB anyway
$EXCLUDE_DB =   'information_schema,
                test,
                wes-bradleyreps,
                wes-demo,
                wes-infinitespanish,
                wes-karlsteltenpohl,
                wes-kidsplayworld,
                wes-mecis,
                wes-mega,
                wes-osplacejazz,
                wes-repository,
                wes-servicesperfect,
                wes-shebeads-dev,
                wes-test,
                wes-v15,
                wes-webksd,
                wes-wescms,
                wesley';

// Defines the maximum number of seconds this script shall run before terminating
// This may need to be adjusted depending on how large your DBs are
// Default: 18000
$MAX_EXECUTION_TIME = 90000;

// Low CPU usage while compressing (recommended) (empty string to disable).
// Only relevant on unix based systems
// Default: 'nice -n 19'
$USE_NICE = 'nice -n 19';

// Flush tables between mysqldumps (recommended, if it runs during non-peak time)
// Default: false
$FLUSH = false;

// Optimize databases between mysqldumps.
// (For detailed information look at
// http://dev.mysql.com/doc/mysql/en/mysqlcheck.html)
// Default: false
$OPTIMIZE = false;

######################################################################
## End of Options
######################################################################

?>