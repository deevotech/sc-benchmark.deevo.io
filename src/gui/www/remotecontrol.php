<?php
    header("Content-Type: text/event-stream; charset=utf-8");
    header("Cache-Control: no-store");
    header("Access-Control-Allow-Origin: *");

    try {
    $user = "datlv";
    $pwd  = "tango12@";
    $path = "/opt/gopath/src/github.com/hyperledger/caliper/";
    $host = "127.0.0.1";
    $port = 2222;
    $debug = false;
    $hostname = "http://insight.localhost/";
    set_time_limit( 0);

    session_start();
    $_SESSION['started'] = true;
    session_write_close();

    $connect = ssh2_connect($host, $port);
    //ssh2_auth_password($connect, 'datlv', 'tango12@');
    $hasReport = false;

    /*$lastEventId = floatval(isset($_SERVER["HTTP_LAST_EVENT_ID"]) ? $_SERVER["HTTP_LAST_EVENT_ID"] : 0);
    if ($lastEventId == 0) {
        $lastEventId = floatval(isset($_GET["lastEventId"]) ? $_GET["lastEventId"] : 0);
    }*/

    function sendmsg($name, $data) {
        /*global $lastEventId;
        ++$lastEventId;*/
        $data = array('type'=>$name, 'data'=>$data);
        echo "data:" . json_encode($data, JSON_UNESCAPED_SLASHES) . "\n\n";
        @ob_flush();
        @flush();
    }

    if(ssh2_auth_password($connect, $user, $pwd)){
        if($debug) {
            sendmsg('debug', 'ssh connected, try to run: bash '.$path.'start.sh ' . $_GET['b'] . ' ' . $_GET['s']);
        }

        // start the benchmark
        $stream = ssh2_exec($connect, 'bash '.$path.'start.sh ' . $_GET['b'] . ' ' . $_GET['s']);
        stream_set_blocking($stream, true);
        // fetch the log file to get running result
        while($stream) {
            @session_start();
            if($_SESSION['started'] == false) {
                sendmsg("finish", "stopped");
                exit();
            }
            session_write_close();

            sleep(1);
            $out = ssh2_exec($connect, 'cat '.$path.'output.log');
            stream_set_blocking($out, true);
            $result = stream_get_contents($out);
            sendmsg("log", $result);
            fclose($out);

            $demo = ssh2_exec($connect, 'cat /opt/gopath/src/github.com/hyperledger/caliper/src/gui/output/demo.json');
            stream_set_blocking($demo, true);
            $demoResult = stream_get_contents($demo);
            sendmsg("metrics", $demoResult);
            if(!$hasReport) {
                $json = json_decode($demoResult);
                $report = $json->report;
                if(strpos($report, '.html') !== false ) {
                    $html = ssh2_exec($connect, 'cat '. $report);
                    stream_set_blocking($html, true);
                    $htmlData = stream_get_contents($html);
                    $htmlData = preg_replace('/<div id="round 0">(.*)provided<\/pre>/', "", $htmlData);
                    $htmlData = str_replace('<div id="round 0">provided</pre>', "", $htmlData);
                    $htmlFile = fopen("/opt/gopath/src/github.com/hyperledger/caliper/src/gui/www/report.html", "w");
                    fwrite($htmlFile, $htmlData);
                    fclose($htmlFile);
                    fclose($html);
                    $htmlData1 = file_get_contents($hostname . 'report.html');
                    $htmlData1 = preg_replace('#\<div id="round 0"\>(.+?)provided\<\/pre\>#s', "", $htmlData1);
                    $htmlFile1 = fopen("/opt/gopath/src/github.com/hyperledger/caliper/src/gui/www/report.html", "w");
                    fwrite($htmlFile1, $htmlData1);
                    fclose($htmlFile1);
                    $hasReport = true;
                    sendmsg("report", "ok");
                }
            }
            fclose($demo);

            if(strpos($result, '# fail  ') !== false || strpos($result, '# ok') !== false) {
                sleep(2);
                break;
            }
        }

        stream_get_contents($stream);
        fclose($stream);

        sendmsg('finish','ok');
        exit();
    }
    else {
        sendmsg('finish', 'Error: could not connect to ssh server');
        exit();
    }
    }
    catch(Exception $e) {
        sendmsg('finish', 'Error: '.$e->getMessage());
        exit();
    }

?>