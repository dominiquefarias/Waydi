function cambiarTab(nombre, pushState) {
    // Ocultar todos los paneles
    document.querySelectorAll('.admin-panel').forEach(p => p.classList.remove('activo'));
    document.querySelectorAll('.nav-item').forEach(n => n.classList.remove('activo'));

    // Mostrar el seleccionado
    const panel = document.getElementById('panel-' + nombre);
    if (panel) panel.classList.add('activo');

    const btn = document.querySelector('[onclick="cambiarTab(\'' + nombre + '\')"]');
    if (btn) btn.classList.add('activo');

    // Actualizar URL sin recargar (para que el back funcione)
    if (pushState !== false) {
        const url = new URL(location.href);
        url.searchParams.set('tab', nombre);
        history.pushState({tab: nombre}, '', url.toString());
    }
}

// Manejar el botón atrás del navegador
window.addEventListener('popstate', e => {
    const tab = (e.state && e.state.tab) || 'dashboard';
    cambiarTab(tab, false);
});
