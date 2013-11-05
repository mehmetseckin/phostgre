<?php

/**
 * Loggy is a basic class responsible from logging events and errors.
 * It writes down logs in a file called log.gy, and can also read the data,
 * and return a formatted version for you.
 *
 * Usage :
 * 
 * Create an instance, using the constructor. If you want to use 
 * a different filename and/or a different separator, pass them as parameters.
 * 
 *     - $loggy = new Loggy($myLogFile, $mySeparator);
 * 
 * Anywhere in your script, use the "w" method to write a new entry.
 * 
 *     - $loggy->w("This is a dummy log message", "Written by this Tag");
 * 
 * Loggy will automatically detect the IP address and pick the date and time
 * information, and add them into your log entry.
 * 
 * 
 * Exporting Loggy Entries :
 * 
 * Use the "export" method, which returns you the HTML formatted 
 * loggy data by default.
 * Export method takes a parameter that specifies what format do you want your
 * data in.
 * 
 *     - $myHTMLLogData = $loggy->export();
 *     - $myJSONLogData = $loggy->export("JSON");
 *     - $myXMLLogData  = $loggy->export("XML");
 * 
 * 
 * @author: Mehmet Seckin
 * @email : seckin92@gmail.com
 * @type  : Library
 */
class Loggy {

    private $handle;        // The file handle for the log operations
    private $filename;      // The filename, "log.gy" by default.
    private $separator;     // The character or pattern to separate log information.
    private $format = array("ID", "ip", "tag", "message", "date");

    /**
     * Creates and initializes the object according to the given
     * settings.
     * 
     * @param string $filename A desired name for the log file. "log.gy" by default.
     * @param type $separator A desired separator pattern. "|" by default.
     */
    function __construct($filename = null, $separator = "|") {
        if (is_null($filename))
            $filename = "log.gy";
        $this->filename = $filename;

        $this->separator = $separator;

        if (file_exists($this->filename))
            if (!is_writable ($this->filename))
                die("$filename is not writable! Make sure you have the right permissions.");
        
        // Open/Create the log file.
        // a+ mode to open the file and place the cursor at the end.
        $this->handle = fopen($this->filename, "a+");
    }

    /**
     * Writes down your message and tag with additional info, into the log file.
     * 
     * @param string $message The log message to be written.
     * @param string $tag
     */
    public function w($message, $tag = "Unknown") {
        $id = "LGY_" . microtime(true);         // Create an unique ID for the entry
        $ip = $this->getClientIPAddress();  // Get the client IP
        $date = date("Y-m-d H:i:s");        // Get the date and time
        // Creating the entry. An entry is a single line, contains information..
        $entry = "";
        $entry .= $this->clean($id) . $this->separator;
        $entry .= $this->clean($ip) . $this->separator;
        $entry .= $this->clean($tag) . $this->separator;
        $entry .= $this->clean($message) . $this->separator;
        $entry .= $this->clean($date) . PHP_EOL;
        // Check if the handle is alright, if not, attempt to open the file.
        if (!$this->handle)
            $this->handle = fopen($this->filename, "a+");
        // Write down the entry.        
        fwrite($this->handle, $entry);
    }

    /**
     * Looks for the separator and removes it.
     * 
     * @param string $dirty The dirty string to be cleaned.
     * @return string
     */
    private function clean($dirty) {
        // Look for the separator pattern and remove it if exists.. 
        $clean = str_replace($this->separator, "", $dirty);
        return $clean;
    }

    /**
     * Returns the log entries in the desired format.
     * 
     * @param string $mode HTML, JSON and XML are supported. 
     * @return string
     */
    public function export($mode = "HTML") {
        switch ($mode) {
            case "JSON" :
                return $this->exportJSON();
            case "XML" :
                return $this->exportXML();
            default :
                return $this->exportHTML();
        }
    }
    /**
     * Truncates the loggy file.
     */
    public function truncate() {
        if($this->handle) fclose ($this->handle);
        // Erase all the content by opening it with writing rights.
        $this->handle = fopen($this->filename, "w");
        
    }
    
    /**
     * Reads the log entries, encodes the data according to JSON and returns
     * the output.
     * 
     * @return string 
     */
    private function exportJSON() {
        $this->handle = fopen($this->filename, "r");
        $output = "{\"entries\" : [";
        while (!feof($this->handle) && $this->handle) {
            $line = fgets($this->handle);
            if ($line == "")
                continue;
            $entries = explode($this->separator, $line);
            foreach ($entries as &$entry) {
                // Iterate through the array and escape double quotes                
                $entry = str_replace('"', '\"', $entry);
            }
            // Combine the format array and the entries, and encode them.
            $output .= json_encode(array_combine($this->format, $entries)) . ", ";
        }

        // Trim the last comma and space...
        $output = substr($output, 0, strlen($output) - 2);
        $output .= "]}";
        fclose($this->handle);
        return $output;
    }

    /**
     * Reads the log entries, formats them into a XML structure and returns the
     * HTML output.
     * 
     * @return string 
     */
    private function exportXML() {
        $this->handle = fopen($this->filename, "r");

        // Creating an XML structure 
        $output = "<?xml version=\"1.0\"?>";
        $output .="<entries>";
        while (!feof($this->handle)) {
            $line = fgets($this->handle);
            if ($line == "")
                continue;
            $output .= "<entry>";
            $entries = explode($this->separator, $line);
            $i = 0;
            foreach ($entries as $entry) {
                $key = $this->format[$i];
                $output .= "<$key>$entry</$key>";
                $i++;
            }
            $output .= "</entry>";
        }
        $output .= "</entries>";
        fclose($this->handle);
        return $output;
    }

    /**
     * Reads the log entries, formats them as a HTML table and returns the
     * HTML output.
     * 
     * @return string 
     */
    private function exportHTML() {
        $this->handle = fopen($this->filename, "r");

        // Creating an HTML Table Structure
        $output = "<table id=\"loggy-entries\">";

        // Giving the headers
        $output .= "<tr>";
        foreach ($this->format as $header) {
            $output .= "<th>$header</th>";
        }
        $output .= "</th>";

        // Read entries and format them as HTML table rows.
        $output .= "<tr>";
        while (!feof($this->handle)) {
            $line = fgets($this->handle);
            // Nobody likes an empty row, if the line is empty, continue.
            if ($line == "")
                continue;
            $entries = explode($this->separator, $line);
            foreach ($entries as $entry) {
                $output .= "<td>$entry</td>";
            }
            $output .= "</tr>";
        }
        $output .= "</table>";

        // Closing the file.
        fclose($this->handle);

        return $output;
    }

    /**
     * Gets the client IP address.
     * 
     * @return string Client's IP Address.
     */
    private function getClientIPAddress() {
        $ip;
        if (getenv("HTTP_CLIENT_IP"))
            $ip = getenv("HTTP_CLIENT_IP");
        else if (getenv("HTTP_X_FORWARDED_FOR"))
            $ip = getenv("HTTP_X_FORWARDED_FOR");
        else if (getenv("REMOTE_ADDR"))
            $ip = getenv("REMOTE_ADDR");
        else
            $ip = "UNKNOWN";
        return $ip;
    }

    /**
     * Destroys the object and closes the file handle if its still active.
     */
    public function __destruct() {
        if ($this->handle)
            fclose($this->handle);
    }

}

?>
