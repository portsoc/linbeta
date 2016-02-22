<?php

/**
 * Class DBException
 */
class DBException extends Exception
{
    // stub
}

/*
Re-usable database utilities in PDO
(c) C Lester 2013 (mysqli), 2014 (PDO)
*/

class DB
{
    private $pdo;
    private $dsn;

    /**
     * @param msg $
     * @throws DBException
     */
    private function throwException($msg = "Unknown DB Error")
    {
        if ($this->pdo) {
            $msg .= $this->pdo->errorInfo()[1];
            $msg .= " ";
            $msg .= $this->pdo->errorInfo()[2];
        }

        if ($this->dsn) {
            $msg .= "DSN: ".$this->dsn;
        }

        throw new DBException(
            $msg
        );
    }

    /**
     * Constructor that connects connects to a MySQL engine
     * using application constants DBHOST, DBUSER, and DBPW
     * (which are defined in inc/config.php).
     *
     * If the database doesn't exist, it is created and
     * initialised it using an
     * application function with spec
     * Initialize_myDatabase($DB,$EZ)
     * where $DB refers to the current connection object, and $EZ refers to
     * this instance of DR_easy.
     */
    public function __construct()
    {
        // CONNECT TO THE DATABASE SERVER
        $this->dsn = "mysql:host=" . DBHOST.";";

        // mysql:host=localhost;dbname=testdb
        // mysql:host=localhost;port=3307;dbname=testdb
        // mysql:unix_socket=/tmp/mysql.sock;dbname=testdb


        $option = array(
        	PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        	PDO::ATTR_PERSISTENT => true
        );

        try {
            $this->pdo = new PDO($this->dsn, DBUSER, DBPW, $option);
        } catch (PDOException $failure) {
             DB::throwException("Connect failed during construct");
        }

        try {
            $this->pdo->query("use ".DBNAME);
        } catch (PDOException $failure) {
             // DB::throwException("Connect failed during use");
        }

    }

    // Close() closes the connection established by the above constructor.
    public function close()
    {
        $this->pdo = null;
    }

    /**
     * @param $query The query to be executed.
     * @param $bindings The data values to bind if the query is to be run as a prepared statement.
     * @return array of associative arrays where each array represents a result row
     * @throws DBException if the query fails for any technical reason
     */
    public function query($query, $bindings = null, &$debug = null)
    {

        try {
            if (isset($bindings)) {
                $result =$this->pdo->prepare($query);
                $result->execute($bindings);
            } else {
                $result =$this->pdo->query($query);
            }

            if (strpos($query, 'SELECT') !== false) {
                return $result->fetchAll(PDO::FETCH_ASSOC);
            }

            return $result->rowCount();

        } catch (Exception $e) {
        	if (isset($debug)) {
                $result["meta"]["ok"] = false;
                $result["meta"]['message'] = $e->getMessage();
                $result["meta"]['query'] = $query;
                $result["meta"]['bindings'] = $bindings;
        	}
        }

    }

    public function lastInsertId()
    {
        return $this->pdo->lastInsertId();
    }
}
