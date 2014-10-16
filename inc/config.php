<?php

DEFINE( "DEBUG", FALSE );
DEFINE( "LOGGING", TRUE );
DEFINE( "LOGFILE", $_SERVER["DOCUMENT_ROOT"] . "/log/linora.log" );

DEFINE( "DBHOST", "127.0.0.1" );	// location of the SQL database
DEFINE( "DBUSER", "rjb" );			// DB user with
DEFINE( "DBPW", "rjb" );			// Password for DB user
DEFINE( "DBNAME", "Linora" );		// DB name for the app

DEFINE( "FORCE_JSON", TRUE );

DEFINE( "DBINIT",
    "CREATE TABLE entries (
        id bigint not null auto_increment,
		cat VARCHAR(100),
		url VARCHAR(500),
		cap VARCHAR(500),
		PRIMARY KEY (id)
	)"
);

// Create the table


?>