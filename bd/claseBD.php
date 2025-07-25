        
<?php
include_once 'conexion.php';
//Clase DB para manejar operaciones de base de datos
    class DB {
        // --- MÉTODOS DE TOTALES PARA DASHBOARD ---
        public function totalUsuarios() {
            try {
                $query = $this->pdo->query('SELECT COUNT(*) FROM usuarios');
                return $query->fetchColumn();
            } catch (PDOException $e) {
                return 0;
            }
        }
        public function totalTema() {
            try {
                $query = $this->pdo->query('SELECT COUNT(*) FROM temas');
                return $query->fetchColumn();
            } catch (PDOException $e) {
                return 0;
            }
        }
        public function totalPreguntas() {
            try {
                $query = $this->pdo->query('SELECT COUNT(*) FROM preguntas');
                return $query->fetchColumn();
            } catch (PDOException $e) {
                return 0;
            }
        }
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

        // MÉTODOS PARA USUARIOS
        //Método para insertar usuario
        public function insertarUsuario($email, $nombre, $password) {
            try {
                $fecha_registro = date('Y-m-d');
                $hora_inicio_actividad = date('H:i:s');
                $passwordHash = password_hash($password, PASSWORD_DEFAULT);
                $query = $this->pdo->prepare("INSERT INTO usuarios (email, nombre, password, fecha_registro, hora_inicio_actividad, activo) VALUES (:email, :nombre, :password, :fecha_registro, :hora_inicio, 1)");
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
        
        //Método para actualizar usuario
        public function actualizarUsuario($id, $email, $nombre, $password = null) {
            try {
                if ($password !== null && $password !== '') {
                    $passwordHash = password_hash($password, PASSWORD_DEFAULT);
                    $query = $this->pdo->prepare("UPDATE usuarios SET email = :email, nombre = :nombre, password = :password WHERE id = :id");
                    $query->bindParam(":password", $passwordHash);
                } else {
                    $query = $this->pdo->prepare("UPDATE usuarios SET email = :email, nombre = :nombre WHERE id = :id");
                }
                $query->bindParam(":email", $email);
                $query->bindParam(":nombre", $nombre);
                $query->bindParam(":id", $id);
                return $query->execute();
            } catch (PDOException $e) {
                return false;
            }
        }

        //Método para obtener usuario por ID
        public function obtenerUsuario($id) {
            try {
                $query = $this->pdo->prepare("SELECT * FROM usuarios WHERE id = :id");
                $query->bindParam(":id", $id);
                $query->execute();
                return $query->fetch(PDO::FETCH_ASSOC);
            } catch (PDOException $e) {
                return false;
            }
        }

        //Método para eliminar usuario (desactivar)
        public function eliminarUsuario($id) {
            try {
                $query = $this->pdo->prepare("UPDATE usuarios SET activo = 0 WHERE id = :id");
                $query->bindParam(":id", $id);
                return $query->execute();
            } catch (PDOException $e) {
                return false;
            }
        }

        //Método para listar usuarios con búsqueda opcional
        public function listarUsuarios($busqueda = "") {
            try {
                if ($busqueda != "") {
                    $query = $this->pdo->prepare("SELECT * FROM usuarios WHERE activo = 1 AND (id LIKE :busqueda OR nombre LIKE :busqueda OR email LIKE :busqueda) ORDER BY fecha_registro DESC");
                    $busquedaParam = '%' . $busqueda . '%';
                    $query->bindParam(":busqueda", $busquedaParam);
                } else {
                    $query = $this->pdo->prepare("SELECT * FROM usuarios WHERE activo = 1 ORDER BY fecha_registro DESC");
                }
                $query->execute();
                return $query->fetchAll(PDO::FETCH_ASSOC);
            } catch (PDOException $e) {
                return [];
            }
        }

        //Método para validar login de usuario
        public function validarUsuario($email, $password) {
            try {
                $query = $this->pdo->prepare("SELECT * FROM usuarios WHERE email = :email AND activo = 1");
                $query->bindParam(":email", $email);
                $query->execute();
                $usuario = $query->fetch(PDO::FETCH_ASSOC);
                if ($usuario && password_verify($password, $usuario['password'])) {
                    return $usuario;
                }
                return false;
            } catch (PDOException $e) {
                return false;
            }
        }

        // MÉTODOS PARA TEMAS
        // Insertar tema
        public function insertarTema($nombre_tema, $descripcion) {
            try {
                $query = $this->pdo->prepare("INSERT INTO temas (nombre_tema, descripcion) VALUES (:nombre_tema, :descripcion)");
                $query->bindParam(":nombre_tema", $nombre_tema);
                $query->bindParam(":descripcion", $descripcion);
                return $query->execute();
            } catch (PDOException $e) {
                return false;
            }
        }

        // Actualizar tema
        public function actualizarTema($id_tema, $nombre_tema, $descripcion) {
            try {
                $query = $this->pdo->prepare("UPDATE temas SET nombre_tema = :nombre_tema, descripcion = :descripcion WHERE id_tema = :id_tema");
                $query->bindParam(":nombre_tema", $nombre_tema);
                $query->bindParam(":descripcion", $descripcion);
                $query->bindParam(":id_tema", $id_tema);
                return $query->execute();
            } catch (PDOException $e) {
                return false;
            }
        }

        // Obtener tema por ID
        public function obtenerTema($id_tema) {
            try {
                $query = $this->pdo->prepare("SELECT * FROM temas WHERE id_tema = :id_tema");
                $query->bindParam(":id_tema", $id_tema);
                $query->execute();
                return $query->fetch(PDO::FETCH_ASSOC);
            } catch (PDOException $e) {
                return false;
            }
        }

        // Eliminar tema
        public function eliminarTema($id_tema) {
            try {
                $query = $this->pdo->prepare("DELETE FROM temas WHERE id_tema = :id_tema");
                $query->bindParam(":id_tema", $id_tema);
                return $query->execute();
            } catch (PDOException $e) {
                return false;
            }
        }

        // Listar todos los temas
        public function listarTemas() {
            try {
                $query = $this->pdo->prepare("SELECT * FROM temas ORDER BY id_tema DESC");
                $query->execute();
                return $query->fetchAll(PDO::FETCH_ASSOC);
            } catch (PDOException $e) {
                return [];
            }
        }

        // MÉTODOS PARA ADMINISTRADORES
        // Obtener datos de un administrador por ID
        public function obtenerAdministrador($id) {
            try {
                $query = $this->pdo->prepare("SELECT id, nombre, email, password, fecha_registro, horas_totales FROM administradores WHERE id = :id");
                $query->bindParam(":id", $id);
                $query->execute();
                return $query->fetch(PDO::FETCH_ASSOC);
            } catch (PDOException $e) {
                return false;
            }
        }

        // Actualizar nombre, email y/o password de administrador
        public function actualizarAdministrador($id, $nombre, $email, $password = null) {
            try {
                if ($password !== null && $password !== '') {
                    $passwordHash = password_hash($password, PASSWORD_DEFAULT);
                    $query = $this->pdo->prepare("UPDATE administradores SET nombre = :nombre, email = :email, password = :password WHERE id = :id");
                    $query->bindParam(":password", $passwordHash);
                } else {
                    $query = $this->pdo->prepare("UPDATE administradores SET nombre = :nombre, email = :email WHERE id = :id");
                }
                $query->bindParam(":nombre", $nombre);
                $query->bindParam(":email", $email);
                $query->bindParam(":id", $id);
                return $query->execute();
            } catch (PDOException $e) {
                return false;
            }
        }

        // Sumar segundos a horas_totales del administrador
        public function sumarHorasAdministrador($id, $segundos) {
            try {
                // Suma los segundos a horas_totales (que es INT, en segundos)
                $query = $this->pdo->prepare("UPDATE administradores SET horas_totales = horas_totales + :segundos WHERE id = :id");
                $query->bindParam(":segundos", $segundos, PDO::PARAM_INT);
                $query->bindParam(":id", $id);
                return $query->execute();
            } catch (PDOException $e) {
                return false;
            }
        }
    }

?>