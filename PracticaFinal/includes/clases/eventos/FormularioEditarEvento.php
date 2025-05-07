<?php
namespace es\ucm\fdi\aw\eventos;

use es\ucm\fdi\aw\Aplicacion;
use es\ucm\fdi\aw\Formulario;

class FormularioEditarEvento extends Formulario{

    private $idEvento;
    private $evento;

    public function __construct($idEvento){
        parent::__construct('formEditarEvento', ['urlRedireccion' => 'adminVista.php', 'method' => 'POST', 'class' => 'form-Editar']);
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
                <input type="text" id="nombre" name="nombre" required 
                       value="{$datos['nombre']}">
                {$erroresCampos['nombre']}
            </div>

            <div class="campo-formulario">
                <label for="precio">Precio (€):</label>
                <input type="number" id="precio" name="precio" required
                       value="{$datos['precio']}">
                {$erroresCampos['precio']}
            </div>

            <div class="campo-formulario">
                <label for="descripcion">Descripción:</label>
                <textarea id="descripcion" name="descripcion">{$datos['descripcion']}</textarea>
                {$erroresCampos['descripcion']}
            </div>

            <div class="campo-formulario">
                <label for="fecha_inicio">Fecha de inicio:</label>
                <input type="datetime-local" id="fecha_inicio" name="fecha_inicio" required
                       value="{$datos['fecha_inicio']}">
                {$erroresCampos['fecha_inicio']}
            </div>

            <div class="campo-formulario">
                <label for="ubicacion">Ubicación:</label>
                <input type="text" id="ubicacion" name="ubicacion" required
                       value="{$datos['ubicacion']}">
                {$erroresCampos['ubicacion']}
            </div>

            <div class="campo-formulario">
                <label for="organizador">Organizador:</label>
                <input type="text" id="organizador" name="organizador" required
                       value="{$datos['organizador']}">
                {$erroresCampos['organizador']}
            </div>

            <div class="campo-formulario">
                <label for="imagen">URL de la imagen:</label>
                <input type="text" id="imagen" name="imagen" value="{$datos['imagen']}">
                {$erroresCampos['imagen']}
            </div>

            <div class="campo-formulario">
                <label for="entradas">Entradas disponibles:</label>
                <input type="number" id="entradas" name="entradas" 
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

    protected function procesaFormulario(&$datos) {  // Si cambiamos a que no peudan estar vacios , habria que quitar los ifs 
        $id = filter_var($datos['id'] ?? $this->idEvento, FILTER_VALIDATE_INT);
        if (!$id) {
            $this->errores[] = 'ID de evento no válido';
            return;
        }

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

