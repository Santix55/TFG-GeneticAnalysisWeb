/**
 * Cuenta atrás para la firma.
 * Añade el id="tiempo" para que muestre la cuenta atrás-
 * Cuando llega a 0 deshabilita el elemento que tiene id="descarga"
 */

let tiempoRestante = Infinity; // Tiempo en segundos
let intervalo;                 // Se actualiza cada segundo
let activa = false;            // Compruba si hay una cuentaAtras en curso

function actualizarCuentaRegresiva() {
    let minutos = Math.floor(tiempoRestante / 60);
    let segundos = tiempoRestante % 60;

    // Formatear minutos y segundos para que siempre tengan dos dígitos
    minutos = minutos < 10 ? "0" + minutos : minutos;
    segundos = segundos < 10 ? "0" + segundos : segundos;

    // Actualizar el contenido del elemento span
    try {document.getElementById("tiempo").innerText = minutos + ":" + segundos;} catch(e) {}

    // Disminuir el tiempo restante
    tiempoRestante--;

    // Detener la cuenta regresiva cuando llegue a cero
    if (tiempoRestante < 0) {
        clearInterval(intervalo);
        try {document.getElementById("descargar").disabled = true;} catch(e) {}
        activa = false;
    }
}

function comenzarCuenta(_tiempoRestante) {
    tiempoRestante = _tiempoRestante;

    // Actualizar cada segundo
    if(!activa) {
        intervalo = setInterval(actualizarCuentaRegresiva, 1000);
        activa = true;
    }

    // Llamar a la función una vez al principio para evitar un segundo de retraso
    actualizarCuentaRegresiva();
}

