<?php

namespace App\Connection;

use PDO;
use PDOException;

class Database
{
    private static $pdo;

    // Método para conectar ao banco de dados usando variáveis de ambiente
    public static function connect()
    {
        if (!self::$pdo) {
            $host = $_ENV['DB_HOST'];
            $dbName = $_ENV['DB_NAME'];
            $user = $_ENV['DB_USER'];
            $pass = $_ENV['DB_PASS'];

            try {
                self::$pdo = new PDO("mysql:host={$host};dbname={$dbName}", $user, $pass);
                self::$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
                error_log('Conexão com o banco de dados estabelecida com sucesso.');
            } catch (PDOException $e) {
                error_log('Erro ao conectar ao banco de dados: ' . $e->getMessage());
                die('Erro ao conectar ao banco de dados. Verifique os logs para mais detalhes.');
            }
        }

        return self::$pdo;
    }

    // Método para desconectar (opcional, PDO se desconecta automaticamente)
    public static function disconnect()
    {
        self::$pdo = null;
        error_log('Conexão com o banco de dados fechada.');
    }
}
