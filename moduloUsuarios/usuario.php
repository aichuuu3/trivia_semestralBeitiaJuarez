<?php
require_once '../excepciones/validacionesColaborador.php';
require_once '../excepciones/validacionesUsuarios.php';

//Clase Colaborador para manejar la información de colaboradores
//Clase Usuario para manejar la información de usuarios
class Usuario {
    private $id;
    private $email;
    private $nombre;
    private $password;
    
    public function __construct($email = '', $nombre = '', $password = '', $id = null) {
        $this->email = $email;
        $this->nombre = $nombre;
        $this->password = $password;
        $this->id = $id;
    }
    
    // Getters
    public function getId() {
        return $this->id;
    }
    
    public function getEmail() {
        return $this->email;
    }
    
    public function getNombre() {
        return $this->nombre;
    }
    
    public function getPassword() {
        return $this->password;
    }
    
    // Setters
    public function setId($id) {
        $this->id = $id;
    }
    
    public function setEmail($email) {
        $this->email = $email;
    }
    
    public function setNombre($nombre) {
        $this->nombre = $nombre;
    }
    
    public function setPassword($password) {
        $this->password = $password;
    }
    
    // método para limpiar datos usando la clase de validaciones
    public function limpiarDatos() {
        $datos = [
            'email' => $this->email,
            'nombre' => $this->nombre,
            'password' => $this->password,
            'id' => $this->id
        ];
        
        $datosLimpios = ValidacionesUsuarios::limpiarDatos($datos);
        
        $this->email = $datosLimpios['email'];
        $this->nombre = $datosLimpios['nombre'];
        $this->password = $datosLimpios['password'];
        $this->id = $datosLimpios['id'];
    }
    
    // método para validar datos usando la clase de validaciones
    public function validar() {
        $datos = [
            'email' => $this->email,
            'nombre' => $this->nombre,
            'password' => $this->password,
            'id' => $this->id
        ];
        
        // determinar si es creación o edición
        $esCreacion = empty($this->id);
        
        // usar validación básica sin base de datos
        $errores = [];
        
        $passwordRequerida = $esCreacion || !empty($this->password);
        $errores = array_merge($errores, ValidacionesUsuarios::validarEmail($this->email));
        $errores = array_merge($errores, ValidacionesUsuarios::validarNombre($this->nombre));
        $errores = array_merge($errores, ValidacionesUsuarios::validarPassword($this->password, $passwordRequerida));
        
        return $errores;
    }
    
    // método para validación completa con base de datos
    public function validarCompleto($db) {
        $datos = [
            'email' => $this->email,
            'nombre' => $this->nombre,
            'password' => $this->password,
            'id' => $this->id
        ];
        
        $esCreacion = empty($this->id);
        $resultado = ValidacionesUsuarios::validarUsuario($datos, $db, $esCreacion);
        
        return $resultado['errores'];
    }
    
    // método para validar email único (mantenido por compatibilidad xd)
    public function validarEmailUnico($db, $idActual = null) {
        $errores = ValidacionesUsuarios::validarEmailUnico($this->email, $db, $idActual);
        return empty($errores);
    }
}
?>