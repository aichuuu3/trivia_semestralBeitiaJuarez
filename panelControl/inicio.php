<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Inicio - Dashboard</title>
    <!-- Forzar recarga de CSS -->
    <link rel="stylesheet" href="../css/inicio.css?v=<?php echo time(); ?>">
    <script src="../js/ventanas.js?v=<?php echo time(); ?>"></script>
    <!-- Meta tags para evitar caché -->
    <meta http-equiv="Cache-Control" content="no-cache, no-store, must-revalidate">
    <meta http-equiv="Pragma" content="no-cache">
    <meta http-equiv="Expires" content="0">
    
    <!-- Estilos críticos inline para asegurar visualización -->
    <style>
        /* Estilos críticos inline */
        body {
            margin: 0;
            padding: 0;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(135deg, #533483 0%, #0f3460 100%);
            min-height: 100vh;
            color: white;
        }
        
        /* Spinner básico */
        .spinner {
            border: 4px solid #f3f3f3;
            border-top: 4px solid #7b2cbf;
            border-radius: 50%;
            width: 40px;
            height: 40px;
            animation: spin 1s linear infinite;
            margin: 0 auto 15px;
        }
        
        @keyframes spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }
        
        /* Estilos básicos para botones */
        button {
            cursor: pointer;
            transition: all 0.3s ease;
        }
        
        button:hover {
            transform: translateY(-2px);
        }
    </style>
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
                
                <!-- Botón de debug temporal -->
                <button style="background: #28a745; color: white; border: none; padding: 10px 20px; border-radius: 5px; margin-left: 10px; cursor: pointer;" onclick="alert('✅ JavaScript funciona!')">
                    🔧 Test
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
        console.log('🚀 Inicializando inicio.php...');
        
        // Esperar a que la página se cargue completamente
        document.addEventListener('DOMContentLoaded', function() {
            console.log('📄 DOM cargado completamente');
            
            // Verificar si las funciones de ventanas.js están disponibles
            if (typeof verificarSesion === 'function') {
                verificarSesion();
            } else {
                console.warn('⚠️ verificarSesion no está disponible');
            }
            
            if (typeof limpiarEstadoSesion === 'function') {
                limpiarEstadoSesion();
            } else {
                console.warn('⚠️ limpiarEstadoSesion no está disponible');
            }
        });

        // funcion para iniciar nueva partida
        function iniciarNuevaPartida() {
            console.log('🎮 Iniciando nueva partida...');
            
            try {
                // Ocultar el contenido actual del panel de juego
                const seccionPartida = document.querySelector('.seccion-partida');
                const contenedorListas = document.querySelector('.contenedor-listas');
                const seccionDescarga = document.querySelector('.seccion-descarga');
                
                console.log('📋 Elementos encontrados:', {
                    seccionPartida: !!seccionPartida,
                    contenedorListas: !!contenedorListas,
                    seccionDescarga: !!seccionDescarga
                });
                
                if (seccionPartida) seccionPartida.style.display = 'none';
                if (contenedorListas) contenedorListas.style.display = 'none';
                if (seccionDescarga) seccionDescarga.style.display = 'none';
                
                // Crear contenedor para selección de categorías
                const contenedorCategorias = document.createElement('div');
                contenedorCategorias.className = 'contenedor-categorias';
                contenedorCategorias.innerHTML = `
                    <h3 class="titulo-categorias">🎯 Selecciona una Categoría</h3>
                    <div class="loading-categorias">
                        <div class="spinner"></div>
                        <p>Cargando categorías...</p>
                    </div>
                    <div class="botones-categorias" style="display: none;"></div>
                    <button class="boton-regresar" onclick="regresarPanelPrincipal()">
                        ← Regresar
                    </button>
                `;
                
                // Insertar después del título del formulario
                const tituloFormulario = document.querySelector('.titulo-formulario');
                if (tituloFormulario) {
                    tituloFormulario.insertAdjacentElement('afterend', contenedorCategorias);
                    console.log('✅ Contenedor de categorías insertado');
                } else {
                    console.error('❌ No se encontró el título del formulario');
                    return;
                }
                
                // Cargar categorías desde el servidor
                cargarCategorias();
                
            } catch (error) {
                console.error('❌ Error en iniciarNuevaPartida:', error);
                alert('Error al iniciar nueva partida: ' + error.message);
            }
        }

        // funcion para cargar categorias desde el servidor
        function cargarCategorias() {
            fetch('cargarCategorias.php')
                .then(response => response.json())
                .then(data => {
                    const loadingDiv = document.querySelector('.loading-categorias');
                    const botonesDiv = document.querySelector('.botones-categorias');
                    
                    if (data.status === 'success') {
                        // Ocultar loading
                        loadingDiv.style.display = 'none';
                        
                        // Crear botones de categorías
                        let botonesHTML = '';
                        data.data.forEach(categoria => {
                            const iconos = {
                                'Principiante': '🌱',
                                'Novato': '⚡',
                                'Experto': '🔥'
                            };
                            
                            const colores = {
                                'Principiante': 'verde',
                                'Novato': 'amarillo', 
                                'Experto': 'rojo'
                            };
                            
                            botonesHTML += `
                                <button class="boton-categoria ${colores[categoria.nombre_categoria]}" 
                                        onclick="seleccionarCategoria(${categoria.id_categoria}, '${categoria.nombre_categoria}')">
                                    <span class="icono-categoria">${iconos[categoria.nombre_categoria]}</span>
                                    <span class="nombre-categoria">${categoria.nombre_categoria}</span>
                                    <span class="descripcion-categoria">
                                        ${categoria.nombre_categoria === 'Principiante' ? 'Preguntas básicas' : 
                                          categoria.nombre_categoria === 'Novato' ? 'Preguntas intermedias' : 
                                          'Preguntas avanzadas'}
                                    </span>
                                </button>
                            `;
                        });
                        
                        botonesDiv.innerHTML = botonesHTML;
                        botonesDiv.style.display = 'grid';
                        
                    } else {
                        loadingDiv.innerHTML = `
                            <div class="error-loading">
                                <p>❌ Error al cargar categorías</p>
                                <p>${data.message}</p>
                            </div>
                        `;
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    const loadingDiv = document.querySelector('.loading-categorias');
                    loadingDiv.innerHTML = `
                        <div class="error-loading">
                            <p>❌ Error de conexión</p>
                            <p>No se pudieron cargar las categorías</p>
                        </div>
                    `;
                });
        }

        // funcion para regresar al panel principal
        function regresarPanelPrincipal() {
            console.log('Regresando al panel principal...');
            
            // Eliminar contenedor de categorías
            const contenedorCategorias = document.querySelector('.contenedor-categorias');
            if (contenedorCategorias) {
                contenedorCategorias.remove();
            }
            
            // Mostrar elementos originales
            const seccionPartida = document.querySelector('.seccion-partida');
            const contenedorListas = document.querySelector('.contenedor-listas');
            const seccionDescarga = document.querySelector('.seccion-descarga');
            
            seccionPartida.style.display = 'block';
            contenedorListas.style.display = 'flex';
            seccionDescarga.style.display = 'block';
        }

        // funcion para seleccionar categoria
        function seleccionarCategoria(idCategoria, nombreCategoria) {
            console.log(`Categoría seleccionada: ${nombreCategoria} (ID: ${idCategoria})`);
            
            // Ocultar los botones de categorías
            const botonesDiv = document.querySelector('.botones-categorias');
            const tituloCategoria = document.querySelector('.titulo-categorias');
            const botonRegresar = document.querySelector('.boton-regresar');
            
            botonesDiv.style.display = 'none';
            botonRegresar.style.display = 'none';
            
            // Actualizar título para mostrar categoría seleccionada
            tituloCategoria.innerHTML = `🎯 ${nombreCategoria} - Selecciona un Tema`;
            
            // Crear loading para temas
            const loadingTemas = document.createElement('div');
            loadingTemas.className = 'loading-temas';
            loadingTemas.innerHTML = `
                <div class="spinner"></div>
                <p>Cargando temas disponibles...</p>
            `;
            
            // Crear contenedor para botones de temas
            const contenedorTemas = document.createElement('div');
            contenedorTemas.className = 'botones-temas';
            contenedorTemas.style.display = 'none';
            
            // Crear botón para regresar a categorías
            const botonRegresarCategorias = document.createElement('button');
            botonRegresarCategorias.className = 'boton-regresar-categorias';
            botonRegresarCategorias.innerHTML = '← Regresar a Categorías';
            botonRegresarCategorias.onclick = () => regresarACategorias(idCategoria, nombreCategoria);
            
            // Insertar elementos en el contenedor
            const contenedorCategorias = document.querySelector('.contenedor-categorias');
            contenedorCategorias.appendChild(loadingTemas);
            contenedorCategorias.appendChild(contenedorTemas);
            contenedorCategorias.appendChild(botonRegresarCategorias);
            
            // Cargar temas desde el servidor
            cargarTemas(idCategoria, nombreCategoria);
        }

        // funcion para cargar temas de una categoria especifica
        function cargarTemas(idCategoria, nombreCategoria) {
            const formData = new FormData();
            formData.append('categoria_id', idCategoria);
            
            fetch('cargarTemas.php', {
                method: 'POST',
                body: formData
            })
                .then(response => response.json())
                .then(data => {
                    const loadingDiv = document.querySelector('.loading-temas');
                    const temasDiv = document.querySelector('.botones-temas');
                    
                    if (data.status === 'success') {
                        // Ocultar loading
                        loadingDiv.style.display = 'none';
                        
                        // Crear botones de temas
                        let temasHTML = '';
                        data.data.forEach(tema => {
                            const iconosTemas = {
                                'PHP': '🐘',
                                'Laravel': '🔴',
                                'CRUD': '📝',
                                'JavaScript': '🟡',
                                'MySQL': '🗃️',
                                'CSS': '🎨',
                                'HTML': '📄'
                            };
                            
                            const coloresTemas = {
                                'PHP': 'tema-php',
                                'Laravel': 'tema-laravel',
                                'CRUD': 'tema-crud',
                                'JavaScript': 'tema-js',
                                'MySQL': 'tema-mysql',
                                'CSS': 'tema-css',
                                'HTML': 'tema-html'
                            };
                            
                            const iconoTema = iconosTemas[tema.nombre_tema] || '📚';
                            const colorTema = coloresTemas[tema.nombre_tema] || 'tema-default';
                            
                            temasHTML += `
                                <button class="boton-tema ${colorTema}" 
                                        onclick="seleccionarTema(${tema.id_tema}, '${tema.nombre_tema}', ${idCategoria}, '${nombreCategoria}')">
                                    <span class="icono-tema">${iconoTema}</span>
                                    <span class="nombre-tema">${tema.nombre_tema}</span>
                                    <span class="descripcion-tema">
                                        ${tema.descripcion || 'Preguntas de ' + tema.nombre_tema}
                                    </span>
                                </button>
                            `;
                        });
                        
                        temasDiv.innerHTML = temasHTML;
                        temasDiv.style.display = 'grid';
                        
                    } else {
                        loadingDiv.innerHTML = `
                            <div class="error-loading">
                                <p>❌ ${data.message}</p>
                                <p>No hay temas disponibles para esta categoría</p>
                            </div>
                        `;
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    const loadingDiv = document.querySelector('.loading-temas');
                    loadingDiv.innerHTML = `
                        <div class="error-loading">
                            <p>❌ Error de conexión</p>
                            <p>No se pudieron cargar los temas</p>
                        </div>
                    `;
                });
        }

        // funcion para regresar a la seleccion de categorias
        function regresarACategorias(idCategoria, nombreCategoria) {
            console.log('Regresando a categorías...');
            
            // Eliminar elementos de temas
            const loadingTemas = document.querySelector('.loading-temas');
            const contenedorTemas = document.querySelector('.botones-temas');
            const botonRegresarCategorias = document.querySelector('.boton-regresar-categorias');
            
            if (loadingTemas) loadingTemas.remove();
            if (contenedorTemas) contenedorTemas.remove();
            if (botonRegresarCategorias) botonRegresarCategorias.remove();
            
            // Restaurar título original
            const tituloCategoria = document.querySelector('.titulo-categorias');
            tituloCategoria.innerHTML = '🎯 Selecciona una Categoría';
            
            // Mostrar botones de categorías y botón regresar original
            const botonesDiv = document.querySelector('.botones-categorias');
            const botonRegresar = document.querySelector('.boton-regresar');
            
            botonesDiv.style.display = 'grid';
            botonRegresar.style.display = 'block';
        }

        // funcion para seleccionar tema y comenzar trivia
        function seleccionarTema(idTema, nombreTema, idCategoria, nombreCategoria) {
            console.log(`Tema seleccionado: ${nombreTema} (ID: ${idTema}) en categoría ${nombreCategoria}`);
            
            // Ocultar la selección de temas
            const loadingTemas = document.querySelector('.loading-temas');
            const contenedorTemas = document.querySelector('.botones-temas');
            const botonRegresarCategorias = document.querySelector('.boton-regresar-categorias');
            const tituloCategoria = document.querySelector('.titulo-categorias');
            
            if (loadingTemas) loadingTemas.style.display = 'none';
            if (contenedorTemas) contenedorTemas.style.display = 'none';
            if (botonRegresarCategorias) botonRegresarCategorias.style.display = 'none';
            
            // Actualizar título para mostrar el tema seleccionado
            tituloCategoria.innerHTML = `🎮 ${nombreCategoria} - ${nombreTema}`;
            
            // Crear contenedor para la trivia
            const contenedorTrivia = document.createElement('div');
            contenedorTrivia.className = 'contenedor-trivia';
            contenedorTrivia.innerHTML = `
                <div class="loading-preguntas">
                    <div class="spinner"></div>
                    <p>Cargando preguntas...</p>
                </div>
                <div class="juego-trivia" style="display: none;">
                    <div class="info-juego">
                        <div class="contador-pregunta">
                            <span class="pregunta-actual">1</span> / <span class="total-preguntas">0</span>
                        </div>
                        <div class="temporizador">
                            ⏱️ <span class="tiempo-transcurrido">00:00</span>
                        </div>
                        <div class="puntos-acumulados">
                            Puntos: <span class="puntos">0</span>
                        </div>
                    </div>
                    <div class="pregunta-contenedor">
                        <h4 class="texto-pregunta"></h4>
                        <div class="respuestas-contenedor"></div>
                    </div>
                    <div class="controles-juego">
                        <button class="boton-siguiente" onclick="siguientePregunta()" style="display: none;">
                            Siguiente Pregunta →
                        </button>
                        <button class="boton-terminar" onclick="terminarTrivia()" style="display: none;">
                            Terminar Trivia
                        </button>
                    </div>
                </div>
                <button class="boton-regresar-tema" onclick="regresarATemas(${idCategoria}, '${nombreCategoria}')">
                    ← Regresar a Temas
                </button>
            `;
            
            // Insertar contenedor de trivia
            const contenedorCategorias = document.querySelector('.contenedor-categorias');
            contenedorCategorias.appendChild(contenedorTrivia);
            
            // Cargar preguntas del tema
            cargarPreguntas(idTema, idCategoria, nombreTema, nombreCategoria);
        }

        // Variables globales para el juego
        let preguntasTrivia = [];
        let preguntaActual = 0;
        let puntosAcumulados = 0;
        let respuestaSeleccionada = null;
        let tiempoInicio = null;
        let tiempoFinal = null;
        let temporizadorIntervalo = null;

        // funcion para cargar preguntas del tema seleccionado
        function cargarPreguntas(idTema, idCategoria, nombreTema, nombreCategoria) {
            const formData = new FormData();
            formData.append('tema_id', idTema);
            formData.append('categoria_id', idCategoria);
            
            fetch('cargarPreguntas.php', {
                method: 'POST',
                body: formData
            })
                .then(response => response.json())
                .then(data => {
                    const loadingDiv = document.querySelector('.loading-preguntas');
                    const juegoDiv = document.querySelector('.juego-trivia');
                    
                    if (data.status === 'success') {
                        // Ocultar loading
                        loadingDiv.style.display = 'none';
                        
                        // Configurar datos del juego
                        preguntasTrivia = data.data;
                        preguntaActual = 0;
                        puntosAcumulados = 0;
                        tiempoInicio = null;
                        tiempoFinal = null;
                        
                        // Actualizar contador total de preguntas
                        document.querySelector('.total-preguntas').textContent = preguntasTrivia.length;
                        
                        // Mostrar juego y cargar primera pregunta
                        juegoDiv.style.display = 'block';
                        mostrarPregunta();
                        
                    } else {
                        loadingDiv.innerHTML = `
                            <div class="error-loading">
                                <p>❌ ${data.message}</p>
                                <p>No se pudieron cargar las preguntas</p>
                            </div>
                        `;
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    const loadingDiv = document.querySelector('.loading-preguntas');
                    loadingDiv.innerHTML = `
                        <div class="error-loading">
                            <p>❌ Error de conexión</p>
                            <p>No se pudieron cargar las preguntas</p>
                        </div>
                    `;
                });
        }

        // funcion para mostrar la pregunta actual
        function mostrarPregunta() {
            const pregunta = preguntasTrivia[preguntaActual];
            
            // Iniciar temporizador en la primera pregunta
            if (preguntaActual === 0 && tiempoInicio === null) {
                tiempoInicio = new Date();
                iniciarTemporizador();
                console.log('⏱️ Temporizador iniciado:', tiempoInicio);
            }
            
            // Actualizar contador de pregunta
            document.querySelector('.pregunta-actual').textContent = preguntaActual + 1;
            document.querySelector('.puntos').textContent = puntosAcumulados;
            
            // Mostrar texto de la pregunta
            document.querySelector('.texto-pregunta').textContent = pregunta.texto_pregunta;
            
            // Crear botones de respuesta
            const respuestasContainer = document.querySelector('.respuestas-contenedor');
            respuestasContainer.innerHTML = '';
            
            pregunta.respuestas.forEach((respuesta, index) => {
                const botonRespuesta = document.createElement('button');
                botonRespuesta.className = 'boton-respuesta';
                botonRespuesta.textContent = respuesta.texto_respuesta;
                botonRespuesta.onclick = () => seleccionarRespuesta(index, respuesta.es_correcta);
                
                respuestasContainer.appendChild(botonRespuesta);
            });
            
            // Resetear estado de respuesta
            respuestaSeleccionada = null;
            document.querySelector('.boton-siguiente').style.display = 'none';
            document.querySelector('.boton-terminar').style.display = 'none';
        }

        // funcion para seleccionar una respuesta
        function seleccionarRespuesta(indiceRespuesta, esCorrecta) {
            if (respuestaSeleccionada !== null) return; // Ya se seleccionó una respuesta
            
            respuestaSeleccionada = indiceRespuesta;
            const botonesRespuesta = document.querySelectorAll('.boton-respuesta');
            
            // Si es la última pregunta, detener el temporizador
            if (preguntaActual === preguntasTrivia.length - 1) {
                tiempoFinal = new Date();
                detenerTemporizador();
                console.log('⏱️ Temporizador detenido:', tiempoFinal);
            }
            
            // Marcar todas las respuestas y mostrar cuál es correcta
            preguntasTrivia[preguntaActual].respuestas.forEach((respuesta, index) => {
                const boton = botonesRespuesta[index];
                
                if (respuesta.es_correcta) {
                    boton.classList.add('respuesta-correcta');
                } else if (index === indiceRespuesta) {
                    boton.classList.add('respuesta-incorrecta');
                } else {
                    boton.classList.add('respuesta-no-seleccionada');
                }
            });
            
            // Actualizar puntos si es correcta
            if (esCorrecta) {
                puntosAcumulados += preguntasTrivia[preguntaActual].puntos;
                document.querySelector('.puntos').textContent = puntosAcumulados;
            }
            
            // Mostrar botón para continuar
            if (preguntaActual < preguntasTrivia.length - 1) {
                document.querySelector('.boton-siguiente').style.display = 'inline-block';
            } else {
                document.querySelector('.boton-terminar').style.display = 'inline-block';
            }
        }

        // funcion para avanzar a la siguiente pregunta
        function siguientePregunta() {
            preguntaActual++;
            mostrarPregunta();
        }

        // funcion para iniciar el temporizador
        function iniciarTemporizador() {
            temporizadorIntervalo = setInterval(actualizarTemporizador, 1000);
            // Agregar clase para animación
            const temporizadorElement = document.querySelector('.temporizador');
            if (temporizadorElement) {
                temporizadorElement.classList.add('activo');
            }
        }

        // funcion para actualizar el display del temporizador
        function actualizarTemporizador() {
            if (tiempoInicio) {
                const tiempoActual = new Date();
                const tiempoTranscurrido = Math.floor((tiempoActual - tiempoInicio) / 1000);
                const minutos = Math.floor(tiempoTranscurrido / 60);
                const segundos = tiempoTranscurrido % 60;
                
                const tiempoFormateado = `${minutos.toString().padStart(2, '0')}:${segundos.toString().padStart(2, '0')}`;
                document.querySelector('.tiempo-transcurrido').textContent = tiempoFormateado;
            }
        }

        // funcion para detener el temporizador
        function detenerTemporizador() {
            if (temporizadorIntervalo) {
                clearInterval(temporizadorIntervalo);
                temporizadorIntervalo = null;
            }
            // Quitar clase de animación
            const temporizadorElement = document.querySelector('.temporizador');
            if (temporizadorElement) {
                temporizadorElement.classList.remove('activo');
            }
        }

        // funcion para calcular el tiempo total transcurrido
        function calcularTiempoTotal() {
            if (tiempoInicio && tiempoFinal) {
                return Math.floor((tiempoFinal - tiempoInicio) / 1000);
            }
            return 0;
        }

        // funcion para terminar la trivia
        function terminarTrivia() {
            // Asegurar que el temporizador esté detenido
            detenerTemporizador();
            
            const tiempoTotalSegundos = calcularTiempoTotal();
            const minutosTotales = Math.floor(tiempoTotalSegundos / 60);
            const segundosTotales = tiempoTotalSegundos % 60;
            const tiempoFormateado = `${minutosTotales}:${segundosTotales.toString().padStart(2, '0')}`;
            
            const porcentaje = ((puntosAcumulados / (preguntasTrivia.length * 10)) * 100).toFixed(1);
            let mensaje = `🎉 ¡Trivia Completada! 🎉\n\n`;
            mensaje += `📊 Puntuación: ${puntosAcumulados} / ${preguntasTrivia.length * 10} puntos\n`;
            mensaje += `📈 Porcentaje: ${porcentaje}%\n`;
            mensaje += `⏱️ Tiempo total: ${tiempoFormateado}\n\n`;
            
            if (porcentaje >= 80) {
                mensaje += `🏆 ¡Excelente! Eres un experto en este tema.`;
            } else if (porcentaje >= 60) {
                mensaje += `👍 ¡Bien hecho! Tienes un buen conocimiento.`;
            } else if (porcentaje >= 40) {
                mensaje += `📚 No está mal, pero podrías estudiar un poco más.`;
            } else {
                mensaje += `💪 Sigue practicando, ¡puedes mejorar!`;
            }
            
            alert(mensaje);
            
            // Agregar a las estadísticas con el tiempo real
            agregarPuntosTiempo(puntosAcumulados, tiempoFormateado);
            
            // Regresar al panel principal
            regresarPanelPrincipal();
        }

        // funcion para regresar a la seleccion de temas
        function regresarATemas(idCategoria, nombreCategoria) {
            // Detener temporizador si está activo
            detenerTemporizador();
            
            // Eliminar contenedor de trivia
            const contenedorTrivia = document.querySelector('.contenedor-trivia');
            if (contenedorTrivia) {
                contenedorTrivia.remove();
            }
            
            // Restaurar título
            const tituloCategoria = document.querySelector('.titulo-categorias');
            tituloCategoria.innerHTML = `🎯 ${nombreCategoria} - Selecciona un Tema`;
            
            // Mostrar elementos de selección de temas
            const loadingTemas = document.querySelector('.loading-temas');
            const contenedorTemas = document.querySelector('.botones-temas');
            const botonRegresarCategorias = document.querySelector('.boton-regresar-categorias');
            
            if (loadingTemas) loadingTemas.style.display = 'block';
            if (contenedorTemas) contenedorTemas.style.display = 'grid';
            if (botonRegresarCategorias) botonRegresarCategorias.style.display = 'block';
            
            // Recargar temas
            cargarTemas(idCategoria, nombreCategoria);
        }

        // funcion para agregar monedas a la lista
        function agregarMoneda(cantidad) {
            const listaMonedas = document.getElementById('lista-monedas');
            const itemVacio = listaMonedas.querySelector('.lista-vacia');
            
            if (itemVacio) {
                itemVacio.remove();
            }
            
            const nuevoItem = document.createElement('li');
            nuevoItem.innerHTML = `💰 +${cantidad} monedas`;
            listaMonedas.appendChild(nuevoItem);
        }

        // funcion para agregar puntos y tiempo a la lista
        function agregarPuntosTiempo(puntos, tiempo) {
            const listaPuntosTiempo = document.getElementById('lista-puntos-tiempo');
            const itemVacio = listaPuntosTiempo.querySelector('.lista-vacia');
            
            if (itemVacio) {
                itemVacio.remove();
            }
            
            const nuevoItem = document.createElement('li');
            nuevoItem.innerHTML = `⭐ ${puntos} pts - ⏱️ ${tiempo}s`;
            listaPuntosTiempo.appendChild(nuevoItem);
        }

        // funcion para descargar estadisticas
        function descargarEstadisticas() {
            console.log('Descargando estadísticas...');
            
            // Crear contenido del archivo
            const estadisticas = {
                usuario: document.getElementById('username-display').textContent,
                fecha: new Date().toLocaleDateString(),
                monedas: Array.from(document.querySelectorAll('#lista-monedas li:not(.lista-vacia)')).map(li => li.textContent),
                puntosTiempo: Array.from(document.querySelectorAll('#lista-puntos-tiempo li:not(.lista-vacia)')).map(li => li.textContent)
            };
            
            const contenido = `
ESTADÍSTICAS DE TRIVIA - REICHMIND
=====================================
Usuario: ${estadisticas.usuario}
Fecha: ${estadisticas.fecha}

MONEDAS GANADAS:
${estadisticas.monedas.length > 0 ? estadisticas.monedas.join('\n') : 'No hay registros'}

PUNTOS Y TIEMPO:
${estadisticas.puntosTiempo.length > 0 ? estadisticas.puntosTiempo.join('\n') : 'No hay registros'}

Generado automáticamente por ReichMind
            `.trim();
            
            // Crear y descargar archivo
            const blob = new Blob([contenido], { type: 'text/plain' });
            const url = window.URL.createObjectURL(blob);
            const a = document.createElement('a');
            a.href = url;
            a.download = `estadisticas_trivia_${new Date().toISOString().split('T')[0]}.txt`;
            document.body.appendChild(a);
            a.click();
            document.body.removeChild(a);
            window.URL.revokeObjectURL(url);
            
            alert('📊 Estadísticas descargadas correctamente');
        }
    </script>
</body>
</html>
