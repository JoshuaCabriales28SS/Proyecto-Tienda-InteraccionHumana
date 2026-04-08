<?php

namespace Model;
class Producto {

    protected static $db;
    protected static $columnasDB = ['id', 'nombre', 'precio', 'imagen', 'descripcion', 'codigo', 'stock', 'categorias_id'];
    protected static $errores = [];

    public $id;
    public $nombre;
    public $precio;
    public $imagen;
    public $descripcion;
    public $codigo;
    public $stock;
    public $categorias_id;

    public static function setDB($database) {
        self::$db = $database;
    }

    public function __construct($args = []) {
        $this->id = $args['id'] ?? '';
        $this->nombre = $args['nombre'] ?? '';
        $this->precio = $args['precio'] ?? '';
        $this->imagen = $args['imagen'] ?? '';
        $this->descripcion = $args['descripcion'] ?? '';
        $this->codigo = $args['codigo'] ?? $this->generarCodigo();
        $this->stock = $args['stock'] ?? '';
        $this->categorias_id = $args['categorias_id'] ?? '';
    }

    public function generarCodigo() {
        $codigoBarras = "";
        for ($i = 0; $i < 10; $i++) {
            $codigoBarras .= strval(random_int(0, 9));
        }
        return $codigoBarras;
    }

    public static function find(int $id): ?array {
        $id = self::$db->real_escape_string($id);
        $query = "SELECT * FROM productos WHERE id = {$id} LIMIT 1";
        $resultado = self::$db->query($query);
        return $resultado ? $resultado->fetch_assoc() : null;
    }

    public static function search(array $filters = []): array {
        $condiciones = [];

        if (!empty($filters['q'])) {
            $busqueda = self::$db->real_escape_string($filters['q']);
            $like = "%{$busqueda}%";
            $condiciones[] = "(nombre LIKE '{$like}' OR descripcion LIKE '{$like}')";
        }

        if (!empty($filters['categoria'])) {
            $categoria = (int) $filters['categoria'];
            $condiciones[] = "categorias_id = {$categoria}";
        }

        $limit = isset($filters['limit']) ? (int) $filters['limit'] : 12;
        $query = "SELECT * FROM productos";
        if (!empty($condiciones)) {
            $query .= ' WHERE ' . implode(' AND ', $condiciones);
        }
        $query .= " ORDER BY id DESC LIMIT {$limit}";

        $resultado = self::$db->query($query);
        return $resultado ? $resultado->fetch_all(MYSQLI_ASSOC) : [];
    }

    public static function findByCategory(int $categoryId, array $options = []): array {
        $condiciones = ["categorias_id = " . (int) $categoryId];

        if (isset($options['min']) && $options['min'] !== null) {
            $condiciones[] = "precio >= " . (float) $options['min'];
        }
        if (isset($options['max']) && $options['max'] !== null) {
            $condiciones[] = "precio <= " . (float) $options['max'];
        }

        $orderSql = 'id DESC';
        if (isset($options['orden'])) {
            switch ($options['orden']) {
                case 'precio-asc':
                    $orderSql = 'precio ASC';
                    break;
                case 'precio-desc':
                    $orderSql = 'precio DESC';
                    break;
                case 'nombre':
                    $orderSql = 'nombre ASC';
                    break;
            }
        }

        $query = "SELECT * FROM productos WHERE " . implode(' AND ', $condiciones) . " ORDER BY {$orderSql}";
        $resultado = self::$db->query($query);
        return $resultado ? $resultado->fetch_all(MYSQLI_ASSOC) : [];
    }

    public function guardar() {
        $atributos = $this->sanitizarDatos();
        $query = "INSERT INTO productos (" . join(', ', array_keys($atributos)) . ") VALUES ('" . join("', '", array_values($atributos)) . "')";
        return self::$db->query($query);
    }

    public function actualizar(): bool {
        $atributos = $this->sanitizarDatos();
        $valores = [];
        foreach ($atributos as $key => $value) {
            $valores[] = "{$key} = '{$value}'";
        }
        $query = "UPDATE productos SET " . join(', ', $valores) . " WHERE id = " . (int) $this->id;
        return self::$db->query($query);
    }

    public static function delete(int $id): bool {
        $id = self::$db->real_escape_string($id);
        $query = "DELETE FROM productos WHERE id = {$id}";
        return self::$db->query($query);
    }

    public function atributos() {
        $atributos = [];
        foreach (self::$columnasDB as $columna) {
            if ($columna === 'id') continue;
            $atributos[$columna] = $this->$columna;
        }
        return $atributos;
    }

    public function sanitizarDatos() {
        $atributos = $this->atributos();
        $sanitizado = [];
        foreach ($atributos as $key => $value) {
            $sanitizado[$key] = self::$db->real_escape_string($value);
        }
        return $sanitizado;
    }

    public static function getErrores(): array {
        return self::$errores;
    }

    public function validar(): array {
        self::$errores = [];

        if (!$this->stock) {
            self::$errores[] = "Debes añadir una cantidad";
        }
        if (!$this->nombre) {
            self::$errores[] = "Debes añadir un nombre";
        }
        if (!$this->precio) {
            self::$errores[] = "Debes añadir un precio";
        }
        if (strlen($this->descripcion) < 50) {
            self::$errores[] = "Debes añadir una descripcion y debe tener al menos 50 caracteres";
        }
        if (!$this->categorias_id) {
            self::$errores[] = "Debes elegir una categoria";
        }

        return self::$errores;
    }
}
