# Doctor Appointment App – Configuración Inicial

Este proyecto corresponde a la práctica de configuración inicial en Laravel, donde se establecieron los elementos base para su desarrollo.

## Configuraciones realizadas

### 1. Base de datos (MySQL)
- Se creó la base de datos `laravel_db`.
- Se generó el usuario `laraveluser` con privilegios completos.
- Se configuró la conexión en el archivo `.env`.
- Se ejecutaron las migraciones para crear las tablas iniciales.

### 2. Zona horaria
- La aplicación está configurada para la zona horaria `America/Merida`.
- Esto garantiza que todas las fechas y horas mostradas en la app sean correctas según la región.

### 3. Idioma
- El idioma principal de la aplicación es Español (`APP_LOCALE=es`).
- Se definió Inglés (`APP_FALLBACK_LOCALE=en`) como idioma de respaldo.

### 4. Foto de perfil
- Se habilitó la funcionalidad de subir foto de perfil usando:
```php
'features' => [
    Features::profilePhotos(),
    Features::accountDeletion(),
],
