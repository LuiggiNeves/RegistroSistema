<?php

namespace App\Connection;

use PDO;
use PDOException;

class Database {
    private static $pdo;

    // Método para conectar ao banco de dados central
    public static function connectCentralDB() {
        if (!self::$pdo) {
            $dbName = $_ENV['DB_NAME'];  // Nome do banco de dados central
            $host = $_ENV['DB_HOST'];
            $user = $_ENV['DB_USER'];
            $pass = $_ENV['DB_PASS'];

            try {
                self::$pdo = new PDO("mysql:host={$host};dbname={$dbName}", $user, $pass);
                self::$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

            } catch (PDOException $e) {

            }
        }

        return self::$pdo;
    }

    // Método para conectar ao banco de dados específico do cliente usando credenciais dinâmicas
    public static function connectToClientDB($databaseName, $dbUser, $dbPassword) {
        self::$pdo = null; // Desconectar do banco central para evitar conflito de conexão
        if (!self::$pdo) {
            $host = $_ENV['DB_HOST'];

            try {
                self::$pdo = new PDO("mysql:host={$host};dbname={$databaseName}", $dbUser, $dbPassword);
                self::$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

            } catch (PDOException $e) {

            }
        }

        return self::$pdo;
    }

    // Método para desconectar
    public static function disconnect() {
        self::$pdo = null;

    }
}
