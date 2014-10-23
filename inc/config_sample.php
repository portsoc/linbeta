<?php
/**
 * @author: Rich Boakes <rjb@port.ac.uk>
 */

/* Database */
DEFINE( "DBHOST",       "127.0.0.1" );	// Hostname of IP address of the SQL DB server
DEFINE( "DBNAME",       "linora"    );	// Name of the database to use
DEFINE( "DBUSER",       "changeme"  );	// User with read/write DB permission
DEFINE( "DBPW",         "changeme"  );	// Password for DB user

/* Debugging */
DEFINE( "DEBUG",        FALSE       );  // a high level debug switch for application debugging

/* Logging */
DEFINE( "LOGGING",      FALSE       );  // Turn logging on or off
DEFINE( "LOGFILE",                      // location of the log file
    __DIR__ .
    "/log/linora.log"
);

/**
 * Do not edit below this line without safety goggles.
 *
 * DB Create Statements - these are separated from the
 * DB library because they are application specific.
 *
 * Currently there is just one table but others can be
 * added by chaining statements using semicolons.
 */
DEFINE( "DBINIT",
    "CREATE TABLE entries (
        id bigint not null auto_increment,
		cat VARCHAR(100),
		url VARCHAR(500),
		cap VARCHAR(500),
		PRIMARY KEY (id)
	)"
);
?>