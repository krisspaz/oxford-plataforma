# Sistema Oxford - Credenciales de Acceso

**URL:** http://localhost:8000

## Usuarios del Sistema

| Rol | Email | Contraseña | Funciones |
|-----|-------|------------|-----------|
| **Administrador** | admin@oxford.edu | oxford123 | Acceso total al sistema |
| **Director** | director@oxford.edu | oxford123 | Gestión académica, reportes, usuarios |
| **Coordinación** | coordination@oxford.edu | oxford123 | Gestión académica, materias, docentes, horarios |
| **Docente** | teacher@oxford.edu | oxford123 | Carga de notas, tareas, mis alumnos, mi horario |
| **Secretaría** | secretary@oxford.edu | oxford123 | Inscripciones, matriculación, familias, pagos |
| **Contabilidad** | accountant@oxford.edu | oxford123 | Finanzas, corte del día, comprobantes |
| **Informática** | informatics@oxford.edu | oxford123 | Usuarios, roles, configuración del sistema |
| **Estudiante** | student@oxford.edu | oxford123 | Mis notas, mi horario, estado de cuenta |
| **Padre de Familia** | parent@oxford.edu | oxford123 | Ver notas y estado de cuenta de sus hijos |

---

## Variables de Entorno (Docker)

```bash
POSTGRES_PASSWORD=oxford2024
APP_SECRET=oxford_secret_key_2024
```

## Comandos Útiles

```bash
# Iniciar servidor
POSTGRES_PASSWORD=oxford2024 APP_SECRET=oxford_secret_key_2024 docker-compose up -d

# Ver logs
docker-compose logs -f

# Ejecutar migraciones
docker-compose exec backend php bin/console doctrine:migrations:migrate

# Cargar datos de prueba
docker-compose exec backend php bin/console doctrine:fixtures:load

# Reiniciar servicios
docker-compose restart
```

---

> ⚠️ **Importante:** Cambiar estas credenciales en producción.
