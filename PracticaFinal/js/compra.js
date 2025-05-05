document.addEventListener('DOMContentLoaded', () => {
    const inputCantidad = document.getElementById('input-cantidad');
    const inputPuntos = document.getElementById('input-puntos');
    const precioTotal = document.getElementById('precio-total');

    // Valores iniciales desde PHP
    let precioPorEntrada = parseFloat(inputCantidad.dataset.precio);
    let puntosDisponibles = parseInt(inputCantidad.dataset.puntos);

    function actualizarTodo() {
        const cantidad = parseInt(inputCantidad.value) || 0;
        
        // Calcular máximo de puntos permitido
        const maxPuntosEfectivo = Math.min(
            puntosDisponibles,
            precioPorEntrada * cantidad
        );

        // Aplicar límites dinámicos
        inputPuntos.max = maxPuntosEfectivo;
        
        // Ajustar valor actual si supera el nuevo máximo
        let puntos = parseInt(inputPuntos.value) || 0;
        if (puntos > maxPuntosEfectivo) {
            inputPuntos.value = maxPuntosEfectivo;
            puntos = maxPuntosEfectivo;
        }

        // Calcular y mostrar total
        const total = Math.max((precioPorEntrada * cantidad) - puntos, 0);
        precioTotal.textContent = `Total estimado: ${total} €`;

        // Deshabilitar input si total es 0
       
    }

    // Event listeners
    inputCantidad.addEventListener('input', actualizarTodo);
    inputPuntos.addEventListener('input', actualizarTodo);

    // Ejecutar al inicio
    actualizarTodo();
});