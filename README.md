#  Elite Moda - Sistema de Gestión

Proyecto desarrollado en Laravel 11 para la gestión de productos, categorías y variantes.

##  Requisitos previos

Antes de empezar, asegúrate de tener instalado:
* **XAMPP** (con PHP 8.2 o superior).
* **Composer**.
* **Node.js & NPM**.
* **Git**.

---

##  Pasos para la instalación inicial

Si es la primera vez que descargas el proyecto, sigue estos pasos en orden:

### 1. Clonar el repositorio
```bash
git clone https://github.com/EsmeryVG/elite_moda.git
cd elite_moda

2. Instalar dependencias
Instala los paquetes de PHP y las librerías de Frontend:

Bash
composer install
npm install
3. Configurar variables de entorno
Copia el archivo de ejemplo y genera la clave de seguridad:

Bash
cp .env.example .env
php artisan key:generate
4. Configuración de Base de Datos
Abre XAMPP e inicia Apache y MySQL.

Entra a phpMyAdmin y crea una base de datos llamada elite_moda.

Abre el archivo .env en VS Code y asegúrate de que los datos coincidan:

DB_DATABASE=elite_moda

DB_USERNAME=root

DB_PASSWORD= (vacío por defecto en XAMPP)

5. Migraciones
Crea la estructura de tablas inicial:

Bash
php artisan migrate
Ejecución del Proyecto
Para trabajar, debes mantener dos terminales abiertas:

Servidor Laravel:

Bash
php artisan serve
Compilador Vite (Estilos y JS):

Bash
npm run dev
Accede al sistema
