# 🎓 ESPECIFICACIÓN COMPLETA - PLATAFORMA OXFORD
## Versión Final Consolidada

> **Colegio Privado Guatemala** | Ciclo Anual | 9 Roles | 35 Módulos Optimizados

---

# PARTE 1: FUNDAMENTOS

## A) NORMALIZACIÓN DE TÉRMINOS

### Glosario Oficial

| Término VIEJO | Término NUEVO | Acción |
|---------------|---------------|--------|
| Curso | ❌ **ELIMINAR** | Confundido con Nivel |
| Nivel | ✅ **NIVEL ACADÉMICO** | Preprimaria, Primaria, Básicos, Bachillerato |
| Grado | ✅ **GRADO** | 1ro, 2do, 3ro... |
| Sección | ✅ **SECCIÓN** | A, B, C... |
| Jornada | ✅ **JORNADA** | Matutina, Vespertina |
| Bimestre | ✅ **PERÍODO** | Más flexible |
| Paquete | ✅ **PLAN DE PAGO** | Claridad para padres |
| Convenio | ✅ **CONVENIO DE PAGO** | Mantener |
| Materia | ✅ **ASIGNATURA** | Término formal |

### Jerarquía Académica
```
Ciclo Escolar → Nivel Académico → Grado → Sección → Jornada
Ejemplo: 2026 → Primaria → 3ro → Sección A → Matutina
```

---

## B) ARQUITECTURA DE PRODUCTO

### Estructura de Menú Final

```
📊 DASHBOARD (Personalizado por rol)
│
├── 🎓 ACADÉMICO
│   ├── Niveles y Grados (unificado)
│   ├── Secciones
│   ├── Asignaturas
│   ├── Asignación Docentes
│   ├── Gestión de Períodos ⭐ COMPLETO
│   └── Cierre de Notas
│
├── 👥 INSCRIPCIONES
│   ├── Nueva Inscripción (wizard)
│   ├── Matriculación
│   ├── Familias
│   └── Documentos
│
├── 💰 FINANZAS
│   ├── Planes de Pago
│   ├── Estados de Cuenta
│   ├── Registro de Pagos
│   ├── Comprobantes ⭐ WORKFLOW COMPLETO
│   │   ├── Pendientes
│   │   ├── Emitidos (3 tipos)
│   │   └── Solicitudes Anulación
│   ├── Corte del Día
│   ├── Insolventes
│   └── Exoneraciones
│
├── 📝 DOCENTE
│   ├── Mis Asignaturas
│   ├── Tareas y Actividades
│   ├── Ingreso de Notas
│   ├── Contenido Educativo
│   └── Listados
│
├── 👨‍👩‍👧 PADRE/ALUMNO
│   ├── Mi Dashboard
│   ├── Cronograma de Tareas
│   ├── Boletas de Notas
│   ├── Estado de Cuenta
│   └── Contrato
│
├── 🔐 ADMINISTRACIÓN
│   ├── Usuarios
│   ├── Roles y Permisos
│   ├── Personal Administrativo
│   ├── Cargos Administrativos ⭐ NUEVO
│   ├── Centro de Ajustes ⭐ UNIFICADO
│   └── Auditoría (Logs) ⭐ COMPLETO
│
├── ⚙️ CONFIGURACIÓN
│   ├── Datos del Colegio
│   ├── Branding (PDFs/Login/Logos) ⭐ NUEVO
│   ├── Catálogos
│   ├── Plantillas de Correo ⭐ NUEVO
│   ├── Notificaciones ⭐ NUEVO
│   └── Cierre Escolar ⭐ WIZARD COMPLETO
│
└── 🔔 NOTIFICACIONES ⭐ SISTEMA COMPLETO
```

### Módulos Eliminados (7)
| Módulo | Razón |
|--------|-------|
| Generador de API | Plantilla sin uso |
| Productos | No utilizado |
| Paquetes y Productos | Duplicado |
| Generador de Módulos | Solo desarrollo |
| Generador de Estadísticas | Unificado en Dashboard |
| Notificaciones Reset | Innecesario |
| Ajuste de Estudiantes | Duplicado con Familias |

### Módulos Unificados (5)
| Antes | Después |
|-------|---------|
| Cursos + Niveles | Niveles Académicos |
| Boletas Estudiante + Cuadros | Boletas de Notas |
| Contratos + Contratos Generados | Contratos |
| 12 submódulos Generales | Centro de Ajustes |
| Personal Admin + Ajuste | Gestión de Personal |

---

# PARTE 2: MÓDULOS DETALLADOS

## C) REVISIÓN MÓDULO POR MÓDULO

### FINANZAS - Comprobantes (WORKFLOW COMPLETO)

#### Estados del Pago → Comprobante
```
[PAGO_REGISTRADO] → [COMPROBANTE_PENDIENTE] → [COMPROBANTE_EMITIDO]
                                            ↘ [COMPROBANTE_ANULADO]
```

#### Comprobantes Pendientes
| Campo | Valor |
|-------|-------|
| **Estado** | ✅ MANTENER (detallar) |
| **Roles** | Secretaría (ver/emitir), Contabilidad (todo) |
| **Alerta** | Badge si >24h sin emitir |
| **Dashboard** | Widget en Secretaría |
| **Estado Vacío** | "✓ Todos los pagos tienen comprobante emitido" |

#### Comprobantes Emitidos (3 Tipos)
| Tipo | Anulación | Campos Clave |
|------|-----------|--------------|
| Factura SAT | Doble autorización | NIT, Serie, DTE |
| Recibo SAT | Doble autorización | NIT, Serie, DTE |
| Recibo Interno | Directo Contabilidad | Correlativo interno |

**Columnas de Tabla:**
Fecha, Tipo, Serie-Número, NIT, Monto, Estado (badge), Anulado Por, Fecha Anulación, Acciones

---

### FINANZAS - Solicitudes de Anulación SAT

#### Máquina de Estados
```
[CREADA] → [PENDIENTE_CONTABILIDAD]
                    │
             ┌──────┴──────┐
             ▼             ▼
    [APROBADA_CONT]   [RECHAZADA]
             │
             ▼
    [PENDIENTE_ADMIN] (solo SAT)
             │
      ┌──────┴──────┐
      ▼             ▼
[APROBADA]   [RECHAZADA_ADMIN]
      │
      ▼
[ANULADA_EN_SAT]
      │
      ▼
[COMPLETADA]
```

#### Campos de Solicitud
| Campo | Tipo | Requerido |
|-------|------|-----------|
| ID Comprobante | FK | Sí |
| Tipo | Enum | Sí |
| Motivo | Texto (min 20) | Sí |
| Adjunto | File | No |
| Solicitado Por | FK | Auto |
| Fecha Solicitud | Timestamp | Auto |
| Estado | Enum | Auto |
| Autorizado Cont | FK | Cuando aplica |
| Autorizado Admin | FK | Cuando aplica (SAT) |
| Motivo Rechazo | Texto | Si rechazada |

#### Permisos
| Rol | Crear | Ver | Aprobar L1 | Aprobar L2 |
|-----|-------|-----|------------|------------|
| Secretaría | ✅ | Propias | ❌ | ❌ |
| Contabilidad | ❌ | ✅ Todas | ✅ | ❌ |
| Super Admin | ❌ | ✅ Todas | ✅ | ✅ |

---

### ADMINISTRACIÓN - Centro de Ajustes (UNIFICADO)

#### Tabs del Centro de Ajustes
1. Estudiantes (grado/sección/estado)
2. Familias (relaciones padre/madre/encargado)
3. Pagos (eliminar cargos, ajustar montos)
4. Usuarios (credenciales, privilegios)
5. Personas (datos básicos)
6. Personal (cargos administrativos)

#### Política de Trazabilidad
| Tipo Ajuste | Riesgo | Autorización | Log |
|-------------|--------|--------------|-----|
| Estudiante | MEDIO | Secretaría | Antes/Después |
| Pago | ALTO | Super Admin | Doble + Justificación |
| Inscripción | ALTO | Contabilidad | + Justificación |
| Familia | BAJO | Secretaría | Simple |
| Usuario | ALTO | Informática | + Notificación |

#### Estructura Log de Ajuste
```json
{
  "tipo_ajuste": "PAGO",
  "entidad": "Cuota",
  "entidad_id": 789,
  "usuario_id": 123,
  "timestamp": "2026-01-28T17:00:00Z",
  "justificacion": "Error en asignación - autorizado",
  "datos_anteriores": { "monto": 500, "estado": "PENDIENTE" },
  "datos_nuevos": { "monto": 0, "estado": "ELIMINADO" },
  "autorizado_por": 456,
  "ip_address": "192.168.1.50"
}
```

---

### CONFIGURACIÓN - Branding (NUEVO)

#### Secciones
| Sección | Elementos |
|---------|-----------|
| Identidad | Nombre, slogan, logos, favicon, colores |
| PDFs | Logo, firmas, pie, marca de agua por documento |
| Login | Imagen fondo, logo central, mensaje bienvenida |

#### Roles
Solo Informática y Super Admin

---

### CONFIGURACIÓN - Cierre Escolar (WIZARD 5 PASOS)

#### Paso 1: Validaciones Pre-Cierre
| Validación | Bloqueante |
|------------|------------|
| Notas completas | Sí |
| Períodos cerrados | Sí |
| Backup <24h | Sí |
| Pagos pendientes | No (advertencia) |

#### Paso 2: Nuevo Ciclo
- Año escolar
- Fechas inicio/fin
- Copiar estructura
- Copiar docentes

#### Paso 3: Migración
| Datos | Acción |
|-------|--------|
| Estudiantes activos | Promover +1 grado |
| Último grado | Marcar "Egresado" |
| Notas/Boletas | Archivar histórico |
| Saldos pendientes | Migrar |
| Docentes | Mantener sin asignación |

#### Paso 4: Confirmación
Checkboxes obligatorios + advertencia irreversible

#### Paso 5: Reporte
PDF con promovidos, egresados, saldos, errores

---

### CONFIGURACIÓN - Plantillas de Correo (NUEVO)

#### 9 Plantillas Requeridas
| # | Plantilla | Trigger |
|---|-----------|---------|
| 1 | Recuperación Contraseña | Olvidé contraseña |
| 2 | Bienvenida Usuario | Crear usuario |
| 3 | Pago Registrado | Confirmar pago |
| 4 | Cuota Próxima | 5 días antes |
| 5 | Cuota Vencida | Día después |
| 6 | Nueva Tarea | Docente crea |
| 7 | Boleta Disponible | Cierre + solvente |
| 8 | Solicitud Anulación | Estado cambia |
| 9 | Cierre Período | 3 días antes |

#### Editor
- WYSIWYG
- Variables autocompletado
- Preview con datos prueba
- Historial versiones

---

### ADMINISTRACIÓN - Auditoría/Logs (COMPLETO)

#### Log de Acceso (Sesiones)
| Campo | Tipo |
|-------|------|
| user_id | FK |
| timestamp | DateTime |
| tipo | LOGIN/LOGOUT/TIMEOUT |
| ip_address | String |
| user_agent | String |
| exito | Boolean |
| motivo_fallo | String |

#### Log de Actividad (Acciones)
| Campo | Tipo |
|-------|------|
| user_id | FK |
| timestamp | DateTime |
| modulo | String |
| accion | CRUD/EXPORT |
| entidad | String |
| entidad_id | Integer |
| datos_anteriores | JSON |
| datos_nuevos | JSON |

#### Retención
| Tipo | En línea | Archivo |
|------|----------|---------|
| Accesos | 1 año | 5 años |
| Actividad | 2 años | 7 años |
| Críticos | 5 años | 10 años |

---

### Sistema de Notificaciones (NUEVO)

#### Canales
| Canal | Implementación |
|-------|----------------|
| In-App | WebSocket |
| Email | SMTP/SendGrid |
| Push | Firebase (futuro) |

#### Tabla Notificaciones
```sql
id, user_id, tipo, titulo, mensaje, link, leida, created_at, read_at
```

#### UI Campanita
- Badge contador
- Dropdown últimas 10
- "Marcar todas leídas"

---

# PARTE 3: PERMISOS Y SEGURIDAD

## D) MATRIZ DE PERMISOS COMPLETA

### Leyenda
✅ Permitido | ❌ Denegado | 🔐 Requiere Autorización | 📤 Exportar

| Módulo | Super Admin | Dirección | Coord | Contab | Secret | Docente | Padre | Alumno |
|--------|-------------|-----------|-------|--------|--------|---------|-------|--------|
| Dashboard | ✅ Full | ✅ Acad | ✅ Acad | ✅ Fin | ✅ Inscr | ✅ Doc | ✅ Hijo | ✅ Pers |
| Inscripciones | ✅ CRUD | ✅ Ver | ❌ | ❌ | ✅ CRUD | ❌ | ❌ | ❌ |
| Matriculación | ✅ CRUD | ✅ Ver | ❌ | ❌ | ✅ CRUD | ❌ | ❌ | ❌ |
| Planes Pago | ✅ CRUD | ✅ Ver | ❌ | ✅ CRUD | ✅ Ver | ❌ | ❌ | ❌ |
| Paquetes Selec | ✅ CRUD | ❌ | ❌ | ✅ CRUD | ❌ Ver | ❌ | ❌ | ❌ |
| Registro Pagos | ✅ CRUD | ❌ | ❌ | ✅ Autor | ✅ Crear | ❌ | ❌ | ❌ |
| Comprobantes | ✅+Anular | ❌ | ❌ | ✅+Autor | ✅ Crear | ❌ | ✅ Ver | ❌ |
| Anulaciones SAT | 🔐 L2 | ❌ | ❌ | 🔐 L1 | ✅ Solic | ❌ | ❌ | ❌ |
| Estados Cuenta | ✅ Todo | ❌ | ❌ | ✅ Todo | ✅ Todo | ❌ | ✅ Suyo | ✅ Suyo |
| Insolventes | ✅+Exon | ❌ | ❌ | ✅+Exon | ✅+Solic | ❌ | ❌ | ❌ |
| Niveles/Grados | ✅ CRUD | ✅ CRUD | ✅ CRUD | ❌ | ❌ | ❌ | ❌ | ❌ |
| Asignaturas | ✅ CRUD | ✅ CRUD | ✅ CRUD | ❌ | ❌ | ✅ Ver | ❌ | ❌ |
| Tareas | ✅ Ver | ✅ Ver | ✅ Ver | ❌ | ❌ | ✅ CRUD | ✅ Ver | ✅ Ver |
| Notas | ✅ Override | ❌ | ✅ Ver | ❌ | ❌ | ✅ CRUD | ❌ | ❌ |
| Cierre Notas | ✅ CRUD | ✅ Desblq | ✅ Desblq | ❌ | ❌ | ❌ | ❌ | ❌ |
| Boletas | ✅📤 | ✅📤 | ✅📤 | ❌ | ❌ | ✅ Ver | ✅📤 solv | ✅ solv |
| Usuarios | ✅ CRUD | ❌ | ❌ | ❌ | ❌ | ❌ | ❌ | ❌ |
| Auditoría | ✅ Ver📤 | ❌ | ❌ | ❌ | ❌ | ❌ | ❌ | ❌ |
| Branding | ✅ CRUD | ❌ | ❌ | ❌ | ❌ | ❌ | ❌ | ❌ |
| Cierre Escolar | ✅ Exec | ❌ | ❌ | ❌ | ❌ | ❌ | ❌ | ❌ |
| Centro Ajustes | ✅ Todo | ❌ | ❌ | ✅ Pagos | ✅ Estud | ❌ | ❌ | ❌ |
| Método Calif | ✅ CRUD | ❌ | ❌ | ❌ | ❌ | ❌ | ❌ | ❌ |

### Doble Autorización
| Acción | Solicita | Autoriza 1 | Autoriza 2 |
|--------|----------|------------|------------|
| Anulación Factura SAT | Secretaría | Contabilidad | Super Admin |
| Anulación Recibo SAT | Secretaría | Contabilidad | Super Admin |
| Exoneración Cuotas | Secretaría | Contabilidad | - |
| Desbloqueo Notas | Docente | Coordinación | - |
| Eliminar Pago | Contabilidad | Super Admin | - |

---

# PARTE 4: FLUJOS Y UX

## E) FLUJOS CRÍTICOS (15)

### Flujo 1: Inscripción → Matriculación → Paquete
```gherkin
GIVEN padre nuevo llega
WHEN Secretaría completa wizard 5 pasos
THEN estudiante en "Pendientes Matricular"

WHEN asigna Grado + Sección + Plan de Pago
THEN genera carné + cuotas automáticas
```

### Flujo 2: Pago → Comprobante → Anulación
```gherkin
GIVEN padre paga mensualidad
WHEN Secretaría registra + emite factura
THEN cuota PAGADA, factura EMITIDA

GIVEN error en NIT
WHEN Secretaría solicita anulación
THEN estado PENDIENTE_CONTABILIDAD
WHEN Contabilidad aprueba
THEN PENDIENTE_ADMIN
WHEN Admin aprueba
THEN ANULADA_SAT + log auditoría
```

### Flujo 3: Exoneración (Baja Alumno)
```gherkin
GIVEN alumno se retira
WHEN Secretaría solicita exoneración cuotas futuras
THEN Contabilidad ve en bandeja

WHEN autoriza
THEN cuotas EXONERADAS, alumno RETIRADO
```

### Flujo 4: Crear Tarea (Multi-Grado)
```gherkin
GIVEN docente tiene 3ro A, B, C
WHEN crea tarea seleccionando 3 secciones
THEN 1 tarea asignada a 3 grupos
AND notificación a padres
AND countdown activo
```

### Flujo 5: Ingreso Notas → Cierre Período
```gherkin
GIVEN período activo
WHEN docente ingresa notas
THEN guardado automático

GIVEN fecha cierre llega
WHEN sistema cierra automático
THEN docente NO puede editar
AND debe solicitar desbloqueo individual
```

### Flujos 6-15: (Resumidos)
| # | Flujo | Resultado |
|---|-------|-----------|
| 6 | Descarga Boleta | Solo si solvente |
| 7 | Convenio y Cuotas | Distribución automática |
| 8 | Corte del Día | PDF horizontal, 1 fila/pago |
| 9 | Ver Insolventes | Filtro vencidos default |
| 10 | Reset Contraseña | Email + link temporal |
| 11 | Asignar Docente | Matriz visual multi-select |
| 12 | Cierre Escolar | Wizard 5 pasos + migración |
| 13 | Subir Contrato Firmado | Padre ve en portal |
| 14 | Configurar Período | Fechas + cierre auto |
| 15 | Consultar Auditoría | Filtros + exportar CSV |

---

## F) UX/UI

### Dashboards por Rol

#### Super Admin (8 KPIs)
| KPI | Visual |
|-----|--------|
| Estudiantes Activos | Número grande |
| Ingresos Mes | Monto + % |
| Cuotas Vencidas | Monto + alerta |
| Docentes Activos | Número |
| Notas Pendientes | Lista nombres |
| Solicitudes Pendientes | Badge |
| Tareas Semana | Número |
| Inscripciones Nuevas | Gráfico |

#### Secretaría (6 KPIs)
Pendientes Matricular, Pagos Hoy, Comprobantes Pendientes, Insolventes, Docs Faltantes, Próximos Vencimientos

#### Contabilidad (6 KPIs)
Recaudación, Por Cobrar, Exoneraciones Pendientes, Anulaciones Pendientes, Morosidad, Corte Día

#### Docente (5 KPIs)
Mis Asignaturas, Pendientes Calificar, Notas por Ingresar, Próximo Cierre, Promedios

#### Padre (5 KPIs)
Próximas Tareas, Estado Financiero, Promedio Actual, Última Boleta, Mensajes

### Estados Vacíos
| Módulo | Mensaje | CTA |
|--------|---------|-----|
| Exoneraciones | "✓ Sin solicitudes pendientes" | Ver historial |
| Insolventes | "🎉 Todos al día" | Ver general |
| Comprobantes Pend | "✓ Todo emitido" | Ver emitidos |
| Tareas (Alumno) | "Sin tareas esta semana" | Ver calendario |

### Microcopy
**Éxito:** "✓ Pago registrado. Comprobante listo."
**Error:** "⚠ Período cerrado. Solicite desbloqueo."
**Confirmación destructiva:** "¿Anular factura? Requiere autorización y no se deshace."

---

# PARTE 5: BACKLOG Y RECOMENDACIONES

## G) BACKLOG EJECUTABLE

### P0 - Bloqueantes (Lanzamiento)

#### Épica: Inscripciones
| User Story | Criterios |
|------------|-----------|
| Inscripción Wizard | 5 pasos, validación DPI, upload docs |
| Matriculación | Validar capacidad, carné auto |
| Asignación Paquete | Generar cuotas |

#### Épica: Finanzas
| User Story | Criterios |
|------------|-----------|
| Registro Pagos | Multi-método, abonos, SAT |
| Estado Cuenta | Historial, cuotas, docs |
| Comprobantes Workflow | 3 tipos, estados completos |
| Insolventes Mejorado | Solo vencidos default |

#### Épica: Académico
| User Story | Criterios |
|------------|-----------|
| Niveles/Grados Unificado | Sin "Curso" |
| Gestión Períodos | Auto-cierre, % punteo |

#### Épica: Docente
| User Story | Criterios |
|------------|-----------|
| Dashboard Docente | KPIs, accesos rápidos |
| Tareas Multi-Grado | 1 creación → N grados |
| Ingreso Notas | Lista editable, auto-save |

### P1 - Importantes

| Épica | User Stories |
|-------|--------------|
| Anulaciones SAT | Workflow doble autorización |
| Exoneraciones | Flujo completo |
| Cierre Notas | Automático + desbloqueo individual |
| Centro Ajustes | Unificado con trazabilidad |
| Auditoría | Logs completos |
| Notificaciones | Sistema campanita |
| Plantillas Correo | 9 templates + editor |

### P2 - Mejoras

| Épica | User Stories |
|-------|--------------|
| Portal Padre | Dashboard, boletas, contrato |
| Cronograma | Estados, countdown |
| Branding | PDFs, login, logos |
| Cierre Escolar | Wizard migración |
| Catálogos | CRUD completo |
| Cargos Admin | CRUD |

---

## H) RECOMENDACIONES EXTRAS

### Implementar
1. **Auditoría completa** - Quién hizo qué, cuándo, desde dónde
2. **Trazabilidad ajustes** - Antes/después con justificación
3. **Notificaciones inteligentes** - Push + Email configurables
4. **Reportes financieros** - Resumen ejecutivo mensual
5. **Búsqueda global** - Encontrar desde cualquier lugar

### Eliminar Definitivamente
- Generador de API
- Generador de Módulos
- Productos / Paquetes y Productos
- Notificaciones Reset

### Performance
- Caché datos estáticos
- Rate limiting
- Sesiones seguras con timeout
- Backup automático pre-cierre

### Futuro
- API móvil (apps nativas)
- Integración bancaria
- Reportes Ministerio Educación

---

## RESUMEN EJECUTIVO

| Métrica | Antes | Después |
|---------|-------|---------|
| Módulos | 71 | 35 |
| Términos confusos | Sí | No |
| Duplicados | 8+ | 0 |
| Estados vacíos | 0 | 100% |
| Dashboards | 1 | 6 por rol |
| Flujos documentados | 0 | 15 |
| Permisos | Básicos | Matriz completa |
| Auditoría | Parcial | Completa |
| Notificaciones | Manual | Sistema automático |

---

> **Estado:** ✅ ESPECIFICACIÓN COMPLETA - Lista para desarrollo
