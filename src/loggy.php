<?php
 /**
  * Loggy is the class responsible from logging events and errors.
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
    private $format = array("ip", "tag", "message", "date");
    function __construct($filename = null, $separator = "|") {
         if(is_null($filename)) $filename = "log.gy";
        $this->filename = $filename;
        
        $this->separator = $separator;
        // a+ mode to open the file and place the cursor at the end. 
        // If the file doesn't exist, attempt to create it.
        $this->handle = fopen($this->filename, "a+");     
        
    }
    
    public function w($message, $tag = "Unknown") {
        $ip = $this->getClientIPAddress();  // Get the client IP
        $date = date("Y-m-d H:i:s");        // Get the date and time
        // Creating the entry. Entries consist from a single line.
        $entry = "";
        $entry .= $this->clean($ip) . $this->separator;
        $entry .= $this->clean($tag) . $this->separator;
        $entry .= $this->clean($message) . $this->separator;
        $entry .= $this->clean($date) . PHP_EOL;
        // Write down the entry.
        fwrite($this->handle, $entry);
    }
    
    private function clean($dirty) {
        // Look for the separator pattern and remove it. 
        $clean = str_replace($this->separator, "", $dirty);
        return $clean;
    }

    public function export($mode = "JSON") {
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
        while(!feof($this->handle)) {
            $line = fgets($this->handle);
            if($line == "") continue;
            $entries = explode($this->separator, $line);
            $i = 0;
            $tmp = array();
            foreach($entries as $entry) {
                // Escape double quotes and initialize array for JSON encoding                
                $tmp[$this->format[$i]] = str_replace('"', '\\"', $entry);
                $i++;
            }
            $output .= json_encode($tmp) . ", ";
        }
        $output = substr($output, 0, strlen($output)-2);
        $output .= "]}";
        fclose($this->handle);
        return $output;
    }
    
    private function exportXML(){
        // TODO: Export XML.
    }
    
    private function exportHTML(){
        // TODO: Export HTML.
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
