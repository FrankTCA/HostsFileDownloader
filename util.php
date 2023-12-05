<?php
class Util {
    function downloadWrapper() {
        if ($this->checkTimestamp()) {
            $this->download();
            $this->timestamp();
            $this->localhost();
        }
    }

    function download() {
        $url = "https://raw.githubusercontent.com/StevenBlack/hosts/master/hosts";
        $filename = basename($url);
        $ch = curl_init($url);

        $dir = "./";

        $save_file_loc = $dir . $filename;

        $fp = fopen($save_file_loc, 'wb');

        curl_setopt($ch, CURLOPT_FILE, $fp);
        curl_setopt($ch, CURLOPT_HEADER, 0);

        curl_exec($ch);
        curl_close($ch);

        fclose($fp);
    }

    function getCurrTimestamp() {
        $date = new DateTime();
        $ts = $date->getTimestamp();
        return $ts;
    }

    // Don't download if timestamp is newer than a day ago
    function timestamp() {
        $ts = $this->getCurrTimestamp();
        file_put_contents("./timestamp.txt", $ts);
    }

    function checkTimestamp() {
        if (!file_exists("./timestamp.txt")) {
            return true;
        }

        $ts = file_get_contents("./timestamp.txt");
        $currTs = $this->getCurrTimestamp();
        if (strtotime($ts) > strtotime("-1 day")) {
            return false;
        }
        return true;
    }

    function localhost() {
        $str = file_get_contents("./hosts");
        $str = str_replace("0.0.0.0", "127.0.0.1", $str);
        file_put_contents("./hosts", $str);
    }

    function unbound_change() {
        $unbound_file = fopen("./unbound", 'w');
        fwrite($unbound_file, "server:\n\n");
        foreach (file("./hosts") as $line) {
            if (strpos($line, "127.0.0.1") !== false) {
                $domain = str_split(" ")[1];
                $newLine = "local-zone: \"$domain\"redirect\nlocal-data: \"$domain. A 0.0.0.0\n";
                fwrite($unbound_file, $newLine);
            }
        }
        fclose($unbound_file);
    }
}
