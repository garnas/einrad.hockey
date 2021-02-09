<?php

class dbi
{
    public static dbWrapper $db;

    /**
     * Helper
     * @param string $host
     * @param string $user
     * @param string $password
     * @param string $database
     */
    function __construct($host = Config::HOST_NAME, $user = Config::USER_NAME, $password = Config::PASSWORD, $database = Config::DATABASE)
    {
        self::$db = new dbWrapper($host, $user, $password, $database);
    }

    public static function escape(mixed $input): mixed
    {
        if (is_array($input)) {
            foreach ($input as $key => $value) { // Rekursion
                if (is_string($key)) $key = self::escape($key);
                if (is_string($value)) $value = self::escape($value);
                $output[$key] = $value;
            }
        } else {
            $output = (is_string($input)) ? htmlspecialchars($input) : $input;
        }
        return $output ?? $input;
    }

    public static function trim_params(mixed $params)
    {
        if (is_array($params)) {
            foreach ($params as $key => $param) {
                if (is_string($params)) $params[$key] = trim($param);
            }
        } else {
            if (is_string($params)) $params = trim($params);
        }
        return $params;
    }

}