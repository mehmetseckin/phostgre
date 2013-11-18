<?php

/**
 * Engine is the base class that provides a database connection, and basic 
 * query execution abilities.
 *
 * @author: Mehmet Seckin
 * @email : seckin92@gmail.com
 * @type  : Library
 */
class Engine {

    protected $queryString;
    protected $hasErrors;
    protected $errorMessage;
    protected $errorDetails;
    private $connection;
    public $lastQuery;

    function __construct() {
        // Connect to the database.
        $this->connect();
    }

    function __destruct() {
        // Close the connection.
        $this->disconnect();
    }

    /**
     * Connects to a PgSQL database and initializes the connection variable.
     */
    protected function connect() {
        $this->connection = @pg_connect(
                        "host=" . PgSQL_HOSTNAME
                        . " port=" . PgSQL_PORT
                        . " dbname=" . PgSQL_DATABASE
                        . " user=" . PgSQL_USERNAME
                        . " password=" . PgSQL_PASSWORD
                        . " options='--client_encoding=utf-8'"
                        . " connect_timeout=10");

        if (!$this->connection) {    // Connection failed, set the error message.
            $this->hasErrors = true;
            $this->errorMessage = "Failed establishing a database connection.";
        }
    }
    
    /**
     * Complains about the last error. For debugging purposes...
     */
    function complain() {
        if($this->hasErrors)
            return "<p class=\"error-message\">$this->errorMessage</p>";
        else
            return "<p class=\"error-message\">Everything's okay!</p>";
    }
    /**
     * Returns the current connection status.
     */
    function status() {
        $status = "Unknown";
        if(pg_connection_status($this->connection) === PGSQL_CONNECTION_OK) {
            $status = "OK";
            if(pg_connection_busy($this->connection)) {
                $status = "BUSY";
            }
        } else if(pg_connection_status($this->connection) === PGSQL_CONNECTION_BAD) {
            $status = "BAD";
        } else {
            $status = "DISCONNECTED";
        }
        return $status;
    }
    
    /**
     * Closes the existing connection, and frees the connection variable.
     */
    protected function disconnect() {
        if($this->connection)
            pg_close($this->connection);
        unset($this->connection);
    }

    /**
     * Set the query string to execute.
     * @param string $sql   The query string which will be executed.
     */
    function setQuery($sql = '') {
        $this->queryString = $sql;
    }
    
    /**
     * Sets the query string to call a specified PgSQL function.
     * 
     * @param string $function The name and parameters of the function to call.
     */
    function f($function) {
        $this->queryString = "select * from $function";
    }
    
    /**
     * Execute the SQL query stored in the queryString variable, and return the 
     * raw results.
     * @return resource Returns the query execution results. 
     */
    private function execute() {
        $result = pg_query($this->connection, $this->queryString);
        return $this->validate($result);
    }

    /**
     * Executes the current query, and returns a single row.
     */
    function loadSingle() {
        $result = $this->execute();
       $data = null;
       while($row = pg_fetch_object($result)) {
            $data = $row;
        }
        pg_free_result($result);
        return $data;
    }
    
    /**
     * Executes the current query, and returns multiple rows as a 2D array.
     */
    function loadMultiple() {
        $result = $this->execute();
        $data = array();
        while($row = pg_fetch_assoc($result)) {
            $data[] = $row;
        }
        return $data;
    }
    
    /**
     * If the query couldn't be executed properly, or produced an error,
     * sets the error flag and the error message. Else, returns back the raw
     * results.
     * @param resource $result The raw results coming from execute()
     * @return resource Validated results.
     */
    private function validate($result) {
        if (!$result) {
            $this->hasErrors = true;
            $this->errorMessage = pg_last_error();
            return null;
        }
        $this->lastQuery = $this->queryString;
        $this->queryString = "";
        return $result;
    }

}

?>
