// Role-based navigation configuration for Oxford Sistema
// Maps each role to their available menu items

export const ROLES = {
    SUPER_ADMIN: 'ROLE_SUPER_ADMIN',
    ADMIN: 'ROLE_ADMIN',
    CONTABILIDAD: 'ROLE_CONTABILIDAD',
    SECRETARIA: 'ROLE_SECRETARIA',
    COORDINACION: 'ROLE_COORDINACION',
    DIRECCION: 'ROLE_DIRECCION',
    INFORMATICA: 'ROLE_INFORMATICA',
    DOCENTE: 'ROLE_DOCENTE',
    ALUMNO: 'ROLE_ALUMNO',
    PADRE: 'ROLE_PADRE',
};

export const ROLE_LABELS = {
    [ROLES.SUPER_ADMIN]: 'Super Administrador',
    [ROLES.ADMIN]: 'Administrador',
    [ROLES.CONTABILIDAD]: 'Contabilidad',
    [ROLES.SECRETARIA]: 'Secretaría',
    [ROLES.COORDINACION]: 'Coordinación',
    [ROLES.DIRECCION]: 'Dirección',
    [ROLES.INFORMATICA]: 'Informática',
    [ROLES.DOCENTE]: 'Docente',
    [ROLES.ALUMNO]: 'Alumno',
    [ROLES.PADRE]: 'Padre de Familia',
};

// Menu items definition
export const MENU_ITEMS = {
    dashboard: { id: 'dashboard', label: 'Dashboard', path: '/dashboard', icon: 'Home' },

    // Secretaría
    inscripciones: { id: 'inscripciones', label: 'Inscripciones', path: '/inscripciones', icon: 'UserPlus' },
    matriculacion: { id: 'matriculacion', label: 'Matriculación', path: '/matriculacion', icon: 'GraduationCap' },
    familias: { id: 'familias', label: 'Familias', path: '/familias', icon: 'Users' },
    estudiantes: { id: 'estudiantes', label: 'Estudiantes', path: '/students', icon: 'User' },
    estadoCuenta: { id: 'estadoCuenta', label: 'Estado de Cuenta', path: '/estado-cuenta', icon: 'FileText' },
    registroPagos: { id: 'registroPagos', label: 'Registro de Pagos', path: '/pagos', icon: 'CreditCard' },
    convenios: { id: 'convenios', label: 'Convenios y Cuotas', path: '/convenios', icon: 'Calendar' },
    contratos: { id: 'contratos', label: 'Contratos', path: '/contracts', icon: 'FileCheck' },

    // Finanzas / Contabilidad
    corteDia: { id: 'corteDia', label: 'Corte del Día', path: '/corte-dia', icon: 'FileSpreadsheet' },
    comprobantes: { id: 'comprobantes', label: 'Comprobantes', path: '/comprobantes', icon: 'Receipt' },
    solicitudesAnulacion: { id: 'solicitudesAnulacion', label: 'Solicitudes', path: '/solicitudes', icon: 'AlertCircle' },
    paquetes: { id: 'paquetes', label: 'Paquetes', path: '/paquetes', icon: 'Package' },
    exoneraciones: { id: 'exoneraciones', label: 'Exoneraciones', path: '/exoneration', icon: 'MinusCircle' },
    financial: { id: 'financial', label: 'Finanzas', path: '/financial', icon: 'DollarSign' },

    // Académico
    academico: { id: 'academico', label: 'Académico', path: '/academic', icon: 'BookOpen' },
    grados: { id: 'grados', label: 'Grados', path: '/grados', icon: 'Layers' },
    secciones: { id: 'secciones', label: 'Secciones', path: '/secciones', icon: 'LayoutGrid' },
    materias: { id: 'materias', label: 'Materias', path: '/materias', icon: 'Book' },
    docentes: { id: 'docentes', label: 'Docentes', path: '/docentes', icon: 'Users' },
    asignaciones: { id: 'asignaciones', label: 'Asignaciones', path: '/asignaciones', icon: 'Link' },
    bimestres: { id: 'bimestres', label: 'Bimestres', path: '/bimestres', icon: 'Calendar' },
    cierreNotas: { id: 'cierreNotas', label: 'Cierre de Notas', path: '/cierre-notas', icon: 'Lock' },
    reportes: { id: 'reportes', label: 'Reportes', path: '/reports', icon: 'BarChart' },

    // Docente
    cargaNotas: { id: 'cargaNotas', label: 'Carga de Notas', path: '/carga-notas', icon: 'Edit' },
    horarios: { id: 'horarios', label: 'Horarios', path: '/horarios', icon: 'Clock' },
    misMaterias: { id: 'misMaterias', label: 'Mis Materias', path: '/mis-materias', icon: 'Bookmark' },
    misAlumnos: { id: 'misAlumnos', label: 'Mis Alumnos', path: '/mis-alumnos', icon: 'Users' },

    // Alumno
    misNotas: { id: 'misNotas', label: 'Mis Notas', path: '/mis-notas', icon: 'FileText' },
    miEstadoCuenta: { id: 'miEstadoCuenta', label: 'Mi Estado de Cuenta', path: '/mi-estado-cuenta', icon: 'CreditCard' },
    miHorario: { id: 'miHorario', label: 'Mi Horario', path: '/mi-horario', icon: 'Calendar' },

    // Administración
    usuarios: { id: 'usuarios', label: 'Usuarios', path: '/usuarios', icon: 'UserCog' },
    roles: { id: 'roles', label: 'Roles y Permisos', path: '/roles', icon: 'Shield' },
    menus: { id: 'menus', label: 'Gestión de Menús', path: '/menus', icon: 'Menu' },
    catalogos: { id: 'catalogos', label: 'Catálogos', path: '/catalogos', icon: 'Database' },
    ajustes: { id: 'ajustes', label: 'Ajustes', path: '/ajustes', icon: 'Settings' },
    configuracion: { id: 'configuracion', label: 'Configuración', path: '/settings', icon: 'Settings' },

    // Otros
    calendario: { id: 'calendario', label: 'Calendario', path: '/calendar', icon: 'Calendar' },
    notificaciones: { id: 'notificaciones', label: 'Notificaciones', path: '/notificaciones', icon: 'Bell' },
};

// Menu configuration per role
export const ROLE_MENUS = {
    [ROLES.SUPER_ADMIN]: [
        { section: 'Principal', items: ['dashboard'] },
        { section: 'Secretaría', items: ['inscripciones', 'matriculacion', 'familias', 'estudiantes', 'contratos'] },
        { section: 'Finanzas', items: ['registroPagos', 'estadoCuenta', 'corteDia', 'comprobantes', 'solicitudesAnulacion', 'paquetes', 'exoneraciones'] },
        { section: 'Académico', items: ['academico', 'grados', 'secciones', 'materias', 'docentes', 'asignaciones', 'bimestres', 'cierreNotas', 'reportes'] },
        { section: 'Administración', items: ['usuarios', 'roles', 'menus', 'catalogos', 'configuracion'] },
    ],

    [ROLES.ADMIN]: [
        { section: 'Principal', items: ['dashboard'] },
        { section: 'Secretaría', items: ['inscripciones', 'matriculacion', 'familias', 'estudiantes', 'contratos'] },
        { section: 'Finanzas', items: ['registroPagos', 'estadoCuenta', 'corteDia', 'comprobantes', 'solicitudesAnulacion', 'paquetes', 'exoneraciones'] },
        { section: 'Académico', items: ['academico', 'grados', 'secciones', 'materias', 'docentes', 'asignaciones', 'bimestres', 'cierreNotas', 'reportes'] },
        { section: 'Administración', items: ['usuarios', 'roles', 'menus', 'catalogos', 'configuracion'] },
    ],

    [ROLES.CONTABILIDAD]: [
        { section: 'Principal', items: ['dashboard'] },
        { section: 'Finanzas', items: ['corteDia', 'comprobantes', 'solicitudesAnulacion', 'paquetes', 'exoneraciones', 'estadoCuenta'] },
        { section: 'Ajustes', items: ['ajustes'] },
    ],

    [ROLES.SECRETARIA]: [
        { section: 'Principal', items: ['dashboard'] },
        { section: 'Inscripciones', items: ['inscripciones', 'matriculacion', 'familias', 'estudiantes'] },
        { section: 'Documentos', items: ['contratos', 'convenios', 'estadoCuenta'] },
        { section: 'Pagos', items: ['registroPagos', 'comprobantes'] },
    ],

    [ROLES.COORDINACION]: [
        { section: 'Principal', items: ['dashboard'] },
        { section: 'Académico', items: ['academico', 'materias', 'docentes', 'asignaciones', 'bimestres', 'cierreNotas'] },
        { section: 'Reportes', items: ['reportes', 'estudiantes'] },
    ],

    [ROLES.DIRECCION]: [
        { section: 'Principal', items: ['dashboard'] },
        { section: 'Académico', items: ['grados', 'secciones', 'materias', 'docentes', 'bimestres', 'horarios'] },
        { section: 'Reportes', items: ['reportes', 'estudiantes'] },
    ],

    [ROLES.INFORMATICA]: [
        { section: 'Principal', items: ['dashboard'] },
        { section: 'Sistema', items: ['usuarios', 'roles', 'menus', 'catalogos', 'configuracion'] },
    ],

    [ROLES.DOCENTE]: [
        { section: 'Principal', items: ['dashboard'] },
        { section: 'Académico', items: ['cargaNotas', 'misMaterias', 'miHorario'] },
        { section: 'Consultas', items: ['misAlumnos', 'calendario'] },
    ],

    [ROLES.ALUMNO]: [
        { section: 'Principal', items: ['dashboard'] },
        { section: 'Académico', items: ['misNotas', 'miHorario'] },
        { section: 'Finanzas', items: ['miEstadoCuenta'] },
    ],

    [ROLES.PADRE]: [
        { section: 'Principal', items: ['dashboard'] },
        { section: 'Mis Hijos', items: ['estudiantes'] },
        { section: 'Académico', items: ['misNotas', 'miHorario'] },
        { section: 'Finanzas', items: ['estadoCuenta'] },
    ],
};

// Get menu items for a specific role
export const getMenuForRole = (role) => {
    const roleMenu = ROLE_MENUS[role] || ROLE_MENUS[ROLES.ALUMNO];

    return roleMenu.map(section => ({
        section: section.section,
        items: section.items
            .map(itemId => MENU_ITEMS[itemId])
            .filter(Boolean),
    }));
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
