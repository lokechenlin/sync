<?php
set_time_limit(0);

require('config.php');

if (empty($config)) {
    echo 'No configuration found!';
    exit;
}

foreach ($config as $eachSync) {
    if (empty($eachSync['master']) || !file_exists($eachSync['master'])) {
        echo 'Master path not set or not exist!';
        continue;
    }

    if (empty($eachSync['slave'])) {
        echo 'Slave not set!';
        continue;
    }

    $syncMTime = isset($eachSync['sync_mtime'])? $eachSync['sync_mtime'] : true;

    $st = microtime(true);
    $sync = new Sync($eachSync['master'], $eachSync['slave'], $syncMTime);
    $et = microtime(true);
    $tt = $et - $st;
    echo "Sync for ". $eachSync['master'] . " is done. (" . $tt . "s)\r\n";
}

class Sync 
{
    public function __construct($master, $slave, $syncMTime) 
    {
        if ($this->validateSlaveDrive($slave) === false) {
            echo 'Backup Tag not found!';
            exit;
        }

        $this->cleanUpTargetFolder($master, $slave, $syncMTime);
        $this->copyToTargetFolder($master, $slave);
    }

    private function validateSlaveDrive($slave)
    {
        $path = $slave{0} . ':/backup.txt';
        if (file_exists($path)) {
            return true;
        }
        return false;
    }

    private function cleanUpTargetFolder($master, $slave, $syncMTime)
    {
        if ($handle = opendir($slave)) {
     
            while (false !== ($entry = readdir($handle))) {
                if ($entry === '.' || $entry === '..') {
                    continue;
                }
    
                $masterEntry =  $master. DIRECTORY_SEPARATOR . $entry;
                $slaveEntry = $slave. DIRECTORY_SEPARATOR . $entry;
    
                if (is_dir($slaveEntry)) {
                    $this->cleanUpTargetFolder($masterEntry, $slaveEntry, $syncMTime);
                    $this->removeFolder($slaveEntry);
                    continue;
                }
    
                if (file_exists($masterEntry) === false) {

                    $this->removeFile($slaveEntry);
                } else {
                    if ($syncMTime && filemtime($masterEntry) != filemtime($slaveEntry)) {
                       
                        $this->removeFile($slaveEntry);
                    }
                }
            }
        
            closedir($handle);
        }
    }
    
    private function copyToTargetFolder($master, $slave)
    {
        if ($handle = opendir($master)) {
     
            while (false !== ($entry = readdir($handle))) {
                if ($entry === '.' || $entry === '..') {
                    continue;
                }
    
                $masterEntry =  $master. DIRECTORY_SEPARATOR . $entry;
                $slaveEntry = $slave. DIRECTORY_SEPARATOR . $entry;
    
                if (is_dir($masterEntry)) {
                    $this->createFolder($slaveEntry);
                    $this->copyToTargetFolder($masterEntry, $slaveEntry);
                    continue;
                }
    
                if (file_exists($slaveEntry)) {
                    continue;
                } 

                $this->copyFile($masterEntry, $slaveEntry);
            }
        
            closedir($handle);
        }
    }

    private function createFolder($target) 
    {
        if (file_exists($target) == false) {

            mkdir($target);     
        }
    }

    private function removeFolder($target)
    {
        $dir = scandir($target); 
        if (count($dir) <= 2 && file_exists($target)) {

            rmdir($target);     
        }
    }

    private function copyFile($master, $slave)
    {
        if (!copy($master, $slave)) {

            echo ("Error backuping $master \r\n");
        } else {

            touch($slave, filemtime($master));
            echo ("Backup'ed $master \r\n");
        }
    }

    private function removeFile($target)
    {
        if (!unlink($target)) {

            echo ("Error deleting $target \r\n");
        } else {

            echo ("Deleted $target \r\n");
        }
    }
}
