<?php

namespace Config;

class Parameters{

    protected static $parameters = [
        'DB_DSN'=>'mysql:host=localhost;dbname=webpoints',
        'DB_DRIVER'  =>'pdo_mysql',
        'DB_USERNAME'=>'root',
        'DB_PASSWORD'=>'toor',
        'DB_HOSTNAME'=>'localhost',
        'DB_DATABASE'=>'webpoints',
        'ENVIRONMENT_PRODUCTION' => false,
        'APC_ENABLED' => true,
    ];


    private static $instance;

    /**
     * Returns Configured Parameter
     * @param string $parm parameter name
     * @return mixed
     */
    static function get( $parm ) {
        if (isset(self::$parameters[$parm])) {
            return self::$parameters[$parm];
        }
        return null;
    }

    /**
     * Returns Configured Parameter
     * @param string $parm parameter name
     * @return mixed
     */
    public function getValue( $parm ) {
        if (isset(self::$parameters[$parm])) {
            return self::$parameters[$parm];
        }
        return null;
    }

    /**
     * Singleton pattern, get a global an unique instance
     * Generally used with Smarty templates
     * (constructor should also be declared private, but that might be too much)
     */
    public static function getInstance() {
        if (!isset(self::$instance)) {
            self::$instance = new Parameters;
        }
        return self::$instance;
    }

    /**
     * Set parm parameter to value Parameter
     * @param string $parm parameter name
     * @param mixed $value the new value
     */
    static function setValue( $parm, $value ) {
        self::$parameters[$parm] = $value;
    }
    /**
     * Returns Configured Parameter
     * @return array current parameters
     */
    static function getParameters() {
        return self::$parameters;
    }

    /**
     * Set All Parameter
     * @param array set all parameters
     */
    static function setParameters( $parms ) {
        self::$parameters = $parms;
    }
}
