<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Inicio - Dashboard</title>
    <link rel="stylesheet" href="../css/inicio.css?v=1.0">
    <script src="../js/ventanas.js"></script>
</head>
<body>
    <!-- Navbar -->
    <nav class="barra-navegacion">
        <div class="contenedor-nav">
            <div class="marca-nav">
                <h1>🧠 ReichMind</h1>
            </div>
            
            <div class="menu-nav">
                <a href="#" class="enlace-nav activo">🏠 Inicio</a>
            </div>
            
            <div class="usuario-nav">
                <button class="boton-cerrar-sesion" onclick="logoutWithSecurity()">
                    Cerrar Sesión
                </button>
            </div>
        </div>
    </nav>

    <div class="contenedor-dashboard">
        <!-- Contenedor 1: Perfil de Usuario -->
        <div class="contenedor perfil-contenedor">
            <div class="cabecera-perfil">
                <div class="contenedor-avatar">
                    <div class="circulo-avatar">
                        <img src="../img/default-avatar.jpg" alt="Avatar del usuario" class="imagen-avatar">
                    </div>
                    <div class="barra-nivel">
                        <div class="progreso-nivel" style="--progress: 65%"></div>
                    </div>
                </div>
                
                <div class="info-usuario">
                    <h2 class="nombre-usuario" id="username-display">-- --</h2>
                    <p class="nivel-usuario">Nivel: --</p>
                </div>
            </div>
            
            <div class="cuadricula-estadisticas">
                <div class="tarjeta-estadistica">
                    <div class="icono-estadistica">🎮</div>
                    <div class="numero-estadistica">--</div>
                    <div class="etiqueta-estadistica">Partidas Totales</div>
                </div>
                
                <div class="tarjeta-estadistica">
                    <div class="icono-estadistica">🏆</div>
                    <div class="numero-estadistica">--</div>
                    <div class="etiqueta-estadistica">Partidas Ganadas</div>
                </div>
                
                <div class="tarjeta-estadistica">
                    <div class="icono-estadistica">❌</div>
                    <div class="numero-estadistica">--</div>
                    <div class="etiqueta-estadistica">Partidas Fallidas</div>
                </div>
                
                <div class="tarjeta-estadistica">
                    <div class="icono-estadistica">⭐</div>
                    <div class="numero-estadistica">--</div>
                    <div class="etiqueta-estadistica">Nivel</div>
                </div>
            </div>
        </div>
        
        <div class="contenedor contenedor-secundario">
            <h2 class="titulo-formulario">Panel de Juego</h2>
            
            <!-- boton para iniciar partida -->
            <div class="seccion-partida">
                <button class="boton-iniciar-partida" onclick="iniciarNuevaPartida()">
                    Iniciar Nueva Partida
                </button>
            </div>

            <!-- listas de estadisticas -->
            <div class="contenedor-listas">
                <div class="lista-contenedor">
                    <h3 class="titulo-lista">💰 Monedas Ganadas</h3>
                    <ul class="lista-monedas" id="lista-monedas">
                        <li class="lista-vacia">No hay registros aún</li>
                    </ul>
                </div>

                <div class="lista-contenedor">
                    <h3 class="titulo-lista">⏱️ Puntos y Tiempo</h3>
                    <ul class="lista-puntos-tiempo" id="lista-puntos-tiempo">
                        <li class="lista-vacia">No hay registros aún</li>
                    </ul>
                </div>
            </div>

            <!-- boton de descarga -->
            <div class="seccion-descarga">
                <button class="boton-descargar" onclick="descargarEstadisticas()">
                    Descargar Estadísticas
                </button>
            </div>
        </div>
    </div>

    <script>
        // verifico el estado de sesion cuando carga la pagina
        verificarSesion();
        
        // limpio cualquier estado previo de sesion cerrada al cargar inicio.php correctamente
        limpiarEstadoSesion();

        // funcion para iniciar nueva partida
        function iniciarNuevaPartida() {
            
        }

        // funcion para agregar monedas a la lista
        function agregarMoneda(cantidad) {
            
        }

        // funcion para agregar puntos y tiempo a la lista
        function agregarPuntosTiempo(puntos, tiempo) {
            
        }

        // funcion para descargar estadisticas
        function descargarEstadisticas() {
            }
    </script>
</body>
</html>
