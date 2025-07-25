// Archivo ventanas.js - Funciones de gestión de sesión y ventanas

// Función para verificar el estado de sesión
function verificarSesion() {
    console.log('Verificando estado de sesión...');
    
    // Aquí puedes agregar la lógica para verificar si el usuario está autenticado
    // Por ejemplo, verificar si existe una cookie de sesión o token
    
    // Por ahora, simplemente log para debug
    const username = localStorage.getItem('username');
    if (username) {
        document.getElementById('username-display').textContent = username;
    }
}

// Función para limpiar el estado de sesión
function limpiarEstadoSesion() {
    console.log('Limpiando estado de sesión...');
    
    // Limpiar cualquier indicador de sesión cerrada
    localStorage.removeItem('sessionClosed');
    sessionStorage.removeItem('sessionClosed');
}

// Función para cerrar sesión con seguridad
function logoutWithSecurity() {
    console.log('Cerrando sesión...');
    
    // Confirmar cierre de sesión
    if (confirm('¿Estás seguro de que deseas cerrar sesión?')) {
        // Limpiar datos de sesión
        localStorage.clear();
        sessionStorage.clear();
        
        // Marcar que la sesión fue cerrada intencionalmente
        localStorage.setItem('sessionClosed', 'true');
        
        // Redirigir a la página de login
        window.location.href = '../moduloUsuarios/indexUsuario.php';
    }
}

// Función para mostrar notificaciones
function mostrarNotificacion(mensaje, tipo = 'info') {
    console.log(`Notificación [${tipo}]: ${mensaje}`);
    
    // Crear elemento de notificación
    const notificacion = document.createElement('div');
    notificacion.className = `notificacion notificacion-${tipo}`;
    notificacion.innerHTML = `
        <span>${mensaje}</span>
        <button onclick="this.parentElement.remove()">×</button>
    `;
    
    // Agregar estilos inline básicos
    notificacion.style.cssText = `
        position: fixed;
        top: 20px;
        right: 20px;
        background: ${tipo === 'error' ? '#f56565' : tipo === 'success' ? '#48bb78' : '#4299e1'};
        color: white;
        padding: 15px 20px;
        border-radius: 8px;
        box-shadow: 0 4px 12px rgba(0,0,0,0.2);
        z-index: 9999;
        display: flex;
        align-items: center;
        gap: 10px;
        font-weight: 600;
        max-width: 300px;
        animation: slideInRight 0.3s ease-out;
    `;
    
    // Agregar al body
    document.body.appendChild(notificacion);
    
    // Auto-remover después de 5 segundos
    setTimeout(() => {
        if (notificacion.parentElement) {
            notificacion.remove();
        }
    }, 5000);
}

// Función para manejar errores globales
function manejarError(error, contexto = '') {
    console.error(`Error${contexto ? ' en ' + contexto : ''}:`, error);
    mostrarNotificacion(`Error: ${error.message || 'Algo salió mal'}`, 'error');
}

// Event listener para errores no manejados
window.addEventListener('error', function(event) {
    manejarError(event.error, 'JavaScript');
});

// Event listener para promesas rechazadas
window.addEventListener('unhandledrejection', function(event) {
    manejarError(new Error(event.reason), 'Promise');
});

console.log('✅ ventanas.js cargado correctamente');
