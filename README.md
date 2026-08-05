# Elite Moda

Sistema integral de gestión comercial (punto de venta, inventario, compras, clientes y finanzas) desarrollado para una tienda de ropa. Proyecto integrador académico desarrollado bajo metodología Scrum.

## Descripción

Elite Moda cubre el ciclo operativo completo de una tienda de retail: catálogo e inventario con variantes de producto, compras a proveedores, ventas en punto de venta (TPV) con soporte de pagos combinados, devoluciones respaldadas por Notas de Crédito, gestión de clientes y crédito, control de caja, gastos, y reportes gerenciales.

## Stack Tecnológico

| Componente | Tecnología |
|---|---|
| Backend | Laravel 13 (PHP 8.3) |
| Base de datos | MySQL 8.0 |
| Frontend | Blade + Bootstrap 5 |
| Build de assets | Vite |
| Gráficos | Chart.js |
| Selects dinámicos | Tom Select |
| Contenedores | Docker Compose (PHP-FPM, Nginx, MySQL) |
| Infraestructura | AWS EC2 (Ubuntu) |

## Módulos Principales

- **Catálogo:** categorías, atributos flexibles (talla, color, etc.), productos y variantes, descuentos
- **Inventario:** stock por almacén, movimientos, ajustes con flujo de aprobación
- **Compras:** proveedores, órdenes de compra, recepciones de mercancía
- **Ventas:** punto de venta (TPV), pagos combinados, factura térmica 80mm, comprobantes fiscales
- **Devoluciones:** flujo completo con inspección por línea y generación automática de Nota de Crédito
- **Clientes:** gestión de clientes, grupos, sistema de crédito
- **Cuentas por Cobrar:** seguimiento de ventas a crédito y registro de abonos
- **Caja:** apertura/cierre de sesión, cuadre diario, caja chica
- **Gastos:** gastos variables y fijos recurrentes
- **Personal:** gestión de empleados
- **Reportes:** ventas, inventario, compras, crédito y cobros, con exportación a CSV y PDF
- **Administración:** usuarios, roles y permisos granulares, sucursales, configuración general

## Requisitos Previos

- PHP >= 8.3
- Composer
- Node.js y npm
- MySQL 8.0
- Extensión PHP: `pdo_mysql`, `mbstring`, `openssl`, `gd`, `curl`, `fileinfo`, `zip`

## Instalación Local

```bash
# Clonar el repositorio
git clone https://github.com/EsmeryVG/elite_moda.git
cd elite_moda

# Instalar dependencias de PHP
composer install

# Instalar dependencias de Node
npm install

# Configurar variables de entorno
cp .env.example .env
php artisan key:generate
```

Edita el archivo `.env` con los datos de tu conexión local a MySQL.


```bash
# Ejecutar migraciones y seeders
php artisan migrate --seed

# Compilar assets de frontend
npm run build
# o, para desarrollo con recarga en caliente:
npm run dev

# Levantar el servidor de desarrollo
php artisan serve
```

El sistema quedará disponible en `http://127.0.0.1:8000`.

## Despliegue en Producción

El sistema está preparado para desplegarse mediante Docker Compose, con tres servicios: `app` (PHP-FPM), `nginx` (servidor web) y `mysql` (base de datos).


## Autoría

**Esmery Vásquez García**
