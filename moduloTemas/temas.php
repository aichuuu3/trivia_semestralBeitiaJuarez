
<?php
require_once '../excepciones/validacionesTemas.php';

//Clase Tema para manejar la información de temas
class Tema {
    private $id_tema;
    private $nombre_tema;
    private $descripcion;

    public function __construct($nombre_tema = '', $descripcion = '', $id_tema = null) {
        $this->nombre_tema = $nombre_tema;
        $this->descripcion = $descripcion;
        $this->id_tema = $id_tema;
    }

    // Getters
    public function getId() {
        return $this->id_tema;
    }
    public function getNombre() {
        return $this->nombre_tema;
    }
    public function getDescripcion() {
        return $this->descripcion;
    }

    // Setters
    public function setId($id_tema) {
        $this->id_tema = $id_tema;
    }
    public function setNombre($nombre_tema) {
        $this->nombre_tema = $nombre_tema;
    }
    public function setDescripcion($descripcion) {
        $this->descripcion = $descripcion;
    }

    // método para limpiar datos usando la clase de validaciones
    public function limpiarDatos() {
        $datos = [
            'id_tema' => $this->id_tema,
            'nombre_tema' => $this->nombre_tema,
            'descripcion' => $this->descripcion
        ];
        $datosLimpios = ValidacionesTemas::limpiarDatos($datos);
        $this->id_tema = $datosLimpios['id_tema'];
        $this->nombre_tema = $datosLimpios['nombre_tema'];
        $this->descripcion = $datosLimpios['descripcion'];
    }

    // método para validar datos usando la clase de validaciones
    public function validar() {
        $datos = [
            'id_tema' => $this->id_tema,
            'nombre_tema' => $this->nombre_tema,
            'descripcion' => $this->descripcion
        ];
        $resultado = ValidacionesTemas::validarTema($datos);
        return $resultado['errores'];
    }
}
?>
