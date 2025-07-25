<?php
class ValidacionesUsuarios {

    // Cleans and sanitizes input data
    public static function limpiarDatos($datos) {
        $datosLimpios = [];
        $datosLimpios['id'] = isset($datos['id']) ? filter_var($datos['id'], FILTER_SANITIZE_NUMBER_INT) : null;
        $datosLimpios['nombre'] = isset($datos['nombre']) ? htmlspecialchars(trim($datos['nombre'])) : '';
        $datosLimpios['email'] = isset($datos['email']) ? filter_var(trim($datos['email']), FILTER_SANITIZE_EMAIL) : '';
        $datosLimpios['password'] = isset($datos['password']) ? trim($datos['password']) : '';
        $datosLimpios['confirm_password'] = isset($datos['confirm_password']) ? trim($datos['confirm_password']) : '';
        // IMPORTANT: For category, we're passing the ID directly now.
        $datosLimpios['cod_categoria'] = isset($datos['cod_categoria']) ? filter_var($datos['cod_categoria'], FILTER_SANITIZE_NUMBER_INT) : null;
        
        return $datosLimpios;
    }

    // Main validation method for user data
    public static function validarUsuario($datos, $db, $esCreacion = true) {
        $errores = [];
        $datosLimpios = self::limpiarDatos($datos);

        // Validate Nombre
        if (empty($datosLimpios['nombre'])) {
            $errores[] = 'El nombre es obligatorio.';
        } elseif (strlen($datosLimpios['nombre']) < 3) {
            $errores[] = 'El nombre debe tener al menos 3 caracteres.';
        }

        // Validate Email
        if (empty($datosLimpios['email'])) {
            $errores[] = 'El email es obligatorio.';
        } elseif (!filter_var($datosLimpios['email'], FILTER_VALIDATE_EMAIL)) {
            $errores[] = 'El formato del email no es válido.';
        } else {
            // Check if email already exists
            $idActual = $esCreacion ? null : $datosLimpios['id'];
            if ($db->emailUsuarioExiste($datosLimpios['email'], $idActual)) {
                $errores[] = 'El email ya está registrado.';
            }
        }

        // Validate Password
        $password = $datosLimpios['password'];
        $confirmPassword = $datosLimpios['confirm_password'];

        if ($esCreacion) { // Password is required for creation
            if (empty($password)) {
                $errores[] = 'La contraseña es obligatoria.';
            } elseif (strlen($password) < 6) {
                $errores[] = 'La contraseña debe tener al menos 6 caracteres.';
            } elseif ($password !== $confirmPassword) {
                $errores[] = 'Las contraseñas no coinciden.';
            }
        } else { // For update, password is optional, but if provided, it must meet criteria
            if (!empty($password)) {
                if (strlen($password) < 6) {
                    $errores[] = 'La nueva contraseña debe tener al menos 6 caracteres.';
                } elseif ($password !== $confirmPassword) {
                    $errores[] = 'Las contraseñas no coinciden.';
                }
            }
        }

        // Validate Category (cod_categoria)
        if (empty($datosLimpios['cod_categoria'])) {
            $errores[] = 'El nivel de conocimiento es obligatorio.';
        }
        // You could add further validation here to check if cod_categoria actually exists in the 'categoria' table,
        // but `obtenerIdCategoriaPorNombre` already handles this to some extent before reaching here.

        return ['datos' => $datosLimpios, 'errores' => $errores];
    }

    // You can keep specific validation methods if you use them elsewhere
    public static function validarEmail($email) {
        $errors = [];
        if (empty($email)) {
            $errors[] = 'El email es obligatorio.';
        } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $errors[] = 'El formato del email no es válido.';
        }
        return $errors;
    }

    public static function validarNombre($nombre) {
        $errors = [];
        if (empty($nombre)) {
            $errors[] = 'El nombre es obligatorio.';
        } elseif (strlen($nombre) < 3) {
            $errors[] = 'El nombre debe tener al menos 3 caracteres.';
        }
        return $errors;
    }

    public static function validarPassword($password, $confirm_password, $passwordRequerida = true) {
        $errors = [];
        if ($passwordRequerida && empty($password)) {
            $errors[] = 'La contraseña es obligatoria.';
        } elseif (!empty($password) && strlen($password) < 6) {
            $errors[] = 'La contraseña debe tener al menos 6 caracteres.';
        }
        if ($passwordRequerida || !empty($password)) { // Only check confirmation if password is provided or required
            if ($password !== $confirm_password) {
                $errors[] = 'Las contraseñas no coinciden.';
            }
        }
        return $errors;
    }
    
    // Validate operation (if you keep this, ensure it's robust)
    public static function validarOperacion($operacion, $postData) {
        $errores = [];
        switch ($operacion) {
            case 'crear':
                // For creation, no ID should be present, and essential fields must be there.
                if (isset($postData['id']) && !empty($postData['id'])) {
                    $errores[] = 'No se debe proporcionar un ID para la creación.';
                }
                break;
            case 'actualizar':
            case 'eliminar':
            case 'obtener':
                // For these operations, an ID is required.
                if (!isset($postData['id']) || empty($postData['id'])) {
                    $errores[] = 'Se requiere un ID para esta operación.';
                }
                break;
            default:
                $errores[] = 'Operación no reconocida.';
                break;
        }
        return $errores;
    }
}
?>