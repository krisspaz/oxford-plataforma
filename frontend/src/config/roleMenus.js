// Role-based navigation configuration for Oxford Sistema
// Maps each role to their available menu items

export const ROLES = {
    SUPER_ADMIN: 'ROLE_SUPER_ADMIN',
    ADMIN: 'ROLE_ADMIN',
    ACCOUNTANT: 'ROLE_ACCOUNTANT',
    SECRETARY: 'ROLE_SECRETARY',
    COORDINATION: 'ROLE_COORDINATION',
    DIRECTOR: 'ROLE_DIRECTOR',
    INFORMATICS: 'ROLE_INFORMATICS',
    TEACHER: 'ROLE_TEACHER',
    STUDENT: 'ROLE_STUDENT',
    PARENT: 'ROLE_PARENT',
};

export const ROLE_LABELS = {
    [ROLES.SUPER_ADMIN]: 'Super Administrador',
    [ROLES.ADMIN]: 'Administrador',
    [ROLES.ACCOUNTANT]: 'Contabilidad',
    [ROLES.SECRETARY]: 'Secretaría',
    [ROLES.COORDINATION]: 'Coordinación',
    [ROLES.DIRECTOR]: 'Dirección',
    [ROLES.INFORMATICS]: 'Informática',
    [ROLES.TEACHER]: 'Docente',
    [ROLES.STUDENT]: 'Estudiante',
    [ROLES.PARENT]: 'Padre de Familia',
};

// Menu items definition
export const MENU_ITEMS = {
    // === PRINCIPAL ===
    dashboard: { id: 'dashboard', label: 'Escritorio', path: '/', icon: 'Home' },

    // === FINANZAS ===
    insolventes: { id: 'insolventes', label: 'Insolventes', path: '/finanzas/insolventes', icon: 'UserMinus' },
    // exonerados: { id: 'exonerados', label: 'Exonerados', path: '/finanzas/exonerados', icon: 'CheckCircle' }, // Removed per request
    corteDia: { id: 'corteDia', label: 'Corte del Día', path: '/finanzas/corte-dia', icon: 'FileSpreadsheet' },
    comprobantes: { id: 'comprobantes', label: 'Comprobantes', path: '/finanzas/comprobantes', icon: 'Receipt' },
    comprobantesPendientes: { id: 'comprobantesPendientes', label: 'Comp. Pendientes', path: '/finanzas/comprobantes-pendientes', icon: 'AlertCircle' },
    comprobantesEmitidos: { id: 'comprobantesEmitidos', label: 'Comp. Emitidos', path: '/finanzas/comprobantes-emitidos', icon: 'FileText' },
    solicitudes: { id: 'solicitudes', label: 'Solicitudes y Anulaciones', path: '/finanzas/solicitudes', icon: 'HelpCircle' },
    paquetes: { id: 'paquetes', label: 'Paquetes Escolares', path: '/finanzas/paquetes', icon: 'Package' },
    costosNivel: { id: 'costosNivel', label: 'Costos por Nivel', path: '/finanzas/costos', icon: 'DollarSign' },
    estadoCuenta: { id: 'estadoCuenta', label: 'Estado de Cuenta', path: '/finanzas/estado-cuenta', icon: 'CreditCard' },
    registroPagos: { id: 'registroPagos', label: 'Registro de Pagos', path: '/finanzas/pagos', icon: 'PlusCircle' },
    paquetesSeleccionados: { id: 'paquetesSeleccionados', label: 'Paq. Seleccionados', path: '/finanzas/paquetes-seleccionados', icon: 'CheckSquare' },

    // === SECRETARÍA / INSCRIPCIONES ===
    inscripciones: { id: 'inscripciones', label: 'Inscripciones', path: '/secretaria/inscripciones', icon: 'UserPlus' },
    matricular: { id: 'matricular', label: 'Matricular', path: '/secretaria/matricular', icon: 'GraduationCap' },
    familias: { id: 'familias', label: 'Familias', path: '/secretaria/familias', icon: 'Users' },
    documentosInscripcion: { id: 'documentosInscripcion', label: 'Doc. Inscripción', path: '/secretaria/documentos', icon: 'File' },
    asignarPaquetes: { id: 'asignarPaquetes', label: 'Asignar Paquetes', path: '/secretaria/asignar-paquetes', icon: 'Package' },
    convenios: { id: 'convenios', label: 'Convenios y Cuotas', path: '/secretaria/convenios', icon: 'Calendar' },
    asignarConvenio: { id: 'asignarConvenio', label: 'Asignar Convenio', path: '/secretaria/asignar-convenio', icon: 'Edit' },
    contratos: { id: 'contratos', label: 'Contratos', path: '/secretaria/contratos', icon: 'FileText' },

    // === ACADÉMICO (Coordinación/Dirección) ===
    cronograma: { id: 'cronograma', label: 'Cronograma Tareas', path: '/academico/cronograma', icon: 'Calendar' },
    // Consolidated reports
    reportesAcademicos: { id: 'reportesAcademicos', label: 'Reportes y Boletas', path: '/academico/boletas', icon: 'FileText' },

    cierreNotas: { id: 'cierreNotas', label: 'Cierre de Notas', path: '/academico/cierre-notas', icon: 'Lock' },
    niveles: { id: 'niveles', label: 'Niveles', path: '/academico/cursos', icon: 'Layers' }, // Renamed from Cursos
    secciones: { id: 'secciones', label: 'Secciones', path: '/academico/secciones', icon: 'Layout' },
    docentes: { id: 'docentes', label: 'Docentes', path: '/academico/docentes', icon: 'Briefcase' },
    materias: { id: 'materias', label: 'Materias', path: '/academico/materias', icon: 'Book' },
    asignacionMaterias: { id: 'asignacionMaterias', label: 'Asig. Materias', path: '/academico/asignacion-materias', icon: 'Link' },
    grados: { id: 'grados', label: 'Grados', path: '/academico/grados', icon: 'BarChart' },
    horarios: { id: 'horarios', label: 'Jornadas y Horarios', path: '/academico/horarios', icon: 'Clock' },
    gestionCursos: { id: 'gestionCursos', label: 'Gestión Cursos', path: '/academico/gestion-cursos', icon: 'Settings' },

    // === DOCENTE ===
    tareasCalificadas: { id: 'tareasCalificadas', label: 'Tareas Calificadas', path: '/docente/tareas-calificadas', icon: 'CheckSquare' },
    asignarContenido: { id: 'asignarContenido', label: 'Asignar Contenido', path: '/docente/contenido', icon: 'Upload' },
    gestionTareas: { id: 'gestionTareas', label: 'Gestión Tareas', path: '/docente/tareas', icon: 'Edit' },
    listadoAlumnos: { id: 'listadoAlumnos', label: 'Listado Alumnos', path: '/docente/alumnos', icon: 'List' },
    cargaNotas: { id: 'cargaNotas', label: 'Cargar Notas', path: '/carga-notas', icon: 'PenTool' }, // Added Carga Notas
    asignarNotas: { id: 'asignarNotas', label: 'Asignar Notas', path: '/docente/notas', icon: 'PenTool' },
    notasFinales: { id: 'notasFinales', label: 'Notas Finales', path: '/docente/notas-finales', icon: 'Award' },
    miHorario: { id: 'miHorario', label: 'Mi Horario', path: '/docente/horario', icon: 'Clock' },

    // === ALUMNO ===
    misNotas: { id: 'misNotas', label: 'Mis Notas', path: '/alumno/notas', icon: 'FileText' },
    chatDocente: { id: 'chatDocente', label: 'Chat Docente', path: '/alumno/chat-profesor', icon: 'MessageSquare' },
    evaluarMaestros: { id: 'evaluarMaestros', label: 'Evaluar Maestros', path: '/alumno/evaluar-profesores', icon: 'Star' },
    buzonAyuda: { id: 'buzonAyuda', label: 'Reportar Problema', path: '/alumno/quejas', icon: 'AlertTriangle' },
    constancias: { id: 'constancias', label: 'Constancias', path: '/alumno/constancias', icon: 'FileText' },
    miHorarioAlumno: { id: 'miHorarioAlumno', label: 'Mi Horario', path: '/alumno/horario', icon: 'Calendar' },
    calendarioTareas: { id: 'calendarioTareas', label: 'Calendario Global', path: '/alumno/calendario', icon: 'Calendar' },
    misTareas: { id: 'misTareas', label: 'Mis Tareas', path: '/alumno/mis-tareas', icon: 'ClipboardList' },
    sugerencias: { id: 'sugerencias', label: 'Sugerencias', path: '/alumno/sugerencias', icon: 'Lightbulb' },

    // === PADRES ===
    misHijos: { id: 'misHijos', label: 'Mis Hijos', path: '/padres/hijos', icon: 'Users' },
    contratoPadres: { id: 'contratoPadres', label: 'Contrato', path: '/padres/contrato', icon: 'FileText' },

    // === ADMINISTRACIÓN / SISTEMA ===
    cierreEscolar: { id: 'cierreEscolar', label: 'Cierre Escolar', path: '/admin/cierre-escolar', icon: 'Archive' },
    cargosAdmin: { id: 'cargosAdmin', label: 'Cargos Admin', path: '/admin/cargos', icon: 'Briefcase' },
    usuarios: { id: 'usuarios', label: 'Usuarios', path: '/admin/usuarios', icon: 'User' },
    notificacionesReset: { id: 'notificacionesReset', label: 'Reset Notif.', path: '/admin/notificaciones-reset', icon: 'BellOff' },
    privilegios: { id: 'privilegios', label: 'Privilegios y Roles', path: '/admin/privilegios', icon: 'Shield' },
    menus: { id: 'menus', label: 'Gestión Menús', path: '/admin/menus', icon: 'Menu' },
    ajustes: { id: 'ajustes', label: 'Ajustes', path: '/admin/ajustes', icon: 'Settings' },
    estadisticas: { id: 'estadisticas', label: 'Estadísticas', path: '/admin/estadisticas', icon: 'TrendingUp' },
    logs: { id: 'logs', label: 'Logs Acceso', path: '/admin/logs', icon: 'Activity' },
    // Consolidated adjustments
    ajusteGeneral: { id: 'ajusteGeneral', label: 'Mantenimiento Datos', path: '/admin/ajustes-generales', icon: 'Sliders' },
    catalogos: { id: 'catalogos', label: 'Catálogos', path: '/admin/catalogos', icon: 'Database' },
    iaHorarios: { id: 'iaHorarios', label: 'Asistente Personal', path: '/academico/oxford-ai', icon: 'Brain' },
};

// Menu configuration per role
export const ROLE_MENUS = {
    [ROLES.SUPER_ADMIN]: [
        { section: 'Principal', items: ['dashboard'] },
        { section: 'Administración', items: ['cierreEscolar', 'usuarios', 'cargosAdmin', 'ajusteGeneral'] },
        { section: 'Finanzas', items: ['insolventes', 'corteDia', 'solicitudes', 'paquetes', 'costosNivel'] },
        { section: 'Académico', items: ['cierreNotas'] },
    ],

    [ROLES.ACCOUNTANT]: [
        { section: 'Principal', items: ['dashboard'] },
        { section: 'Gestión Cobros', items: ['insolventes', 'comprobantesPendientes', 'paquetesSeleccionados'] },
        { section: 'Operaciones', items: ['corteDia', 'solicitudes', 'contratos'] },
        { section: 'Configuración', items: ['paquetes', 'costosNivel', 'ajusteGeneral'] },
        { section: 'Inteligencia Artificial', items: ['iaHorarios'] },
    ],

    [ROLES.SECRETARY]: [
        { section: 'Principal', items: ['dashboard'] },
        { section: 'Inscripciones', items: ['inscripciones', 'matricular', 'familias', 'documentosInscripcion', 'contratos'] },
        { section: 'Cobros', items: ['registroPagos', 'estadoCuenta', 'insolventes', 'comprobantesEmitidos', 'corteDia'] },
        { section: 'Gestión', items: ['asignarPaquetes', 'convenios', 'asignarConvenio', 'paquetesSeleccionados'] },
        { section: 'Configuración', items: ['ajustes', 'ajusteGeneral', 'catalogos'] },
        { section: 'Inteligencia Artificial', items: ['iaHorarios'] },
    ],

    [ROLES.INFORMATICS]: [
        { section: 'Principal', items: ['dashboard'] },
        { section: 'Sistema', items: ['notificacionesReset', 'cierreEscolar', 'usuarios', 'privilegios', 'menus'] },
        { section: 'Configuración', items: ['ajustes', 'ajusteGeneral', 'catalogos'] },
        { section: 'Monitoreo', items: ['estadisticas', 'logs'] },
        { section: 'Inteligencia Artificial', items: ['iaHorarios'] },
    ],

    [ROLES.COORDINATION]: [
        { section: 'Principal', items: ['dashboard'] },
        { section: 'Gestión Académica', items: ['cronograma', 'cierreNotas', 'materias', 'asignacionMaterias', 'docentes'] },
        { section: 'Organización', items: ['niveles', 'secciones', 'grados', 'iaHorarios', 'gestionCursos'] },
        { section: 'Reportes', items: ['reportesAcademicos'] },
        { section: 'Configuración', items: ['ajustes', 'ajusteGeneral', 'catalogos'] },
    ],

    [ROLES.DIRECTOR]: [
        { section: 'Principal', items: ['dashboard'] },
        { section: 'Inteligencia Artificial', items: ['iaHorarios'] },
        { section: 'Supervisión', items: ['cronograma', 'cierreNotas', 'reportesAcademicos'] },
        { section: 'Académico', items: ['niveles', 'secciones', 'docentes', 'materias', 'grados', 'gestionCursos'] },
        { section: 'Configuración', items: ['ajustes', 'ajusteGeneral', 'catalogos'] },
    ],

    [ROLES.TEACHER]: [
        { section: 'Principal', items: ['dashboard'] },
        { section: 'Mis Clases', items: ['miHorario', 'listadoAlumnos', 'cronograma'] },
        { section: 'Actividades', items: ['gestionTareas', 'asignarContenido', 'tareasCalificadas'] },
        { section: 'Calificaciones', items: ['asignarNotas', 'notasFinales'] },
        { section: 'Inteligencia Artificial', items: ['iaHorarios'] },
    ],

    [ROLES.STUDENT]: [
        { section: 'Principal', items: ['dashboard'] },
        { section: 'Mi Aprendizaje', items: ['misTareas', 'calendarioTareas', 'misNotas', 'miHorarioAlumno'] },
        { section: 'Comunicación', items: ['chatDocente', 'sugerencias', 'buzonAyuda'] },
        { section: 'Evaluaciones', items: ['evaluarMaestros'] },
    ],

    [ROLES.PARENT]: [
        { section: 'Principal', items: ['dashboard'] },
        { section: 'Mis Hijos', items: ['misHijos', 'contratoPadres'] },
        { section: 'Seguimiento', items: ['calendarioTareas', 'reportesAcademicos'] },
    ],

    // Admin fallback
    [ROLES.ADMIN]: [
        { section: 'Principal', items: ['dashboard'] },
        { section: 'Administración', items: ['usuarios', 'privilegios', 'menus', 'ajustes'] },
        { section: 'Finanzas', items: ['estadoCuenta', 'insolventes', 'corteDia', 'solicitudes', 'paquetes', 'costosNivel'] },
        { section: 'Académico', items: ['cierreNotas'] },
    ],

    // === SPANISH ROLE ALIASES (para compatibilidad con BD) ===
    'ROLE_SECRETARIA': [
        { section: 'Principal', items: ['dashboard'] },
        { section: 'Inscripciones', items: ['inscripciones', 'matricular', 'familias', 'documentosInscripcion', 'contratos'] },
        { section: 'Cobros', items: ['registroPagos', 'estadoCuenta', 'insolventes', 'comprobantesEmitidos', 'corteDia'] },
        { section: 'Gestión', items: ['asignarPaquetes', 'convenios', 'asignarConvenio', 'paquetesSeleccionados'] },
        { section: 'Configuración', items: ['ajustes', 'ajusteGeneral', 'catalogos'] },
    ],
    'ROLE_CONTABILIDAD': [
        { section: 'Principal', items: ['dashboard'] },
        { section: 'Gestión Cobros', items: ['insolventes', 'comprobantesPendientes', 'paquetesSeleccionados'] },
        { section: 'Operaciones', items: ['corteDia', 'solicitudes', 'contratos'] },
        { section: 'Configuración', items: ['paquetes', 'costosNivel', 'ajusteGeneral'] },
    ],
    'ROLE_COORDINACION': [
        { section: 'Principal', items: ['dashboard'] },
        { section: 'Gestión Académica', items: ['cronograma', 'cierreNotas', 'materias', 'asignacionMaterias', 'docentes'] },
        { section: 'Organización', items: ['niveles', 'secciones', 'grados', 'iaHorarios', 'gestionCursos'] },
        { section: 'Reportes', items: ['reportesAcademicos'] },
        { section: 'Configuración', items: ['ajustes', 'ajusteGeneral', 'catalogos'] },
    ],
    'ROLE_DIRECCION': [
        { section: 'Principal', items: ['dashboard'] },
        { section: 'Inteligencia Artificial', items: ['iaHorarios'] },
        { section: 'Supervisión', items: ['cronograma', 'cierreNotas', 'reportesAcademicos'] },
        { section: 'Académico', items: ['niveles', 'secciones', 'docentes', 'materias', 'grados', 'gestionCursos'] },
        { section: 'Configuración', items: ['ajustes', 'ajusteGeneral', 'catalogos'] },
    ],
    'ROLE_DOCENTE': [
        { section: 'Principal', items: ['dashboard'] },
        { section: 'Mis Clases', items: ['miHorario', 'listadoAlumnos', 'cronograma'] },
        { section: 'Actividades', items: ['gestionTareas', 'asignarContenido', 'tareasCalificadas'] },
        { section: 'Calificaciones', items: ['asignarNotas', 'notasFinales'] },
        { section: 'Inteligencia Artificial', items: ['iaHorarios'] },
    ],
    'ROLE_ALUMNO': [
        { section: 'Principal', items: ['dashboard'] },
        { section: 'Mi Aprendizaje', items: ['calendarioTareas', 'misNotas', 'miHorarioAlumno'] },
    ],
    'ROLE_PADRE': [
        { section: 'Principal', items: ['dashboard'] },
        { section: 'Mis Hijos', items: ['misHijos', 'contratoPadres'] },
        { section: 'Seguimiento', items: ['calendarioTareas', 'reportesAcademicos'] },
    ],
    'ROLE_INFORMATICA': [
        { section: 'Principal', items: ['dashboard'] },
        { section: 'Sistema', items: ['notificacionesReset', 'cierreEscolar', 'usuarios', 'privilegios', 'menus'] },
        { section: 'Configuración', items: ['ajustes', 'ajusteGeneral', 'catalogos'] },
        { section: 'Monitoreo', items: ['estadisticas', 'logs'] },
    ],
};

// Get menu items for a specific role
export const getMenuForRole = (role) => {
    const roleMenu = ROLE_MENUS[role] || ROLE_MENUS[ROLES.STUDENT];

    return roleMenu.map(section => ({
        section: section.section,
        items: section.items
            .map(itemId => MENU_ITEMS[itemId])
            .filter(Boolean),
    }));
};

// Combine menus for multiple roles
export const getCombinedMenu = (roles) => {
    if (!roles || roles.length === 0) return getMenuForRole('ROLE_STUDENT');

    const sectionsMap = new Map();

    roles.forEach(role => {
        const roleMenu = ROLE_MENUS[role];
        if (!roleMenu) return;

        roleMenu.forEach(section => {
            if (!sectionsMap.has(section.section)) {
                sectionsMap.set(section.section, new Set());
            }
            section.items.forEach(item => sectionsMap.get(section.section).add(item));
        });
    });

    // Define section order preference
    const sectionOrder = [
        'Principal', 'Administración', 'Sistema', 'Monitoreo',
        'Inscripciones', 'Finanzas', 'Gestión Cobros', 'Operaciones',
        'Académico', 'Gestión Académica', 'Organización', 'Reportes', 'Supervisión',
        'Docente', 'Mis Clases', 'Actividades', 'Calificaciones',
        'Estudiante', 'Mi Aprendizaje',
        'Padres', 'Mis Hijos', 'Seguimiento',
        'Configuración'
    ];

    const sortedSections = [];

    // Add sections in preferred order
    sectionOrder.forEach(name => {
        if (sectionsMap.has(name)) {
            sortedSections.push({
                section: name,
                items: Array.from(sectionsMap.get(name))
                    .map(itemId => MENU_ITEMS[itemId])
                    .filter(Boolean)
            });
            sectionsMap.delete(name);
        }
    });

    // Add remaining sections
    sectionsMap.forEach((itemsSet, name) => {
        sortedSections.push({
            section: name,
            items: Array.from(itemsSet)
                .map(itemId => MENU_ITEMS[itemId])
                .filter(Boolean)
        });
    });

    return sortedSections;
};

// Check if user has access to a specific path
export const hasAccessToPath = (roles, path) => {
    if (!roles || roles.length === 0) return false;

    // Super admin has access to everything
    if (roles.includes(ROLES.SUPER_ADMIN)) return true;

    // Check each role's menu
    for (const role of roles) {
        const menu = ROLE_MENUS[role];
        if (!menu) continue;

        for (const section of menu) {
            for (const itemId of section.items) {
                const item = MENU_ITEMS[itemId];
                if (item && item.path === path) return true;
            }
        }
    }

    return false;
};
