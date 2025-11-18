<?php

namespace Model\DB;

class Connection
{

    private static $instance = null;
    private $connection;

    private function __construct()
    {
        $host = 'localhost';
        $user = 'root';
        $password = '';
        $dbName = 'login_system';

        $this->connection = new \mysqli($host, $user, $password, $dbName);

        // Comprobar si hay un error de conexión
        if ($this->connection->connect_error) {
            die("Error de conexión: " . $this->connection->connect_error);
        }

        // Establecer la codificación de caracteres
        $this->connection->set_charset("utf8mb4");
    }

    // Patrón Singleton para obtener una única instancia de la conexión
    public static function getInstance()
    {
        if (self::$instance === null) {
            self::$instance = new Connection();
        }
        return self::$instance;
    }

    public function getConnection()
    {
        return $this->connection;
    }

    public function closeConnection()
    {
        if ($this->connection) {
            $this->connection->close();
            self::$instance = null;
        }
    }

    // Método para obtener errores de la base de datos
    public function getError()
    {
        return $this->connection->error;
    }

    // Método para comprobar el estado de la conexión
    public function isConnected()
    {
        return $this->connection !== null && !$this->connection->connect_error;
    }

    // Método para probar la conexión a la base de datos
    public static function testConnection()
    {
        try {
            $instance = self::getInstance();
            return $instance->isConnected();
        } catch (\Exception $e) {
            return false;
        }
    }
}
