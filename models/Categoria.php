<?php

namespace Model;

class Categoria {
    protected static $db;

    public static function setDB($database){
        self::$db = $database;
    }

    public static function all(): array{
        $query = "SELECT * FROM categorias ORDER BY nombre";
        $resultado = self::$db->query($query);
        return $resultado ? $resultado->fetch_all(MYSQLI_ASSOC) : [];
    }

    public static function find(int $id): ?array{
        $id = self::$db->real_escape_string($id);
        $query = "SELECT * FROM categorias WHERE id = {$id} LIMIT 1";
        $resultado = self::$db->query($query);
        return $resultado ? $resultado->fetch_assoc() : null;
    }
}
