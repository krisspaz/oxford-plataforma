# ADR 001: Refactorización Arquitectónica del Servicio de IA

## Estado
Aceptado

## Contexto
El servicio de IA (`ai_service`) creció como un monolito en `main.py`, conteniendo lógica de negocio, enrutamiento, configuración y persistencia en un solo archivo de +800 líneas. Esto dificultaba el mantenimiento, las pruebas y la escalabilidad.

## Decisión
Se decidió refactorizar la aplicación utilizando el patrón **Controller-Service-Repository** (adaptado a Flask con Blueprints):

1. **Blueprints (Rutas)**: Se separaron los endpoints en módulos temáticos bajo `routes/`:
   - `core_routes.py`: Endpoints generales y NLP.
   - `auth_routes.py`: Autenticación y JWT.
   - `schedule_routes.py`: Lógica de horarios.
   - `analytics_routes.py`: Reportes y predicciones.

2. **Servicios (Lógica)**: Se centralizó la lógica de negocio en clases especializadas (`scheduler`, `nlp_engine`, `auth_service`) instanciadas como Singletons en `services_container.py`.

3. **Configuración**: Se extrajo toda la configuración y secretos a `config.py`, leyendo de variables de entorno con `python-dotenv`.

4. **Persistencia**: Se aisló la gestión de base de datos en `database.py`.

## Consecuencias
### Positivas
- **Testabilidad**: Ahora es posible probar rutas individuales sin cargar toda la aplicación.
- **Mantenibilidad**: Archivos más pequeños y con responsabilidad única.
- **Seguridad**: Secretos fuera del código fuente.

### Negativas
- **Complejidad**: Aumentó el número de archivos y la estructura del proyecto.
- **Migración**: Requiere actualizar tests antiguos (se crearon nuevos tests de humo).
