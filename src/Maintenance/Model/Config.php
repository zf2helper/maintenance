<?php

namespace Maintenance\Model;

use Maintenance\Model\Storage\SQLite as SQLiteStorage,
    Maintenance\Model\Storage\Json as JsonStorage;

class Config
{
    protected $initConfig;
    protected $adapter = null;

    public function __construct($config)
    {
        $this->initConfig = $config;
        if ($config['storage']['path']) {
            $dbFileName = $config['storage']['path'] . '/' . $config['storage']['filename'];
        } else {
            $dbFileName = $config['storage']['filename'];
        }

        switch ($config['storage']['type']) {
            case 'sqlite3':
                $this->adapter = new SQLiteStorage($dbFileName);
                break;
            case 'json':
                $this->adapter = new JsonStorage($dbFileName);
                break;
            default:
                throw new Exception('Unknow adapter type');
        }
    }

    /**
     * Get maintenance config with options from top-priority storage
     * 
     * @return array()
     */
    public function getConfig()
    {
        if (is_null($this->adapter)) {
            return $this->initConfig;
        }

        $fullConfig = array_merge($this->initConfig, $this->adapter->load());

        return $fullConfig;
    }

    /**
     * Set configuration to save changed options
     * 
     * @param array() $changedConfig
     * @return \Maintenance\Model\Config
     */
    public function saveConfig($changedConfig)
    {   
        $changesToSave = $this->array_diff_assoc_recursive($this->initConfig, $changedConfig);
        
        $this->adapter->save($changesToSave);
        
        return $this;
    }
    
    /**
     * Set configuration to save changed options
     * 
     * @param array() $changedConfig
     * @return \Maintenance\Model\Config
     */
    public function saveChanges($changesToSave)
    {   
        $this->adapter->save($changesToSave);
        
        return $this;
    }
    
    /**
     * Clean Up top-level congif params
     * 
     * @return \Maintenance\Model\Config
     */
    public function clear()
    {
        $this->adapter->clear();
        
        return $this;
    }

    /**
     * Drop file or database with top-level congif params
     * 
     * @return \Maintenance\Model\Config
     */
    public function remove()
    {
        if (!is_null($this->adapter)) {
            if ($this->initConfig['storage']['path']) {
                $dbFileName = $this->initConfig['storage']['path'] . '/' . $this->initConfig['storage']['filename'];
            } else {
                $dbFileName = $this->initConfig['storage']['filename'];
            }
            $this->adapter->close();
            unlink($dbFileName);
            $this->adapter = null;
        }
        return $this;
    }
    
    public function toForm()
    {
        $formElements = array(
            'enable',
            'http_code',
            'message',
            'layout',
            'allowed_route',
        );
        
        $fullConfig = $this->getConfig();
        
        $formData = array();
        foreach($formElements as $formElement){
            switch($formElement){
                case 'enable':
                    $formData[$formElement] = ($fullConfig[$formElement]) ? 1 : 0;
                    break;
                case 'http_code':
                    $formData[$formElement] = (int) $fullConfig[$formElement];
                    break;
                case 'layout':
                case 'message':
                case 'allowed_route':
                    $formData[$formElement] = $fullConfig[$formElement];
                    break;
            }
        }

        return $formData;
    }

    /**
     * @param array $array1
     * @param array $array2
     * @return array
     */
    protected function array_diff_assoc_recursive($array1, $array2)
    {
        $difference = array();
        foreach ($array1 as $key => $value) {
            if (is_array($value)) {
                if (!isset($array2[$key]) || !is_array($array2[$key])) {
                    $difference[$key] = $value;
                } else {
                    $new_diff = $this->array_diff_assoc_recursive($value, $array2[$key]);
                    if (!empty($new_diff))
                        $difference[$key] = $new_diff;
                }
            } else if (!array_key_exists($key, $array2) || $array2[$key] !== $value) {
                $difference[$key] = $value;
            }
        }
        return $difference;
    }

}
