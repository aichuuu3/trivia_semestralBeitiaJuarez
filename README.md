# Universidad Tecnológica de Panamá
# Facultad de Ingeniería en Sistemas Computacionales
# Lic. En Desarrollo y Gestión de Software
# Desarrollo de Software 7
# Sistema de Gestión de Trivia ReichMind

# Beitia Bethel 4-828-2349 y Juárez Edgar 8-964-1614

### 📋 Pasos para ejecutar el sistema en un entorno local

1. **Clona o copia el proyecto en la carpeta `htdocs` de XAMPP.**
   - Ejemplo: `C:/xampp/htdocs/PaginasPHP/trivia_semestralBeitiaJuarez`

2. **Importa la base de datos:**
   - Abre phpMyAdmin (`http://localhost/phpmyadmin`).
   - Crea una base de datos (por ejemplo, `trivia_db`).
   - Importa el archivo SQL ubicado en `bd/trivitabckup.sql`.

4. **Configura la conexión a la base de datos:**
   - Edita el archivo `bd/conexion.php` y ajusta los datos de host, usuario, contraseña y nombre de la base de datos si es necesario.

5. **Credenciales por defecto:**
   - El sistema puede incluir usuarios de prueba en la base de datos importada. Revisa los registros en las tablas `administradores`, `colaboradores` y `usuarios` para obtener credenciales de acceso.

6. **Accede al sistema:**
   - Ingresa al link desde tu navegador.
   - Utiliza las credenciales de prueba para iniciar sesión como administrador, colaborador o usuario.

7. **Puede agregar un administrador desde la base de datos**
    - Recuerde que al momento de crear la contraseña se dirija a migrar_passwords.php para poder que le haga el hash y actualize inmediatamente.
    - Si desea iniciar sesión, puede utilizar este correo edgar.juarez@reichmind.com Contraseña: password

8. **Acceder como colaborador**
    - El administrador debe de crear la cuenta o debe tener una existente
    - Si desea iniciar sesión, puede acceder como Correo: adrian.quijada@reichmind.com, Contraseña: 123456

9. **Iniciar sesión como usuario del juego**
    - El administrador o colaborador debe de crear la cuenta.
    - Si desea iniciar sesión rápidamente correo: at@gmail.com, Contraseña:123456

10. **Si planea ver toda la interfaz le sugiero que aleje con la lupa para que pueda observar detalladamente lo que se hace en el proyecto**


### 🧪 Recomendaciones para pruebas funcionales

- Verifica que los servicios de Apache y MySQL estén activos antes de iniciar el sistema.
- Tiene que iniciar sesión para poder que muestre las funcionalidades de forma fluida.
- Realiza pruebas de registro, login y gestión de preguntas, temas y usuarios desde los diferentes roles (administrador, colaborador, usuario).
- Comprueba la correcta visualización de los módulos y la actualización dinámica de los datos.
- Utiliza navegadores modernos (Chrome, Firefox, Edge) para asegurar compatibilidad visual y funcional.
- Si encuentras errores, revisa la consola del navegador y los logs de PHP para identificar problemas de conexión o permisos.

---

> **Desarrollado por el equipo ReichMind.**
> 
> Para soporte o dudas, contacta al administrador del sistema.

---

