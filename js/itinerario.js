let diaActual = 0;
let actActual = 0;

function cambiarDia(indice) {
    // Ocultar día anterior
    const anteriorDia   = document.getElementById('dia-'   + diaActual);
    const anteriorMapa  = document.getElementById('mapa-'  + diaActual);
    const anteriorNotas = document.getElementById('notas-' + diaActual);
    if (anteriorDia)   anteriorDia.style.display   = 'none';
    if (anteriorMapa)  anteriorMapa.style.display  = 'none';
    if (anteriorNotas) anteriorNotas.style.display = 'none';

    // Mostrar nuevo día
    diaActual = indice;
    actActual = 0;

    const nuevoDia   = document.getElementById('dia-'   + diaActual);
    const nuevoMapa  = document.getElementById('mapa-'  + diaActual);
    const nuevasNotas= document.getElementById('notas-' + diaActual);
    if (nuevoDia)    nuevoDia.style.display   = 'block';
    if (nuevoMapa)   nuevoMapa.style.display  = 'block';
    if (nuevasNotas) nuevasNotas.style.display= 'block';

    // Actualizar tabs
    document.querySelectorAll('.tab').forEach((tab, i) => {
        tab.classList.toggle('activo', i === indice);
    });

    // Marcar primera actividad del nuevo día
    seleccionarActividad(diaActual, 0);
}

function seleccionarActividad(dia, act) {
    // Quitar selección anterior
    const anterior = document.getElementById('act-' + dia + '-' + actActual);
    const pinAnt   = document.getElementById('pin-' + dia + '-' + actActual);
    if (anterior) anterior.classList.remove('activa');
    if (pinAnt)   pinAnt.classList.remove('activo');

    actActual = act;

    // Marcar nueva selección
    const actEl = document.getElementById('act-' + dia + '-' + act);
    const pinEl = document.getElementById('pin-' + dia + '-' + act);
    if (actEl) actEl.classList.add('activa');
    if (pinEl) pinEl.classList.add('activo');
}

// Inicializar primera actividad al cargar
document.addEventListener('DOMContentLoaded', () => {
    seleccionarActividad(0, 0);
});
