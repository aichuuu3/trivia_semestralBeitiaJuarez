<?php
class ValidacionesTemas {
    public static function limpiarDatos($datos) {
        $datosLimpios = [];
        $datosLimpios['id_tema'] = isset($datos['id_tema']) ? filter_var($datos['id_tema'], FILTER_SANITIZE_NUMBER_INT) : null;
        $datosLimpios['nombre_tema'] = isset($datos['nombre_tema']) ? htmlspecialchars(trim($datos['nombre_tema'])) : '';
        $datosLimpios['descripcion'] = isset($datos['descripcion']) ? htmlspecialchars(trim($datos['descripcion'])) : '';
        return $datosLimpios;
    }

    public static function validarTema($datos) {
        $errores = [];
        $datosLimpios = self::limpiarDatos($datos);
        if (empty($datosLimpios['nombre_tema'])) {
            $errores[] = 'El nombre del tema es obligatorio.';
        }
        if (empty($datosLimpios['descripcion'])) {
            $errores[] = 'La descripción es obligatoria.';
        }
        return ['datos' => $datosLimpios, 'errores' => $errores];
    }
}
?>
