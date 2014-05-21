<?php

namespace Maintenance\Model\Storage;

class SQLite
{
    protected $initConfig;
    protected $db = null;
    protected $tableName = 'maintenance';
    protected $adapter;

    public function __construct($config)
    {
        $this->initConfig = $config;
        $dbFileName = $config['storage']['path'] . '/' . $config['storage']['filename'];
        switch($config['storage']['type']){
            case 'sqlite3':
            $this->db = new \SQLite3($dbFileName);
            if (!$this->checkTable() && !$this->createDb()) {
                $this->db = new SQLiteStorage($dbFileName);
            }
            break;
            case 'json':
                $this->db = new JsonStorage($dbFileName);
                break;
            default:
                throw new Exception('Unknow adapter type');
        }
    }
    
    public function __destruct()
    {
        $this->db->close();
    }

    public function get(){
        
    }
    
    public function set(){
        return $this;
    }
    
    public function getAll(){
        
    }
    
    public function setAll(){
        return $this;
    }
    
    public function getConfig(){
        if(is_null($this->db)){
           return $this->initConfig;
        }
        
        $result = $this->db->query("SELECT name, type, value FROM {$this->tableName}");
        while($row = $result->fetchArray(SQLITE3_ASSOC)){
            var_dump($row);
        }
        exit;
        return $fullConfig;
    }


    public static function clear(){
        return $this;
    }
    
    protected function createDb()
    {
        $this->db->exec("CREATE TABLE IF NOT EXISTS {$this->tableName} ( "
                . 'id INTEGER NOT NULL PRIMARY KEY AUTOINCREMENT, '
                . 'name VARCHAR(255) NOT NULL, '
                . 'type VARCHAR(255) NOT NULL, '
                . 'value VARCHAR(255) DEFAULT "")');
        
        return $this->checkTable();
    }
    
    protected function checkTable(){
        $result = $this->db->querySingle("SELECT COUNT(name) FROM sqlite_master WHERE type = 'table' AND name = '{$this->tableName}'");
        return ($result > 0) ? true : false;
    }
    
    protected function fillDemoData(){
        $allowedRoute = json_encode(array('api','admin','service'));
        $this->db->exec("INSERT INTO {$this->tableName} (id, name, type, value) VALUES (1, 'http_code', 'int', '503')");
        $this->db->exec("INSERT INTO {$this->tableName} (id, name, type, value) VALUES (2, 'message', 'string', 'No service')");
        $this->db->exec("INSERT INTO {$this->tableName} (id, name, type, value) VALUES (3, 'allowed_route', 'array', '{$allowedRoute}')");
    }
}
