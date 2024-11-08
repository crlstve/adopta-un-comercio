function desplegable() {
    // Selecciona el elemento con el atributo data-toggle="influencer"
    const elemento = document.querySelector('[data-toggle="influencer"]');

    // Verifica si el elemento tiene las clases "h-0" y "hidden"
    if (elemento.classList.contains('h-0') && elemento.classList.contains('hidden')) {
        // Quita "h-0" y "hidden", agrega "h-full"
        elemento.classList.remove('h-0', 'hidden');
        elemento.classList.add('h-full');
    } else {
        // Si ya está visible, restaura a "h-0" y "hidden", y quita "h-full"
        elemento.classList.add('h-0', 'hidden');
        elemento.classList.remove('h-full');
    }
}
function desplegable_2() {
    // Selecciona el elemento con el atributo data-toggle="influencer"
    const elemento = document.querySelector('[data-toggle="comercio"]');
    // Verifica si el elemento tiene las clases "h-0" y "hidden"
    if (elemento.classList.contains('h-0') && elemento.classList.contains('hidden')) {
        // Quita "h-0" y "hidden", agrega "h-full"
        elemento.classList.remove('h-0', 'hidden');
        elemento.classList.add('h-full');
    } else {
        // Si ya está visible, restaura a "h-0" y "hidden", y quita "h-full"
        elemento.classList.add('h-0', 'hidden');
        elemento.classList.remove('h-full');
    }
}

document.addEventListener('DOMContentLoaded', function () {
    // Comprobar si el parámetro 'gracias' está en la URL
    const urlParams = new URLSearchParams(window.location.search);
    const graciasParam = urlParams.has('gracias');

    if (graciasParam) {
        // Si el parámetro 'gracias' está en la URL, ocultamos los formularios y mostramos el mensaje
        document.getElementById('forms').classList.add('hidden');
        document.getElementById('gracias').classList.remove('hidden');
    } else {
        // Si no, mostramos los formularios
        document.getElementById('forms').classList.remove('hidden');
        document.getElementById('gracias').classList.add('hidden');
    }
});