<?php
namespace es\ucm\fdi\aw\eventos;

use es\ucm\fdi\aw\Aplicacion;
use es\ucm\fdi\aw\Formulario;

class FormularioAnyadirEvento extends Formulario {

    public function __construct() {
        parent::__construct('formAnyadirEvento', [
            'urlRedireccion' => 'adminVista.php', 
            'method' => 'POST', 
            'class' => 'form-Nuevo',
            'enctype' => 'multipart/form-data'
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
            'imagen' => '',
            'entradas' => 0
        ];

        $app = Aplicacion::getInstance();

        
        $erroresCampos = self::generaErroresCampos(
            ['nombre', 'precio', 'descripcion', 'fecha_inicio', 'ubicacion', 'organizador', 'imagen', 'entradas'],
            $this->errores, 
            'span', 
            ['class' => 'error']
        );
        
        $erroresGlobales = self::generaListaErroresGlobales($this->errores, 'errores-globales');
        
        $html = <<<EOF
        <div class="nuevo-evento">
            <h2>Añadir Nuevo Evento</h2>
            {$erroresGlobales}
            
            <div class="campo-formulario">
                <label for="nombre">Nombre del evento:</label>
                <input type="text" id="nombre" name="nombre" required>
                {$erroresCampos['nombre']}
            </div>

            <div class="campo-formulario">
                <label for="precio">Precio (€):</label>
                <input type="number" id="precio" name="precio" min='0' step="0.5" required>
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
                <label for="imagen">Selecciona una imagen:</label>
                <input type="file" id="imagen" name="imagen" accept="image/*">
                {$erroresCampos['imagen']}
            </div>

            <div class="campo-formulario">
                <label for="entradas">Entradas disponibles:</label>
                <input type="number" id="entradas" name="entradas" 
                    min="0" step="1" required value="{$datos['entradas']}">
                {$erroresCampos['entradas']}
            </div>


            <div class="acciones">
                <button type="submit" class="boton-guardar">Crear Evento</button>
                <a href="{$app->resuelve('adminVista.php')}" class="boton-cancelar">Cancelar</a>
            </div>
        </div>
        EOF;

        return $html;
    }

    protected function procesaFormulario(&$datos) {
        // Validar nombre
        $nombre = trim($datos['nombre'] ?? '');
        if (empty($nombre)) {
            $this->errores['nombre'] = 'El nombre es obligatorio';
        }

        // Validar precio
        $precioRaw = str_replace(',', '.', $datos['precio'] ?? '');
        $precio = filter_var($precioRaw, FILTER_VALIDATE_FLOAT);
        if ($precio === false || $precio < 0) {
            $this->errores['precio'] = 'Precio no válido';
        } 
        else {
            $precio = number_format($precio, 2, '.', '');
        }

        // Validar descripción
        $descripcion = trim($datos['descripcion'] ?? '');
        
        // Validar fecha
        $fecha_inicio = trim($datos['fecha_inicio'] ?? '');
        if (empty($fecha_inicio)) {
            $this->errores['fecha_inicio'] = 'Fecha de inicio requerida';
        }
        
        // Validar ubicación
        $ubicacion = trim($datos['ubicacion'] ?? '');
        if (empty($ubicacion)) {
            $this->errores['ubicacion'] = 'La ubicación es obligatoria';
        }
        
        // Validar organizador
        $organizador = trim($datos['organizador'] ?? '');
        if (empty($organizador)) {
            $this->errores['organizador'] = 'El organizador es obligatorio';
        }
        
        // Validar imagen
        $tiposPermitidos = ['image/jpeg', 'image/jpg', 'image/png'];
        $imagen = 'img/default.png';

        if (isset($_FILES['imagen']) && $_FILES['imagen']['error'] === UPLOAD_ERR_OK) {
            if (!in_array($_FILES['imagen']['type'], $tiposPermitidos)) {
                $this->errores['imagen'] = 'El tipo de imagen no es válido. Solo se permiten JPEG, JPG o PNG.';
            } else {
                $nombreImagen = basename($_FILES['imagen']['name']);
                $rutaTemporal = $_FILES['imagen']['tmp_name'];
                $rutaDestino = RAIZ_APP . '/' . RUTA_IMGS. '/' . $nombreImagen;

                if (!move_uploaded_file($rutaTemporal, $rutaDestino)) {
                    $this->errores['imagen'] = 'Error al guardar la imagen en el servidor';
                } else {
                    $imagen = 'img/' . $nombreImagen;
                }
            }
        }

        // Validar entradas
        $entradas = filter_var($datos['entradas'], FILTER_VALIDATE_INT);
        if ($entradas === false || $entradas < 0) {
            $this->errores['entradas'] = 'Número de entradas no válido';
        }

        if (count($this->errores) > 0) {
            return;
        }


        try {
            $resultado = Evento::altaEvento(
                $nombre,
                $precio,
                $descripcion,
                $fecha_inicio,
                $ubicacion,
                $organizador,
                $imagen,
                $entradas
            );

            if (!$resultado[0]) {
                $this->errores[] = $resultado[1];
            } else {
                return 'adminVista.php'; 
            }
        } catch (\Exception $e) {
            $this->errores[] = "Error al crear el evento: " . $e->getMessage();
        }
    }
}
