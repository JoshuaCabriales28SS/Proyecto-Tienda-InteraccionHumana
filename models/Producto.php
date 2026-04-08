<?php

namespace Model;

class Producto {

    // Base de datos
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

    // conexion a la BD
    public static function setDB($database) {
        self::$db = $database;
    }

    public function __construct($args = []) {
        $this->id = $args['id'] ?? '';
        $this->nombre = $args['nombre'] ?? '';
        $this->precio = $args['precio'] ?? '';
        $this->imagen = $args['imagen'] ?? '';
        $this->descripcion = $args['descripcion'] ?? '';
        $this->codigo = $this->generarCodigo();
        $this->stock = $args['stock'] ?? '';
        $this->categorias_id = $args['categorias_id'] ?? '';
    }

    public function generarCodigo() {
        //GENERAR CODIGO DE BARRAS DEL PRODUCTO
        $codigoBarras = "";
        for ($i=0; $i < 10; $i++){
            $num = random_int(0,9);
            $codigoBarras .= strval($num);
        }
        return $codigoBarras;
    }

    public function guardar() {
        $atributos = $this->sanitizarDatos();

        $query = " INSERT INTO productos ( ";
        $query .= join(', ', array_keys($atributos));
        $query .= " ) VALUES (' ";
        $query .= join("', '", array_values($atributos));
        $query .= " ') ";

        return $resultado = self::$db->query($query);
    }

    public function atributos() {
        $atributos = [];
        foreach(self::$columnasDB as $columna) {
            if($columna === 'id') continue;
            $atributos[$columna] = $this->$columna;
        }
        return $atributos;
    }

    public function sanitizarDatos() {
        $atributos = $this->atributos();
        $sanitizado = [];

        foreach($atributos as $key => $value) {
            $sanitizado[$key] = self::$db->escape_string($value);
        }
        return $sanitizado;
    }

    // Validacion
    public static function getErrores() {
        return self::$errores;
    }

    public function validar() {
        if(!$this->stock){
            self::$errores[] = "Debes añadir una cantidad";
        }
        if(!$this->nombre){
            self::$errores[] = "Debes añadir un nombre";
        }
        if(!$this->precio){
            self::$errores[] = "Debes añadir un precio";
        }
        if(strlen($this->descripcion) < 50){
            self::$errores[] = "Debes añadir una descripcion y debe tener al menos 50 caracteres";
        }
        if(!$this->categorias_id){
            self::$errores[] = "Debes elegir una categoria";
        }

        // if(!$this->imagen['name'] || $this->imagen['error']){
        //     self::$errores[] = "Debes añadir una imagen";
        // }
        
        // $medida = 1000*1000;
        
        // if($this->imagen['size']>$medida){
        //     self::$errores[] = "La imagen es muy pesada";
        // }
    }
}