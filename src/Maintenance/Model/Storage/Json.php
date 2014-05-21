<?php

namespace Maintenance\Model\Storage;

class Json
{
    protected $dbFile = null;

    public function __construct($fileName)
    {
        $this->dbFile = fopen($fileName, 'c+');
    }
    
    public function load(){
        if(is_null($this->dbFile)){
           return array();
        }
        
        rewind($this->dbFile);
        $fileContent = stream_get_contents($this->dbFile);
        if(!$fileContent){
           return array();
        }
        
        $config = json_decode(trim($fileContent), true);
        
        return $config;
    }
    
    public function save($fullConfig){
        if(!is_null($this->dbFile)){
           $jsonString = json_encode($fullConfig);
           $this->clear();
           fwrite($this->dbFile, $jsonString);
        }
        
        return $this;
    }
    
    public function close(){
        if(!is_null($this->dbFile)){
            fclose($this->dbFile);
        }
        $this->dbFile = null;
        
        return $this;
    }

    public function clear(){
        if(!is_null($this->dbFile)){
            ftruncate($this->dbFile, 0);
            rewind($this->dbFile);
        }
        return $this;
    }
}
