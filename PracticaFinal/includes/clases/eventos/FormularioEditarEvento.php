<?php
namespace es\ucm\fdi\aw\eventos;

use es\ucm\fdi\aw\Aplicacion;
use es\ucm\fdi\aw\Formulario;

class FormularioEditarEvento extends Formulario{

    private $idEvento;
    private $evento;

    public function __construct($idEvento){
        parent::__construct('formEditarEvento', 
                            ['urlRedireccion' => 'adminVista.php', 
                            'method' => 'POST', 
                            'class' => 'form-Editar',
                            'enctype' => 'multipart/form-data'
                        ]);
        $this->idEvento = $idEvento;
        $this->evento = Evento::buscaPorId($idEvento);
        
        if (!$this->evento) {
            throw new \Exception('El evento no existe');
        }
    }

    protected function generaCamposFormulario(&$datos) {
        if (empty($datos)) {
            $datos = [
                'id' => $this->evento->getId(),
                'nombre' => $this->evento->getNombre(),
                'precio' => $this->evento->getPrecio(),
                'descripcion' => $this->evento->getDescripcion(),
                'fecha_inicio' => $this->evento->getFecha(),
                'ubicacion' => $this->evento->getUbicacion(),
                'organizador' => $this->evento->getOrganizador(),
                'imagen' => $this->evento->getImagen(),
                'entradas_disponibles' => $this->evento->getEntradasDisponibles()
            ];
        
        $app = Aplicacion::getInstance();

        $erroresCampos = self::generaErroresCampos(['nombre', 'precio', 'descripcion', 'fecha_inicio', 'ubicacion', 'organizador', 'imagen', 'entradas'], $this->errores, 'span', ['class' => 'error']);
        $erroresGlobales = self::generaListaErroresGlobales($this->errores, 'errores-globales');
        $html = $erroresGlobales;

        $html .= <<<EOF
        <div class="editar-evento">
            <h2>Editar Evento: {$this->evento->getNombre()}</h2>
            
            <div class="campo-formulario">
                <label for="nombre">Nombre del evento:</label>
                <input type="text" class="texto-card" id="nombre" name="nombre" required 
                       value="{$datos['nombre']}">
                {$erroresCampos['nombre']}
            </div>

            <div class="campo-formulario">
                <label for="precio">Precio (€):</label>
                <input type="number" class="texto-card" id="precio" name="precio" step="0.5" min="0" 
                    value="{$datos['precio']}" required>
                {$erroresCampos['precio']}
            </div>

            <div class="campo-formulario">
                <label for="descripcion">Descripción:</label>
                <textarea id="descripcion"  class="texto-card" name="descripcion">{$datos['descripcion']}</textarea>
                {$erroresCampos['descripcion']}
            </div>

            <div class="campo-formulario">
                <label for="fecha_inicio">Fecha de inicio:</label>
                <input type="datetime-local" class="texto-card" id="fecha_inicio" name="fecha_inicio" required
                       value="{$datos['fecha_inicio']}">
                {$erroresCampos['fecha_inicio']}
            </div>

            <div class="campo-formulario">
                <label for="ubicacion">Ubicación:</label>
                <input type="text" class="texto-card" id="ubicacion" name="ubicacion" required
                       value="{$datos['ubicacion']}">
                {$erroresCampos['ubicacion']}
            </div>

            <div class="campo-formulario">
                <label for="organizador">Organizador:</label>
                <input type="text" class="texto-card" id="organizador" name="organizador" required
                       value="{$datos['organizador']}">
                {$erroresCampos['organizador']}
            </div>

            <div class="campo-formulario">
                <label for="imagen">Imagen actual: {$datos['imagen']}</label><br>
                <label for="imagen">Subir nueva imagen (opcional):</label>
                <input type="file" class="texto-card" id="imagen" name="imagen" accept="image/*">
                {$erroresCampos['imagen']}
            </div>

            <div class="campo-formulario">
                <label for="entradas">Entradas disponibles:</label>
                <input type="number" class="texto-card" id="entradas" name="entradas" 
                    min="0" step="1" required 
                    value="{$datos['entradas_disponibles']}">
                {$erroresCampos['entradas']}
            </div>

            <div class="acciones">
                <button type="submit" class="boton-guardar">Guardar cambios</button>
                <a href="{$app->resuelve('adminVista.php')}" class="boton-cancelar">Cancelar</a>
            </div>
        </div>
        EOF;

        return $html;
    }
}

    protected function procesaFormulario(&$datos) {
        $id = filter_var($datos['id'] ?? $this->idEvento, FILTER_VALIDATE_INT);
        if (!$id) {
            $this->errores[] = 'ID de evento no válido';
            return;
        }

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
        $imagen = $this->evento->getImagen();

        if (isset($_FILES['imagen']) && $_FILES['imagen']['error'] === UPLOAD_ERR_OK) {
            if (!in_array($_FILES['imagen']['type'], $tiposPermitidos)) {
                $this->errores['imagen'] = 'El tipo de imagen no es válido. Solo se permiten JPEG, JPG o PNG.';
            } else {
                $nombreImagen = basename($_FILES['imagen']['name']);
                $rutaTemporal = $_FILES['imagen']['tmp_name'];
                $rutaDestino = RAIZ_APP . '/' . RUTA_IMGS. '/' . $nombreImagen;

                error_log("*********************TRAZA: Ruta temporal: $rutaTemporal; ruta destino: $rutaDestino", 0);

                if (!move_uploaded_file($rutaTemporal, $rutaDestino)) {
                    $this->errores['imagen'] = 'Error al guardar la imagen en el servidor';
                } else {
                    $imagen = 'img/' . $nombreImagen;
                }
            }
        }


        // Validar entradas
        $entradas = filter_var($datos['entradas'] ?? 0, FILTER_VALIDATE_INT);
        if ($entradas === false || $entradas < 0) {
            $this->errores['entradas'] = 'Número de entradas no válido';
        }

        if (count($this->errores) > 0) {
            return;
        }

        try {
            $evento = Evento::buscaPorId($id);
            if (!$evento) {
                throw new \Exception("No se encontró el evento con ID: $id");
            }

            $resultado = $evento->editarEvento(
                $nombre,
                $precio,
                $descripcion,
                $fecha_inicio,
                $ubicacion,
                $organizador,
                $imagen,
                $entradas
            );

            if (!$resultado) {
                throw new \Exception("No se pudo actualizar el evento en la base de datos");
            }

            return 'adminVista.php';

        } catch (\Exception $e) {
            $this->errores[] = "Error al actualizar el evento: " . $e->getMessage();
        }
    }

}

