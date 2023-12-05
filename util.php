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
        file_put_contents("./hosts.txt", $str);
    }

    function unbound_change() {
        $unbound_file = fopen("./unbound", 'w');
        $file = file_get_contents("./hosts.txt");
        $rows = explode("\n", $file);
        $writeStr = "server:\n\n";
        foreach (file("./hosts.txt") as $line) {
            if (str_starts_with($line, "127.0.0.1")) {
                $domain = $line->str_split(" ");
                $newLine = "local-zone: \"$domain[1]\" redirect\nlocal-data: \"$domain[1]. A 0.0.0.0\n";
                $writeStr .= $newLine;
            }
        }
        fwrite($unbound_file, $writeStr);
        fclose($unbound_file);
    }
}
