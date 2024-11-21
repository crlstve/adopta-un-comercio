function desplegable() {
    const elemento = document.querySelector('[data-toggle="influencer"]');
    if (elemento.classList.contains('h-0') && elemento.classList.contains('hidden')) {
        elemento.classList.remove('h-0', 'hidden');
        elemento.classList.add('h-full');
    } else {
        elemento.classList.add('h-0', 'hidden');
        elemento.classList.remove('h-full');
    }
}
function desplegable_2() {
    const elemento = document.querySelector('[data-toggle="comercio"]');
    if (elemento.classList.contains('h-0') && elemento.classList.contains('hidden')) {
        elemento.classList.remove('h-0', 'hidden');
        elemento.classList.add('h-full');
    } else {
        elemento.classList.add('h-0', 'hidden');
        elemento.classList.remove('h-full');
    }
}

document.addEventListener('DOMContentLoaded', function () {
    const urlParams = new URLSearchParams(window.location.search);
    const graciasParam = urlParams.has('gracias');
    if (graciasParam) {
        document.getElementById('forms').classList.add('hidden');
        document.getElementById('gracias').classList.remove('hidden');
    } else {
        document.getElementById('forms').classList.remove('hidden');
        document.getElementById('gracias').classList.add('hidden');
    }
});