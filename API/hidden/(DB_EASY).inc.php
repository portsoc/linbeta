<?php	// DB_UTILS.INC.PHP - Re-usable database		(c) C Lester
	// ================   utilities in PDO     2013 (mysqli), 2014 (PDO)


class DB_easy {

    /* ============================== PRIVATE =========================== */

    private $_DB;	// Refers to the underlying PDO object

    /* ============================== PRIVATE =========================== */

    // DB_exists returns TRUE if the connection given by $dbhost has
    // a database named $dbname. This method could reasonably be public.
	    // NB: SHOW DATABASES LIKE isn't Standard SQL - it's one of
	    // MySQL's naughty "extras".

    private function DB_exists($dbhost,$dbname)
      { $showquery = "show databases like '$dbname'";
	$showresult = $dbhost->query($showquery);
	return (boolean) ($showresult->fetch()); }
	// i.e., return TRUE is there's a row, FALSE if there are no rows.

    
    /* ****** Validator functions for quick tidy death when needed. ****** */


    // Failure exits, saying $bad failed, with the usual error number and
    // message from the $db object.

    private function Failure($db,$bad,$file,$line)
      { echo "<br/><br/><b>Crash:</b> ".
	     "&ldquo;$bad&rdquo; failed at line $line of $file - the MySQL ".
	     "error is <i>".$db->errorInfo()[1]." ".$db->errorInfo()[2]."</i>";
	exit; }


      private function Query_and_Validate($db,$sql,$file,$line)
      { $result = $db->query($sql);
	if (!$result) DB_easy::Failure($db,$sql,$file,$line);
	return $result; }



    /* ============================== PUBLIC ============================ */


    /* *********** Opening and closing the database *********** */

    // new DB_easy() first connects to the MySQL engine with previously
    // DEFINEd application constants _db_host_, _db_user_, and _db_pw_ as
    // parameters, then opens the database whose name is given by an
    // application constant named _db_nbame_: if the database doesn't exist,
    // this constructor will create the database and initialize it using an
    // application function with spec
    //		Initialize_myDatabase($DB,$EZ)
    // where $DB refers to the current connection object, and $EZ refers to
    // this instance of DR_easy.

    public function __construct()
      { global $_DB;
	//
	// CONNECT TO THE DATABASE SERVER
        $dsn = "mysql:"._db_host_.";charset=UTF-8";
	$option = array(PDO::ATTR_ERRMODE=>PDO::ERRMODE_EXCEPTION);
			// Manana: maybe also PDO::ATTR_PERSISTENT=>TRUE ??
	try { $_DB = new PDO($dsn,_db_user_,_db_pw_,$option); }
	    catch (PDOException $failure)
	          { DB_easy::Failure("CONNECT failed - ".
					$_DB->getCode()." ".
	    				$_DB->getInfo()," ".
					$file,$line); }
	// DOES THE DATABASE EXIST YET?
	if (DB_easy::DB_exists($_DB,_db_name_))
          //
	  { // THE DATABASE EXISTS: SO MERELY SELECT IT.
          $_DB->exec("USE "._db_name_); } //[Fixed params so no prepare().]
        else // ****** ??? wrap the following in a Transaction? ??? ******
          //
          { // THE DATABASE DOESN'T YET EXIST, SO CREATE IT...
            DB_easy::Query("CREATE DATABASE "._db_name_,__file__,__line__);
            // ... SELECT IT ...
            $_DB->exec("USE "._db_name_); //
            // ... AND DO WHATEVER CREATES ETC THE APPLICATION NEEDS.
	    Initialize_myDatabase($_DB,$this);
      }   }


    // Close() closes the connection establisged by the above constructor.

    public function Close()
      { global $_DB;

	$_DB = null; }	// ...which in PDO MIGHT garbage collect the
        		// connection object, thereby closing the connection
                        // ... unless I start using the "persitent" option.


    /* *********** Queries - simple and prepaared   *********** */

    /* ****    Most likely, Query will be called by GET    **** */
    /* ****    reponders, and PQuery by POST responders.   **** */


    // Query does (without preparation) the SQL query given by $sql on this
    // object's database connection, returning a reference to the resulting
    // PDOStatement. DIEs if the query fails, giving a message including $sql,
    // $file, and $line.


    public function Query($sql,$file,$line)
      { global $_DB;
	try {   $result = $_DB->query($sql);
		return $result; }
            catch (PDOException $failure )
		{ DB_easy::Failure($_DB,$sql,$file,$line); }
      }


    // PQuery PREPAREs the SQL query given by $sql on this object's database
    // connection, and EXECUTEs the prepared statements with the given array
    // of bindings: if $sql has named placeholders (i.e. of format :name //
    // rather than ?), then the bindings array should be associative. Returns a
    // ereference to the PREPAREd-and-EXECUTEd PDOStatement. DIEs if the query
    // fails, giving a message including $sql, $file, and $line.


    public function PQuery($sql,$bindings,$file,$line)
      { global $_DB;
	try {   $prepared = $_DB->prepare($sql);
		$prepared->execute($bindings);
		return $prepared; }
            catch (PDOException $failure )
		{ DB_easy::Failure($_DB,$sql,$file,$line); }
      }


    /* ****** Three ECHO functions to display a table. Each takes   ****** */
    /*        one parameter, which should be the result of an SQL          */
    /*        query that successfully returned a table.                    */

    public function Echo_associative($®)
      { // $®->data_seek(0);	// in case this wasn't the first use of $®...
	echo "<br/>\r\n";
	$®->setFetchMode(PDO::FETCH_ASSOC);
	while ($row = $®->fetch())
	  { foreach ($row as $cellname => $cell)
		echo "$cellname = $cell &nbsp; ";
	    echo "<br/>\r\n";
      }   }

    public function Echo_tabulate($®)
      { // $®->data_seek(0);	// in case this wasn't the first use of $®...
	$selectquery = "SELECT * FROM MyTable";
	$selectresult = ($dbhost->query($selectquery));
	$selectresult->setFetchMode(PDO::FETCH_ASSOC);
	echo "\r\n\n<p>Fetching as table:";
        if ($selectresult->rowCount() == 0)
          { echo "\r\n<br>Empty table"; return; }
	echo "\r\n<p><table border=1 cellspacing=0 cellpadding=2>";
	$needheads=true;
	while ($row=$selectresult->fetch())
	  { if ($needheads)
	      { echo "\r\n<tr>";
	        foreach ($row as $index => $value) echo "<td>$index</td>";
		echo "</tr>";
		$needheads=false; }
	    echo "\r\n<tr>";
	    foreach ($row as $index => $value) echo "<td>$value</td>";
	    echo "\r\n<tr>"; }
	echo "\r\n</table>";
      }


}

?>
