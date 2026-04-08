<?php

namespace Model;

class Carrito {
    protected static $db;

    public static function setDB($database){
        self::$db = $database;
    }

    public static function getItems(): array{
        $query = "SELECT carrito.id, carrito.cantidad, productos.id AS producto_id, productos.nombre, productos.precio, productos.imagen
                  FROM carrito
                  JOIN productos ON carrito.productos_id = productos.id";
        $resultado = self::$db->query($query);
        return $resultado ? $resultado->fetch_all(MYSQLI_ASSOC) : [];
    }

    public static function getIds(): array{
        $query = "SELECT productos_id FROM carrito";
        $resultado = self::$db->query($query);
        $ids = [];
        if($resultado){
            while($item = $resultado->fetch_assoc()){
                $ids[] = (int) $item['productos_id'];
            }
        }
        return $ids;
    }

    public static function findItem(int $productoId): ?array{
        $productoId = self::$db->real_escape_string($productoId);
        $query = "SELECT id, cantidad FROM carrito WHERE productos_id = {$productoId} LIMIT 1";
        $resultado = self::$db->query($query);
        return $resultado ? $resultado->fetch_assoc() : null;
    }

    public static function create(int $productoId, int $cantidad): bool{
        $productoId = self::$db->real_escape_string($productoId);
        $cantidad = self::$db->real_escape_string($cantidad);
        $query = "INSERT INTO carrito (productos_id, cantidad) VALUES ('{$productoId}', '{$cantidad}')";
        return self::$db->query($query);
    }

    public static function updateQuantityByProductoId(int $productoId, int $cantidad): bool{
        $productoId = self::$db->real_escape_string($productoId);
        $cantidad = self::$db->real_escape_string($cantidad);
        $query = "UPDATE carrito SET cantidad = '{$cantidad}' WHERE productos_id = '{$productoId}'";
        return self::$db->query($query);
    }

    public static function deleteByProductoId(int $productoId): bool{
        $productoId = self::$db->real_escape_string($productoId);
        $query = "DELETE FROM carrito WHERE productos_id = '{$productoId}'";
        return self::$db->query($query);
    }

    public static function deleteByProductoIds(array $ids): bool{
        if(empty($ids)){
            return false;
        }

        $sanitized = array_map([self::$db, 'real_escape_string'], $ids);
        $idsList = implode(',', $sanitized);
        $query = "DELETE FROM carrito WHERE productos_id IN ({$idsList})";
        return self::$db->query($query);
    }

    public static function clearAll(): bool{
        $query = "DELETE FROM carrito";
        return self::$db->query($query);
    }

    public static function getItemsByProductoIds(array $ids): array{
        $sanitized = array_map([self::$db, 'real_escape_string'], $ids);
        if(empty($sanitized)){
            return [];
        }
        $idsList = implode(',', $sanitized);
        $query = "SELECT carrito.id, carrito.cantidad, productos.id AS producto_id, productos.nombre, productos.precio, productos.imagen
                  FROM carrito
                  JOIN productos ON carrito.productos_id = productos.id
                  WHERE carrito.productos_id IN ({$idsList})";
        $resultado = self::$db->query($query);
        return $resultado ? $resultado->fetch_all(MYSQLI_ASSOC) : [];
    }
}
