# Tu Voto Cipote Back‑Office

Sistema de gestión de informacion publica construido con **Laravel 12** y **MySQL 8**. Permite a los operadores registrar y actualizar candidatos, planillas y catálogos; a su vez publica una **API REST** (v1.6) que terceros —por ejemplo un bot— pueden consumir para obtener los datos en formato JSON fácilmente deserializable.

---

## 🎯 Propósito

1. **Centralizar** la administración del padrón de candidatos y sus metadatos.
2. **Abstraer** la base de datos tras una API bien documentada.
3. **Facilitar** la integración de aplicaciones externas (chat‑bots, sitios, móviles) mediante peticiones HTTP estándar.

---

## ✨ Funcionalidades clave

- CRUD completo de candidatos, planillas y catálogos (partidos, cargos, movimientos).
- Importación masiva desde archivos Excel.
- Panel de control con métricas en tiempo real.
- Gestión de usuarios, roles y permisos (Spatie Permission).
- API REST protegida por clave de acceso (en cabecera `X-API-KEY`).
- Límite configurable de peticiones por minuto para evitar abuso.

---

## 🛠️ Stack

| Capa         | Tecnología |
|--------------|------------|
| Backend      | Laravel 12 (PHP ≥ 8.3) |
| Base de datos| MySQL 8 / MariaDB 10 |
| Frontend     | Blade + TailwindCSS + Alpine.js |
| Autenticación| Sesiones web + API‑Key |
| Despliegue   | Docker (nginx + php‑fpm) o Laravel Cloud |

---

## ⚙️ Instalación local (desarrolladores)

```bash
# 1. Clonar repositorio
$ git clone https://github.com/JJerryC/tuvotocipotebackoffice.git
$ cd tuvotocipotebackoffice

# 2. Instalar dependencias
$ composer install
$ npm ci --audit false && npm run build

# 3. Configurar entorno
$ cp .env.example .env
$ php artisan key:generate
# ➜ Edita .env y completa las credenciales de tu base MySQL y tu clave para la API

# 4. Migrar y poblar datos de ejemplo
$ php artisan migrate --seed

# 5. Iniciar servidor local
$ php artisan serve
```

> **Nota:** Las variables sensibles (contraseñas, claves de API, etc.) **no** se incluyen en este documento. Usa el archivo `.env` local para definirlas.

---

## 📡 API REST – resumen rápido

| Método | Endpoint | Descripción |
|--------|----------|-------------|
| GET | /ping | Respuesta de salud (`pong`) |
| GET | /candidates | Lista paginada de candidatos |
| GET | /candidates/{id} | Detalle de un candidato |
| GET | /identidad/{dni} | Búsqueda por número de identidad |
| GET | /planillas | Lista de planillas |
| GET | /planillas/{id}/foto | Foto pública de la planilla |

Todas las peticiones deben incluir la cabecera:

```http
X-API-KEY: <tu-clave>
```

Documentacion completa consultar doc: ApiCandidatos.

---

## 🤝 Contribuir

1. Haz *fork* y crea una rama (`git checkout -b feature/NuevaFeature`).
2. Realiza *commit* siguiendo [Conventional Commits](https://www.conventionalcommits.org/).
3. Ejecuta PHPStan, Pint y las pruebas unitarias antes de abrir un PR.

---

