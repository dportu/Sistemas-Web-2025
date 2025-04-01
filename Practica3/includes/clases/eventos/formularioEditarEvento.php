<?php
namespace es\ucm\fdi\aw\Eventos;

use es\ucm\fdi\aw\Aplicacion;
use es\ucm\fdi\aw\Formulario;

class FormularioEditarEvento extends Formulario{

    private $idEvento;
    private $evento;

    public function __construct($idEvento){
        parent::__construct('formEditarEvento', ['action' => 'admin.php', 'class' => 'form-Editar' ]);

        $this->idEvento = $idEvento;
        $this->evento = Evento::buscaPorId($idEvento);
        
        if (!$this->evento) {
            throw new \Exception('El evento no existe');
        }
    }
    

    protected function generaCamposFormulario(&$datos) {
        if (empty($datos)) {
            $datos = [
                'nombre' => $this->evento->getNombre(),
                'precio' => $this->evento->getPrecio(),
                'descripcion' => $this->evento->getDescripcion(),
                'fecha_inicio' => $this->evento->getFecha(),
                'ubicacion' => $this->evento->getUbicacion(),
                'organizador' => $this->evento->getOrganizador(),
                'imagen' => $this->evento->getImagen()
            ];
        
        $app = Aplicacion::getInstance();

        $erroresCampos = self::generaErroresCampos(['nombre', 'precio', 'descripcion', 'fecha_inicio', 'ubicacion', 'organizador', 'imagen'], $this->errores, 'span', ['class' => 'error']);
        $erroresGlobales = self::generaListaErroresGlobales($this->errores, 'errores-globales');
        $html = $erroresGlobales;

        $html .= <<<EOF
        <div class="editar-evento">
            <h1>Editar Evento: {$app->escape($this->evento->getNombre())}</h1>
            
            <div class="campo-formulario">
                <label for="nombre">Nombre del evento:</label>
                <input type="text" id="nombre" name="nombre" required 
                       value="{$app->escape($datos['nombre'])}">
                {$erroresCampos['nombre']}
            </div>

            <div class="campo-formulario">
                <label for="precio">Precio (€):</label>
                <input type="number" id="precio" name="precio" step="0.01" min="0" required
                       value="{$app->escape($datos['precio'])}">
                {$erroresCampos['precio']}
            </div>

            <div class="campo-formulario">
                <label for="descripcion">Descripción:</label>
                <textarea id="descripcion" name="descripcion">{$app->escape($datos['descripcion'])}</textarea>
                {$erroresCampos['descripcion']}
            </div>

            <div class="campo-formulario">
                <label for="fecha_inicio">Fecha de inicio:</label>
                <input type="datetime-local" id="fecha_inicio" name="fecha_inicio" required
                       value="{$app->escape(date('Y-m-d\TH:i', strtotime($datos['fecha_inicio'])))}">
                {$erroresCampos['fecha_inicio']}
            </div>

            <div class="campo-formulario">
                <label for="ubicacion">Ubicación:</label>
                <input type="text" id="ubicacion" name="ubicacion" required
                       value="{$app->escape($datos['ubicacion'])}">
                {$erroresCampos['ubicacion']}
            </div>

            <div class="campo-formulario">
                <label for="organizador">Organizador:</label>
                <input type="text" id="organizador" name="organizador" required
                       value="{$app->escape($datos['organizador'])}">
                {$erroresCampos['organizador']}
            </div>

            <div class="campo-formulario">
                <label for="imagen">URL de la imagen:</label>
                <input type="url" id="imagen" name="imagen" 
                       value="{$app->escape($datos['imagen'])}">
                {$erroresCampos['imagen']}
            </div>

            <div class="acciones">
                <button type="submit" class="boton-guardar">Guardar cambios</button>
                <a href="admin.php" class="boton-cancelar">Cancelar</a>
            </div>
        </div>
        EOF;

        return $html;

    }

    protected function procesaFormulario(&$datos) {
        // Validar datos
        $nombre = trim($datos['nombre'] ?? '');
        if (empty($nombre)) {
            $this->errores['nombre'] = 'El nombre es obligatorio';
        }
        
        $precio = filter_var($datos['precio'] ?? '', FILTER_VALIDATE_FLOAT);
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
        
        // Si hay errores no continuamos
        if (count($this->errores) > 0) {
            return;
        }
        
        // Actualizar el evento
        try {
            $this->evento->editarEvento(
                $nombre,
                $precio,
                $descripcion,
                $fecha_inicio,
                $ubicacion,
                $organizador,
                $imagen
            );
        } catch (\Exception $e) {
            $this->errores[] = "Error al actualizar el evento: " . $e->getMessage();
        }
    }
}

