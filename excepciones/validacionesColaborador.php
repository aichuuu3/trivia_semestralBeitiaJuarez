<?php
/**
 * Clase ValidacionesColaborador
 * Centraliza todas las validaciones para colaboradores
 */
class ValidacionesColaborador {
    
    /**
     * Limpia los datos de entrada de un colaborador
     * @param array $datos - Array con los datos del colaborador
     * @return array - Array con los datos limpiados
     */
    public static function limpiarDatos($datos) {
        return [
            'email' => isset($datos['email']) ? trim(strtolower($datos['email'])) : '',
            'nombre' => isset($datos['nombre']) ? trim($datos['nombre']) : '',
            'password' => isset($datos['password']) ? trim($datos['password']) : '',
            'id' => isset($datos['id']) ? trim($datos['id']) : null
        ];
    }
    
    /**
     * Valida el email
     * @param string $email
     * @return array - Array de errores (vacío si no hay errores)
     */
    public static function validarEmail($email) {
        $errores = [];
        
        if (empty($email)) {
            $errores[] = "El email es obligatorio";
        } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $errores[] = "El email no es válido";
        } elseif (strlen($email) > 100) {
            $errores[] = "El email no puede tener más de 100 caracteres";
        }
        
        return $errores;
    }
    
    /**
     * Valida el nombre
     * @param string $nombre
     * @return array - Array de errores (vacío si no hay errores)
     */
    public static function validarNombre($nombre) {
        $errores = [];
        
        if (empty($nombre)) {
            $errores[] = "El nombre es obligatorio";
        } elseif (strlen($nombre) < 2) {
            $errores[] = "El nombre debe tener al menos 2 caracteres";
        } elseif (strlen($nombre) > 100) {
            $errores[] = "El nombre no puede tener más de 100 caracteres";
        } elseif (!preg_match("/^[a-zA-ZáéíóúÁÉÍÓÚñÑ\s]+$/", $nombre)) {
            $errores[] = "El nombre solo puede contener letras y espacios";
        }
        
        return $errores;
    }
    
    /**
     * Valida la contraseña
     * @param string $password
     * @param bool $esRequerida - Si la contraseña es obligatoria (true para crear, false para editar)
     * @return array - Array de errores (vacío si no hay errores)
     */
    public static function validarPassword($password, $esRequerida = true) {
        $errores = [];
        
        if ($esRequerida && empty($password)) {
            $errores[] = "La contraseña es obligatoria";
        } elseif (!empty($password)) {
            if (strlen($password) < 6) {
                $errores[] = "La contraseña debe tener al menos 6 caracteres";
            } elseif (strlen($password) > 255) {
                $errores[] = "La contraseña no puede tener más de 255 caracteres";
            }
            // Opcional: Validar complejidad de contraseña
            /*
            elseif (!preg_match("/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)/", $password)) {
                $errores[] = "La contraseña debe contener al menos una mayúscula, una minúscula y un número";
            }
            */
        }
        
        return $errores;
    }
    
    /**
     * Valida que el email sea único en la base de datos
     * @param string $email
     * @param object $db - Instancia de la clase DB
     * @param int|null $idActual - ID del colaborador actual (para edición)
     * @return array - Array de errores (vacío si no hay errores)
     */
    public static function validarEmailUnico($email, $db, $idActual = null) {
        $errores = [];
        
        try {
            $pdo = $db->getPdo();
            if (!$pdo) {
                $errores[] = "Error de conexión a la base de datos";
                return $errores;
            }
            
            if ($idActual) {
                $query = $pdo->prepare("SELECT COUNT(*) FROM colaboradores WHERE email = :email AND id != :id");
                $query->bindParam(":email", $email);
                $query->bindParam(":id", $idActual);
            } else {
                $query = $pdo->prepare("SELECT COUNT(*) FROM colaboradores WHERE email = :email");
                $query->bindParam(":email", $email);
            }
            
            $query->execute();
            $count = $query->fetchColumn();
            
            if ($count > 0) {
                $errores[] = "El email ya está registrado";
            }
            
        } catch (PDOException $e) {
            $errores[] = "Error al validar el email en la base de datos";
        }
        
        return $errores;
    }
    
    /**
     * Valida todos los datos del colaborador
     * @param array $datos - Array con los datos del colaborador
     * @param object $db - Instancia de la clase DB
     * @param bool $esCreacion - Si es una creación (true) o edición (false)
     * @return array - Array de errores (vacío si no hay errores)
     */
    public static function validarColaborador($datos, $db, $esCreacion = true) {
        $errores = [];
        
        // Limpiar datos
        $datosLimpios = self::limpiarDatos($datos);
        
        // Validar email
        $errores = array_merge($errores, self::validarEmail($datosLimpios['email']));
        
        // Validar nombre
        $errores = array_merge($errores, self::validarNombre($datosLimpios['nombre']));
        
        // Validar contraseña
        $passwordRequerida = $esCreacion || !empty($datosLimpios['password']);
        $errores = array_merge($errores, self::validarPassword($datosLimpios['password'], $passwordRequerida));
        
        // Validar email único (solo si no hay errores en el formato del email)
        if (empty($errores) || !in_array("El email es obligatorio", $errores) && !in_array("El email no es válido", $errores)) {
            $errores = array_merge($errores, self::validarEmailUnico($datosLimpios['email'], $db, $datosLimpios['id']));
        }
        
        return [
            'errores' => $errores,
            'datos' => $datosLimpios
        ];
    }
    
    /**
     * Valida solo los datos necesarios para operaciones específicas
     * @param string $operacion - Tipo de operación (crear, actualizar, eliminar, obtener)
     * @param array $datos - Datos a validar
     * @return array - Array de errores
     */
    public static function validarOperacion($operacion, $datos) {
        $errores = [];
        
        switch ($operacion) {
            case 'eliminar':
            case 'obtener':
                if (empty($datos['id']) || !is_numeric($datos['id'])) {
                    $errores[] = "ID de colaborador requerido y debe ser numérico";
                }
                break;
                
            case 'crear':
            case 'actualizar':
                // Estas operaciones usan validarColaborador completo
                break;
                
            default:
                $errores[] = "Operación no válida";
                break;
        }
        
        return $errores;
    }
}
?>