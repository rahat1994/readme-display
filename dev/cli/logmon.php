<?php

$logFile = __DIR__."/../test.log";
file_put_contents($logFile, '');
$fileHandle = fopen($logFile, "r");

while (true) {

    if ($fileHandle) {
        
        clearstatcache();
        
        if (($fileSize = filesize($logFile)) === 0) {
            fclose($fileHandle);
            $fileHandle = fopen($logFile, "r");
            fseek($fileHandle, 0, SEEK_END);
            sleep(1);
            continue;
        }
        
        if (fread($fileHandle, $fileSize)) {
            $title = "Error";
            $msg = 'Please check the dev/test.Log file.';
            $command = 'osascript -e \'display notification "'.$msg.'" with title "'.$title.'" sound name "funk"\'';
            shell_exec($command);
        }
    }
    
    sleep(1);
}

fclose($fileHandle);
