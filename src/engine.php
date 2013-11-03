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
    function connect() {
        $this->connection = @pg_connect(
                        "host=" . PgSQL_HOST
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
     * Closes the existing connection, and frees the connection variable.
     */
    function disconnect() {
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
     * Execute the SQL query stored in the queryString variable, and return the 
     * raw results.
     * @return resource Returns the query execution results. 
     */
    function execute() {
        $result = pg_query($this->connection, $this->queryString);
        return $this->validate($result);
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
            $this->errorMessage = pg_errormessage($this->connection);
            return null;
        }
        return $result;
    }

}

?>
