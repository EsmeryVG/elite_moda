# Elite Moda

Sistema web desarrollado con **Laravel** y **Vite**, diseñado para la gestión de una tienda de moda.

---

## Requisitos Previos

Antes de comenzar, asegúrate de tener instalado lo siguiente:

- [PHP >= 8.1](https://www.php.net/)
- [Composer](https://getcomposer.org/)
- [Node.js & NPM](https://nodejs.org/)
- [XAMPP](https://www.apachefriends.org/) (Apache + MySQL)
- [VS Code](https://code.visualstudio.com/) *(recomendado)*

---

## Instalación y Configuración

### 1. Clonar el Repositorio

```bash
git clone https://github.com/EsmeryVG/elite_moda.git
cd elite-moda
```

---

### 2. Instalar Dependencias

Instala los paquetes de PHP y las librerías de Frontend:

```bash
composer install
npm install
```

---

### 3. Configurar Variables de Entorno

Copia el archivo de ejemplo y genera la clave de seguridad de la aplicación:

```bash
cp .env.example .env
php artisan key:generate
```

---

### 4. Configuración de Base de Datos

1. Abre **XAMPP** e inicia los servicios de **Apache** y **MySQL**.
2. Entra a [phpMyAdmin](http://localhost/phpmyadmin) y crea una base de datos llamada `elite_moda`.
3. Abre el archivo `.env` en VS Code y verifica que los datos de conexión coincidan:

```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=elite_moda
DB_USERNAME=root
DB_PASSWORD=
```

> En XAMPP, la contraseña de MySQL es **vacía** por defecto.

---

### 5. Ejecutar Migraciones

Crea la estructura inicial de tablas en la base de datos:

```bash
php artisan migrate
```

---

## Ejecución del Proyecto

Para trabajar en el proyecto, debes mantener **dos terminales abiertas** de forma simultánea:

**Terminal 1 — Servidor Laravel:**

```bash
php artisan serve
```

**Terminal 2 — Compilador Vite (Estilos y JS):**

```bash
npm run dev
```

---

## Acceso al Sistema

Una vez que ambos servidores estén corriendo, abre tu navegador y visita:

```
http://localhost:8000
```

---

## Estructura del Proyecto

```
elite-moda/
├── app/            # Lógica de la aplicación (Modelos, Controladores)
├── database/       # Migraciones y seeders
├── public/         # Archivos públicos
├── resources/      # Vistas (Blade), CSS y JS
├── routes/         # Definición de rutas
├── .env            # Variables de entorno (no subir al repositorio)
└── ...
```

---

## Tecnologías Utilizadas

| Tecnología   | Descripción                          |
|--------------|--------------------------------------|
| Laravel      | Framework PHP para el backend        |
| Vite         | Bundler para assets de frontend      |
| MySQL        | Base de datos relacional             |
| XAMPP        | Entorno de desarrollo local          |
| Blade        | Motor de plantillas de Laravel       |

---

## Licencia

Este proyecto es de uso académico/personal. Todos los derechos reservados © Elite Moda.
