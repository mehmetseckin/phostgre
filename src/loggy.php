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
  * Use "export" method, which returns you the HTML formatted 
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
                            // "|" by default.
    private $format = array("ID", "ip", "tag", "message", "date");
    function __construct($filename = null, $separator = "|") {
         if(is_null($filename)) $filename = "log.gy";
        $this->filename = $filename;
        
        $this->separator = $separator;
        // a+ mode to open the file and place the cursor at the end. 
        // If the file doesn't exist, attempt to create it.
        $this->handle = fopen($this->filename, "a+");     
        
    }
    
    public function w($message, $tag = "Unknown") {
        $id = "LGY_".  microtime(true);         // Create an unique ID for the entry
        $ip = $this->getClientIPAddress();  // Get the client IP
        $date = date("Y-m-d H:i:s");        // Get the date and time
        // Creating the entry. Entries consist from a single line.
        $entry = "";
        $entry .= $this->clean($id) . $this->separator;
        $entry .= $this->clean($ip) . $this->separator;
        $entry .= $this->clean($tag) . $this->separator;
        $entry .= $this->clean($message) . $this->separator;
        $entry .= $this->clean($date) . PHP_EOL;
        // Write down the entry.
        fwrite($this->handle, $entry);
    }
    
    private function clean($dirty) {
        // Look for the separator pattern and remove it if exists.. 
        $clean = str_replace($this->separator, "", $dirty);
        return $clean;
    }

    public function export($mode = "HTML") {
        switch($mode) {
            case "JSON" :
                return $this->exportJSON();
            case "XML" :
                return $this->exportXML();
            default :
                return $this->exportHTML();
        }
    }
    
    private function exportJSON() {
        $this->handle = fopen($this->filename, "r");
        $output = "{\"entries\" : [";
        while(!feof($this->handle) && $this->handle) {
            $line = fgets($this->handle);
            if($line == "") continue;
            $entries = explode($this->separator, $line);
            foreach($entries as &$entry) {
                // Iterate through the array and escape double quotes                
                $entry = str_replace('"', '\"', $entry);
            }
            // Combine the format array and the entries, and encode them.
            $output .= json_encode(array_combine($this->format, $entries)) . ", ";
        }
        
        // Trim the last comma and space...
        $output = substr($output, 0, strlen($output)-2);
        $output .= "]}";
        fclose($this->handle);
        return $output;
    }
    
    private function exportXML(){
        $this->handle = fopen($this->filename, "r");
        
        // Creating an XML structure 
        $output = "<?xml version=\"1.0\"?>";
        $output .="<entries>";
        while(!feof($this->handle)) {
            $line = fgets($this->handle);
            if($line == "") continue;
            $output .= "<entry>";
            $entries = explode($this->separator, $line);
            $i = 0;
            foreach($entries as $entry) {                
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
    
    private function exportHTML(){
        $this->handle = fopen($this->filename, "r");
        
        // Creating an HTML Table Structure
        $output = "<table id=\"loggy-entries\">";
        
        // Giving the headers
        $output .= "<tr>";
        foreach($this->format as  $header) {
            $output .= "<th>$header</th>";
        }
        $output .= "</th>";
        
        // Read entries and format them as HTML table rows.
        $output .= "<tr>";
        while(!feof($this->handle)) {
            $line = fgets($this->handle);
            // Nobody likes an empty row, if the line is empty, continue.
            if($line == "") continue;
            $entries = explode($this->separator, $line);
            foreach($entries as $entry) {
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
    
    public function __destruct() {
        fclose($this->handle);
    }
    
}
?>
