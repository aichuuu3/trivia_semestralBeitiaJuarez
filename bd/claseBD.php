<?php
include_once 'conexion.php';
//Clase DB para manejar operaciones de base de datos
    class DB {
        private $pdo;
        
        public function __construct() {
            global $pdo;
            $this->pdo = $pdo;
        }
        
        // Getter para PDO
        public function getPdo() {
            return $this->pdo;
        }

        // MÉTODOS PARA COLABORADORES
        //Método para insertar colaborador
        public function insertarColaborador($email, $nombre, $password) {
            try {
                $fecha_registro = date('Y-m-d');
                $hora_inicio_actividad = date('H:i:s');
                $passwordHash = password_hash($password, PASSWORD_DEFAULT);
                
                $query = $this->pdo->prepare("INSERT INTO colaboradores (email, nombre, password, fecha_registro, hora_inicio_actividad, activo) VALUES (:email, :nombre, :password, :fecha_registro, :hora_inicio, 1)");
                $query->bindParam(":email", $email);
                $query->bindParam(":nombre", $nombre);
                $query->bindParam(":password", $passwordHash);
                $query->bindParam(":fecha_registro", $fecha_registro);
                $query->bindParam(":hora_inicio", $hora_inicio_actividad);
                return $query->execute();
            } catch (PDOException $e) {
                return false;
            }
        }
        
        //Método para actualizar colaborador
        public function actualizarColaborador($id, $email, $nombre, $password = null) {
            try {
                if ($password !== null && $password !== '') {
                    $passwordHash = password_hash($password, PASSWORD_DEFAULT);
                    $query = $this->pdo->prepare("UPDATE colaboradores SET email = :email, nombre = :nombre, password = :password WHERE id = :id");
                    $query->bindParam(":password", $passwordHash);
                } else {
                    $query = $this->pdo->prepare("UPDATE colaboradores SET email = :email, nombre = :nombre WHERE id = :id");
                }
                
                $query->bindParam(":email", $email);
                $query->bindParam(":nombre", $nombre);
                $query->bindParam(":id", $id);
                return $query->execute();
            } catch (PDOException $e) {
                return false;
            }
        }
        
        //Método para obtener colaborador por ID
        public function obtenerColaborador($id) {
            try {
                $query = $this->pdo->prepare("SELECT * FROM colaboradores WHERE id = :id");
                $query->bindParam(":id", $id);
                $query->execute();
                return $query->fetch(PDO::FETCH_ASSOC);
            } catch (PDOException $e) {
                return false;
            }
        }
        
        //Método para eliminar colaborador (desactivar)
        public function eliminarColaborador($id) {
            try {
                $query = $this->pdo->prepare("UPDATE colaboradores SET activo = 0 WHERE id = :id");
                $query->bindParam(":id", $id);
                return $query->execute();
            } catch (PDOException $e) {
                return false;
            }
        }
        
        //Método para listar colaboradores con búsqueda opcional
        public function listarColaboradores($busqueda = "") {
            try {
                if ($busqueda != "") {
                    $query = $this->pdo->prepare("SELECT * FROM colaboradores WHERE activo = 1 AND (id LIKE :busqueda OR nombre LIKE :busqueda OR email LIKE :busqueda) ORDER BY fecha_registro DESC");
                    $busquedaParam = '%' . $busqueda . '%';
                    $query->bindParam(":busqueda", $busquedaParam);
                } else {
                    $query = $this->pdo->prepare("SELECT * FROM colaboradores WHERE activo = 1 ORDER BY fecha_registro DESC");
                }
                $query->execute();
                return $query->fetchAll(PDO::FETCH_ASSOC);
            } catch (PDOException $e) {
                return [];
            }
        }
        
        //Método para validar login de colaborador
        public function validarColaborador($email, $password) {
            try {
                $query = $this->pdo->prepare("SELECT * FROM colaboradores WHERE email = :email AND activo = 1");
                $query->bindParam(":email", $email);
                $query->execute();
                $colaborador = $query->fetch(PDO::FETCH_ASSOC);
                
                if ($colaborador && password_verify($password, $colaborador['password'])) {
                    return $colaborador;
                }
                return false;
            } catch (PDOException $e) {
                return false;
            }
        }
    }

?>