CrudUsuarioPHP_Evidencia
Evidencia: GA7-220501096-AA5-EV04

Sistema CRUD de usuarios y gestión de citas, desarrollado con PHP y MySQL, interfaz responsiva en HTML + Tailwind CSS + JavaScript (Fetch).
Este proyecto permite registrar usuarios, iniciar sesión, asignar o cancelar citas, generar facturas y acceder a módulos personalizados por rol: cliente, empleado y administrador.
Diseñado como API híbrida, puede utilizarse tanto desde formularios web locales como desde Postman para pruebas y consumo de endpoints.

Forma parte de la evidencia GA7-AA5-EV04 del proceso formativo del SENA.

📁 Tabla de contenidos
Características

Requisitos

Instalación local

Configuración de base de datos

Estructura del proyecto

Uso general del sistema

Servicios API implementados

Dificultades y decisiones de diseño

Consideraciones para producción

Evidencia documental

Autor

Licencia

🚀 Características
Registro de usuarios desde formularios HTML o Postman (API híbrida).

Inicio de sesión con control de rol y manejo de sesiones PHP.

Asignación y cancelación de citas tanto desde frontend como desde Postman.

Generación y visualización de facturas asociadas a citas.

Envío de facturas por correo electrónico usando PHPMailer.

Interfaz moderna y responsiva con TailwindCSS.

CRUD completo para administradores (gestión de usuarios).

Módulos separados para cada rol: cliente, empleado, administrador.

Compatible con pruebas API REST y entorno web local.

🛠️ Requisitos
Software	Versión recomendada
PHP	8.0 o superior
MySQL / MariaDB	10.4 o superior
XAMPP (Apache + MySQL)	Última versión estable
Navegador moderno	Chrome, Firefox, Edge
Postman	Última versión

💻 Instalación local
bash
Copiar
Editar
# 1. Clonar el repositorio
git clone https://github.com/jerp1987/GA7-220501096-AA5-EV03.git

# 2. Mover la carpeta clonada a htdocs (XAMPP)
# Ejemplo en Windows:
move GA7-220501096-AA5-EV03 C:\xampp\htdocs\CrudUsuarioPHP

# 3. Iniciar XAMPP y levantar Apache y MySQL

# 4. Crear la base de datos usando el script incluido en el repositorio

# 5. Abrir en navegador: http://localhost/CrudUsuarioPHP/index.html
📊 Configuración de base de datos
Crear base de datos crudusuarios.

Ejecutar el script script_bd.sql incluido en el proyecto para generar las tablas:

usuarios

citas

facturas

cancelaciones

📂 Estructura del proyecto
bash
Copiar
Editar
CrudUsuarioPHP/
├── index.html               # Registro e inicio de sesión
├── registrar.php            # API de registro (formulario y Postman)
├── login.php                # API de login (formulario y Postman)
├── logout.php               # Cierre de sesión
├── dashboard.php            # Panel general
├── modulo_cliente.php       # Vista cliente
├── modulo_empleado.php      # Vista empleado
├── modulo_administrador.php # Vista administrador + CRUD
├── asignar_cita.php         # Formulario agendar cita
├── cancelar_cita.php        # Formulario cancelar cita
├── citas.php                # API citas (asignar/cancelar)
├── factura.php              # Generar/ver factura
├── listar_facturas.php      # Listar facturas
├── listar_citas.php         # Listar citas
├── conexion.php             # Conexión MySQL
├── script_bd.sql            # Script SQL BD
📖 Uso general del sistema
Registrar un usuario desde formulario o Postman (POST registrar.php).

Iniciar sesión con correo y contraseña.

El sistema redirige según el rol: cliente, empleado o administrador.

Cliente → puede agendar y cancelar citas.

Empleado → puede gestionar citas.

Administrador → gestiona usuarios y visualiza todas las citas.

🔄 Servicios API implementados
POST /registrar.php → Registro de usuario.

POST /login.php → Inicio de sesión.

POST /citas.php → Asignar cita.

DELETE /citas.php?id=ID → Cancelar cita.

POST /factura.php → Generar factura.

GET /listar_facturas.php → Listar facturas.

GET /listar_citas.php → Listar citas.

⚠️ Dificultades y decisiones de diseño
Se presentaron errores 409 Conflict y SyntaxError al manejar respuestas desde PHP, resueltos enviando siempre respuestas JSON válidas en modo API.

Hubo problemas con roles y sesiones, se corrigió la validación para que cada módulo muestre solo funciones permitidas.

Inicialmente el sistema solo funcionaba como aplicación web; se decidió hacerlo híbrido para permitir pruebas y consumo vía Postman.

El modo híbrido facilita integración futura con aplicaciones móviles o frontend en React/Vue.

🏭 Consideraciones para producción
Migrar credenciales de base de datos a variables de entorno .env.

Implementar HTTPS y tokens JWT para sesiones seguras.

Añadir validaciones backend más estrictas y sanitización de entrada.

Configurar control de errores y logging.

Optimizar consultas SQL y agregar índices.

Proteger rutas API con autenticación y control de CORS.

📄 Evidencia documental
✅ Video de pruebas en Postman.

✅ Capturas de pruebas en navegador.

✅ Documento explicativo (Word).

✅ Archivo con endpoints documentados.

✅ Paquete ZIP: JHONN_ROMERO_AA5_EV03.zip.

👤 Autor
Jhonn Edison Romero Peña
Repositorio del proyecto

📚 Licencia
Proyecto con fines educativos, parte del proceso formativo SENA. Sin licencia comercial.
