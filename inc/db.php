<?php

/**
 * Class DBException
 */
class DBException extends Exception { }

/*
Re-usable database utilities in PDO
(c) C Lester 2013 (mysqli), 2014 (PDO)
*/

class DB {

    private $pdo;

    /*
     * @param $dbname The name of the database to look for.
     * @return boolean is true of the connection of this object contains a database like the one named.
     * @todo Find an alternative to "SHOW DATABASES LIKE" which isn't Standard SQL - it's one of MySQL's naughty "extras" see also http://stackoverflow.com/questions/15177652/pdo-check-if-database-exists
    */
    private function dbExists($dbname) {
        $showquery = "show databases like '$dbname'";
        $showresult = $this->pdo->query($showquery);
        return (boolean)($showresult->fetch());
    }


    /**
     * @param msg $
     * @throws DBException
     */
    private function throwException($msg = "Unknown DB Error") {
        throw new DBException(
            $msg . " " .
            $this->pdo->getCode()." ".
            $this->pdo->getInfo()." ".
            $this->pdo->errorInfo()[1]." ".
            $this->pdo->errorInfo()[2]
        );
    }

    /**
     * Constructor that connects connects to a MySQL engine
     * using application constants DBHOST, DBUSER, and DBPW
     * (which are defined in inc/config.php).
     *
     * If the database doesn't exist, it is created and
     * initialised it using an
    application function with spec
            Initialize_myDatabase($DB,$EZ)
    where $DB refers to the current connection object, and $EZ refers to
    this instance of DR_easy.
    */
    public function __construct() {

        // CONNECT TO THE DATABASE SERVER
        $dsn = "mysql:" . DBHOST . ";charset=UTF-8";
        $option = array(PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION);
        // Manana: maybe also PDO::ATTR_PERSISTENT=>TRUE ??
        try {
            $this->pdo = new PDO($dsn, DBUSER, DBPW, $option);
        } catch (PDOException $failure) {
            DB::throwException("Connect failed during construct");
        }

        // DOES THE DATABASE EXIST YET?

        if (DB::dbExists(DBNAME)) {
            // THE DATABASE EXISTS: SO MERELY SELECT IT.
            $this->pdo->exec("USE " . DBNAME);
        } else {
            // ****** ??? wrap the following in a Transaction? ??? ******
            // THE DATABASE DOESN'T YET EXIST, SO CREATE IT...
            DB::Query("CREATE DATABASE " . DBNAME, __file__, __line__);

            // ... SELECT IT ...
            $this->pdo->exec("USE " . DBNAME);

            // ... AND DO WHATEVER CREATES ETC THE APPLICATION NEEDS.
            DB::Query(DBINIT);
        }

    }

    // Close() closes the connection established by the above constructor.
    public function Close() {
        $this->pdo = null;
    }

    /**
     * @param $query The query to be executed.
     * @param $bindings The data values to bind if the query is to be run as a prepared statement.
     * @return array of associative arrays where each array represents a result row
     * @throws DBException if the query fails for any technical reason
     */
    function query($query, $bindings = NULL) {

        try {
            if (isset($bindings)) {
                $result =$this->pdo->prepare($query);
                $result->execute($bindings);
            } else {
                $result =$this->pdo->query($query);
            }

            return $result->fetchAll();

        } catch (PDOException $e) {
            echo "DB Error: " . $e->getMessage();
            echo "DB Error: " . $query;
            print_r($bindings);
        } catch (Exception $e) {
            echo "Error: " . $e->getMessage();
        }

    }


}

?>
