<?php
namespace es\ucm\fdi\aw\Eventos;

use es\ucm\fdi\aw\Aplicacion;
use es\ucm\fdi\aw\Formulario;

class FormularioAnyadirEvento extends Formulario {

    public function __construct() {
        parent::__construct('formAnyadirEvento', [
            'urlRedireccion' => 'admin.php', 
            'method' => 'POST', 
            'class' => 'form-Nuevo'
        ]);
    }

    protected function generaCamposFormulario(&$datos) {
        // Valores por defecto (vacíos para nuevo evento)
        $datos = [
            'nombre' => '',
            'precio' => 0,
            'descripcion' => '',
            'fecha_inicio' => '',
            'ubicacion' => '',
            'organizador' => '',
            'imagen' => ''
        ];

        $app = Aplicacion::getInstance();

        // Generar errores
        $erroresCampos = self::generaErroresCampos(
            ['nombre', 'precio', 'descripcion', 'fecha_inicio', 'ubicacion', 'organizador', 'imagen'],
            $this->errores, 
            'span', 
            ['class' => 'error']
        );
        
        $erroresGlobales = self::generaListaErroresGlobales($this->errores, 'errores-globales');
        
        $html = <<<EOF
        <div class="nuevo-evento">
            <h1>Añadir Nuevo Evento</h1>
            {$erroresGlobales}
            
            <div class="campo-formulario">
                <label for="nombre">Nombre del evento:</label>
                <input type="text" id="nombre" name="nombre" required>
                {$erroresCampos['nombre']}
            </div>

            <div class="campo-formulario">
                <label for="precio">Precio (€):</label>
                <input type="number" id="precio" name="precio" step="0.01" required>
                {$erroresCampos['precio']}
            </div>

            <div class="campo-formulario">
                <label for="descripcion">Descripción:</label>
                <textarea id="descripcion" name="descripcion"></textarea>
                {$erroresCampos['descripcion']}
            </div>

            <div class="campo-formulario">
                <label for="fecha_inicio">Fecha de inicio:</label>
                <input type="datetime-local" id="fecha_inicio" name="fecha_inicio" required>
                {$erroresCampos['fecha_inicio']}
            </div>

            <div class="campo-formulario">
                <label for="ubicacion">Ubicación:</label>
                <input type="text" id="ubicacion" name="ubicacion" required>
                {$erroresCampos['ubicacion']}
            </div>

            <div class="campo-formulario">
                <label for="organizador">Organizador:</label>
                <input type="text" id="organizador" name="organizador" required>
                {$erroresCampos['organizador']}
            </div>

            <div class="campo-formulario">
                <label for="imagen">URL de la imagen:</label>
                <input type="src" id="imagen" name="imagen">
                {$erroresCampos['imagen']}
            </div>

            <div class="acciones">
                <button type="submit" class="boton-guardar">Crear Evento</button>
                <a href="{$app->resuelve('admin.php')}" class="boton-cancelar">Cancelar</a>
            </div>
        </div>
        EOF;

        return $html;
    }

    protected function procesaFormulario(&$datos) {
        // Validación de datos
        $nombre = trim($datos['nombre'] ?? '');
        if (empty($nombre)) {
            $this->errores['nombre'] = 'El nombre es obligatorio';
        }

        $precio = filter_var($datos['precio'] ?? 0, FILTER_VALIDATE_FLOAT);
        if ($precio === false || $precio < 0) {
            $this->errores['precio'] = 'Precio no válido';
        }

        $descripcion = trim($datos['descripcion'] ?? '');
        
        $fecha_inicio = trim($datos['fecha_inicio'] ?? '');
        if (empty($fecha_inicio)) {
            $this->errores['fecha_inicio'] = 'Fecha de inicio requerida';
        }
        
        $ubicacion = trim($datos['ubicacion'] ?? '');
        if (empty($ubicacion)) {
            $this->errores['ubicacion'] = 'La ubicación es obligatoria';
        }
        
        $organizador = trim($datos['organizador'] ?? '');
        if (empty($organizador)) {
            $this->errores['organizador'] = 'El organizador es obligatorio';
        }
        
        $imagen = trim($datos['imagen'] ?? '');

        // Si hay errores, detenemos el proceso
        if (count($this->errores) > 0) {
            return;
        }

        // Crear el evento
        try {
            $resultado = Evento::altaEvento(
                $nombre,
                $precio,
                $descripcion,
                $fecha_inicio,
                $ubicacion,
                $organizador,
                $imagen
            );

            if (!$resultado[0]) {
                $this->errores[] = $resultado[1];
            } else {
                return 'admin.php'; // Redirigir si es exitoso
            }
        } catch (\Exception $e) {
            $this->errores[] = "Error al crear el evento: " . $e->getMessage();
        }
    }
}