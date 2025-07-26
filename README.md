#  Universidad Tecnológica de Panamá  
### Facultad de Ingeniería en Sistemas Computacionales  
### Licenciatura en Desarrollo y Gestión de Software  
### Asignatura: Desarrollo de Software 7  

## 🧠 Sistema de Gestión de Trivia - *ReichMind*

**Autores:**  
- 👩‍💻 Beitia Bethel – *Cédula: 4-828-2349*  
- 👨‍💻 Juárez Edgar – *Cédula: 8-964-1614*  

---

## 📋 Pasos para ejecutar el sistema en un entorno local

1. 📁 **Clona o copia el proyecto en la carpeta `htdocs` de XAMPP.**  
   - Ejemplo: `C:/xampp/htdocs/PaginasPHP/trivia_semestralBeitiaJuarez`

2. 🛠️ **Importa la base de datos:**  
   - Abre [phpMyAdmin](http://localhost/phpmyadmin)  
   - Crea una base de datos (por ejemplo: `trivia_db`)  
   - Importa el archivo SQL ubicado en: `bd/trivitabckup.sql`

3. ⚙️ **Configura la conexión a la base de datos:**  
   - Abre el archivo `bd/conexion.php`  
   - Ajusta los datos según tu configuración local (host, usuario, contraseña y nombre de la BD)

4. 🔑 **Credenciales por defecto:**  
   - El sistema incluye usuarios de prueba. Revisa las tablas `administradores`, `colaboradores` y `usuarios` para obtener credenciales.

5. 🌐 **Accede al sistema desde el navegador:**  
   - Ingresa a `http://localhost/PaginasPHP/trivia_semestralBeitiaJuarez/`  
   - Utiliza las credenciales de prueba según el rol (ver abajo).

---

## 🔐 Accesos rápidos

### 👨‍💼 Administrador  
- Correo: `edgar.juarez@reichmind.com`  
- Contraseña: `password`  
- ⚠️ **Importante:** Si agrega un nuevo administrador manualmente, debe utilizar `migrar_passwords.php` para hashear la contraseña correctamente.

### 👨‍🔧 Colaborador  
- Correo: `adrian.quijada@reichmind.com`  
- Contraseña: `123456`  
- Solo puede ser creado por un administrador.

### 🎮 Usuario del juego  
- Correo: `at@gmail.com`  
- Contraseña: `123456`  
- Puede ser creado por un administrador o colaborador.

---

## 🧪 Recomendaciones para pruebas funcionales

- 🔌 Verifica que los servicios de **Apache y MySQL** estén activos.
- ✅ Inicia sesión antes de navegar por el sistema para un funcionamiento completo.
- 🧪 Prueba funcionalidades clave: registro, login, creación/gestión de preguntas, usuarios y temas.
- 👀 Asegúrate de que los módulos se visualicen correctamente y que los datos se actualicen dinámicamente.
- 🌐 Utiliza navegadores modernos (Chrome, Firefox, Edge) para evitar incompatibilidades.
- 🐞 En caso de errores, revisa:
  - La consola del navegador (F12)
  - Los logs de PHP (archivo `php_error.log` o desde XAMPP)

---

## 🔍 Sugerencia visual

Para una mejor experiencia y poder ver toda la interfaz con claridad, se recomienda **alejar la vista con la lupa del navegador (Ctrl + rueda del mouse)**.

---

> 💼 **Desarrollado por el equipo ReichMind**  
> Para soporte o dudas, contactar a los creadores bethel.beitia@utp.ac.pa y edgar.juarez2@utp.ac.pa del sistema.
