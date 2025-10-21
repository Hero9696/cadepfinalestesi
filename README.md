# Guía de Instalación del Proyecto

Este documento proporciona una guía paso a paso para instalar todas las dependencias necesarias y ejecutar el proyecto en un entorno de desarrollo local.

## Requisitos Previos

Asegúrate de tener instalado el siguiente software en tu sistema antes de comenzar:

- PHP (se recomienda la versión 8.1 o superior)
- Composer (gestor de dependencias para PHP)
- Node.js (se recomienda la versión 18 o superior)
- npm o yarn (gestor de paquetes de Node.js)

## Pasos de Instalación

Sigue estos pasos en orden para configurar tu entorno de desarrollo.

### 1. Clonar el Repositorio

Primero, clona el repositorio del proyecto en tu máquina local.

```bash
git clone <URL_DEL_REPOSITORIO>
cd <NOMBRE_DEL_PROYECTO>
```

### 2. Instalar Dependencias de PHP

Usa Composer para instalar todas las dependencias de backend requeridas por el proyecto.

```bash
composer install
```

### 3. Instalar Dependencias de JavaScript

Usa `npm` o `yarn` para instalar las dependencias del frontend.

```bash
npm install
```

o si prefieres usar `yarn`:

```bash
yarn install
```

### 4. Configuración del Entorno

Copia el archivo de ejemplo `.env.example` para crear tu propio archivo de configuración de entorno `.env`.

```bash
cp .env.example .env
```

Luego, genera la clave de la aplicación, que es necesaria para la encriptación y seguridad.

```bash
php artisan key:generate
```

Abre el archivo `.env` y configura las variables de la base de datos (`DB_DATABASE`, `DB_USERNAME`, `DB_PASSWORD`, etc.) según tu configuración local.

### 5. Ejecutar las Migraciones

Una vez configurada la base de datos, ejecuta las migraciones para crear la estructura de tablas.

```bash
php artisan migrate
```

### 6. Compilar Assets y Ejecutar el Servidor

Finalmente, compila los assets del frontend y ejecuta el servidor de desarrollo de Laravel.

```bash
npm run dev
```

En otra terminal, inicia el servidor:

```bash
php artisan serve
```

¡Y listo! Ahora deberías poder acceder al proyecto en tu navegador desde la URL que te proporcione el comando `serve` (normalmente `http://127.0.0.1:8000`).
