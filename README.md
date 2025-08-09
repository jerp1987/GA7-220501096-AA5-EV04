# 📁 CrudUsuarioPHP_Evidencia — GA7-220501096-AA5-EV04

**Repositorio del Proyecto:**  
🔗 [https://github.com/jerp1987/GA7-220501096-AA5-EV04](https://github.com/jerp1987/GA7-220501096-AA5-EV04)

Este repositorio contiene el código fuente completo del sistema híbrido desarrollado como parte de la evidencia **GA7-220501096-AA5-EV04** del proceso formativo **SENA**.

El proyecto implementa un sistema CRUD de usuarios y gestión de citas con **PHP** y **MySQL**, interfaz responsiva en **HTML + Tailwind CSS + JavaScript (Fetch)**, y soporte API híbrido para trabajar tanto desde formularios web como desde **Postman**.

---

## 📁 Tabla de contenidos

1. [Características](#-características)  
2. [Requisitos](#-requisitos)  
3. [Instalación local](#-instalación-local)  
4. [Configuración de base de datos](#-configuración-de-base-de-datos)  
5. [Estructura del proyecto](#-estructura-del-proyecto)  
6. [Uso general del sistema](#-uso-general-del-sistema)  
7. [Servicios API implementados](#-servicios-api-implementados)  
8. [Dificultades y decisiones de diseño](#-dificultades-y-decisiones-de-diseño)  
9. [Consideraciones para producción](#-consideraciones-para-producción)  
10. [Evidencia documental](#-evidencia-documental)  
11. [Autor](#-autor)  
12. [Licencia](#-licencia)  

---

## 🚀 Características

- Registro de usuarios desde formularios HTML o Postman (**API híbrida**).
- Inicio de sesión con control de rol y manejo de sesiones PHP.
- Asignación y cancelación de citas desde frontend o Postman.
- Generación y visualización de facturas asociadas a citas.
- Envío de facturas por correo electrónico con **PHPMailer**.
- Interfaz moderna y responsiva con **TailwindCSS**.
- CRUD completo para administradores (gestión de usuarios).
- Módulos separados para cada rol: cliente, empleado, administrador.
- Compatible con pruebas API REST y entorno web local.

---

## 🛠️ Requisitos

| Software               | Versión recomendada    |
| ---------------------- | ---------------------- |
| PHP                    | 8.0 o superior         |
| MySQL / MariaDB        | 10.4 o superior        |
| XAMPP (Apache + MySQL) | Última versión estable |
| Navegador moderno      | Chrome, Firefox, Edge  |
| Postman                | Última versión         |

---

## 💻 Instalación local

```bash
# 1. Clonar el repositorio
git clone https://github.com/jerp1987/GA7-220501096-AA5-EV04.git

# 2. Mover la carpeta clonada a htdocs (XAMPP)
# Ejemplo en Windows:
move GA7-220501096-AA5-EV04 C:\xampp\htdocs\CrudUsuarioPHP

# 3. Iniciar XAMPP y levantar Apache y MySQL

# 4. Crear la base de datos (ver script incluido en el repositorio)

# 5. Abrir en navegador:
http://localhost/CrudUsuarioPHP/index.html
📊 Configuración de base de datos
Crear base de datos crudusuarios.

Ejecutar el script script_bd.sql incluido en el repositorio para generar las tablas:

usuarios

citas

facturas

cancelaciones

📂 Estructura del proyecto
graphql
Copiar
Editar
CrudUsuarioPHP/
├── index.html               # Registro e inicio de sesión
├── registrar.php            # API de registro
├── login.php                # API de login
├── logout.php               # Cierre de sesión
├── dashboard.php            # Panel principal
├── modulo_cliente.php       # Vista cliente
├── modulo_empleado.php      # Vista empleado
├── modulo_administrador.php # Vista administrador + CRUD usuarios
├── asignar_cita.php         # Formulario agendar cita
├── cancelar_cita.php        # Formulario cancelar cita
├── citas.php                # API citas (asignar/cancelar)
├── factura.php              # Generar/ver factura
├── listar_facturas.php      # Listar facturas
├── listar_citas.php         # Listar citas
├── conexion.php             # Conexión MySQL
├── script_bd.sql            # Script SQL base de datos
📖 Uso general del sistema
Registrar usuario desde formulario o Postman (POST registrar.php).

Iniciar sesión con correo y contraseña.

El sistema redirige según rol:

Cliente → Agendar y cancelar citas.

Empleado → Gestionar citas.

Administrador → CRUD de usuarios y ver citas.

Facturas generadas disponibles en pantalla y envío por correo.

🔄 Servicios API implementados
POST /registrar.php → Registro de usuario.

POST /login.php → Inicio de sesión.

POST /citas.php → Asignar cita.

DELETE /citas.php?id=ID → Cancelar cita.

POST /factura.php → Generar factura.

GET /listar_facturas.php → Listar facturas.

GET /listar_citas.php → Listar citas.

⚠️ Dificultades y decisiones de diseño
Errores 409 Conflict y SyntaxError solucionados enviando siempre respuestas JSON válidas.

Validación de roles para mostrar únicamente funciones autorizadas.

Decisión de diseño: migrar a API híbrida para compatibilidad con Postman y formularios HTML.

Ventaja en producción: integración futura con apps móviles o frontend React/Vue.

🏭 Consideraciones para producción
Mover credenciales a variables de entorno .env.

Implementar HTTPS y autenticación JWT.

Validaciones backend estrictas y sanitización de entradas.

Logging y manejo de errores controlado.

Optimización de consultas SQL e índices.

Protección de rutas API con autenticación y CORS.

📄 Evidencia documental
✅ Video de pruebas en Postman.

✅ Capturas de pruebas en navegador.

✅ Documento explicativo (Word).

✅ Archivo con endpoints documentados.

✅ Paquete ZIP: JHONN_ROMERO_AA5_EV04.zip.

👤 Autor
Jhonn Edison Romero Peña
🔗 Repositorio del proyecto

📚 Licencia
Proyecto con fines educativos — Proceso formativo SENA.
Sin licencia comercial.