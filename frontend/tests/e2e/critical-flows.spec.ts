// E2E Tests Configuration
// Run with: npx playwright test

import { test, expect } from '@playwright/test';

const BASE_URL = 'http://localhost';

// Test: Login Flow
test.describe('Authentication', () => {
    test('should display login page', async ({ page }) => {
        await page.goto(BASE_URL);
        await expect(page.locator('text=Iniciar Sesión')).toBeVisible();
    });

    test('should login with valid credentials', async ({ page }) => {
        await page.goto(BASE_URL);
        await page.fill('input[type="email"]', 'admin@oxford.edu');
        await page.fill('input[type="password"]', 'oxford2025');
        await page.click('button[type="submit"]');
        await expect(page.locator('text=Dashboard')).toBeVisible({ timeout: 10000 });
    });
});

// Test: Enrollment Flow
test.describe('Inscripciones', () => {
    test.beforeEach(async ({ page }) => {
        await page.goto(BASE_URL);
        await page.fill('input[type="email"]', 'secretaria@oxford.edu');
        await page.fill('input[type="password"]', 'oxford2025');
        await page.click('button[type="submit"]');
        await page.waitForTimeout(2000);
    });

    test('should display enrollment wizard', async ({ page }) => {
        await page.click('text=Inscripciones');
        await expect(page.locator('text=Nueva Inscripción')).toBeVisible();
        await expect(page.locator('text=Estudiante')).toBeVisible();
    });

    test('should navigate through wizard steps', async ({ page }) => {
        await page.click('text=Inscripciones');

        // Step 1: Student
        await page.fill('input[placeholder*="Nombres"]', 'Test');
        await page.fill('input[placeholder*="Apellidos"]', 'Student');
        await page.click('text=Siguiente');

        // Should be on step 2
        await expect(page.locator('text=Datos del Padre')).toBeVisible();
    });
});

// Test: Payment Flow
test.describe('Registro de Pagos', () => {
    test.beforeEach(async ({ page }) => {
        await page.goto(BASE_URL);
        await page.fill('input[type="email"]', 'contabilidad@oxford.edu');
        await page.fill('input[type="password"]', 'oxford2025');
        await page.click('button[type="submit"]');
        await page.waitForTimeout(2000);
    });

    test('should search for student', async ({ page }) => {
        await page.click('text=Registro de Pagos');
        await page.fill('input[placeholder*="Buscar"]', 'Juan');
        await page.waitForTimeout(1000);
        // Should show search results
    });

    test('should display corte del día', async ({ page }) => {
        await page.click('text=Corte del Día');
        await expect(page.locator('text=Corte de Caja')).toBeVisible();
    });
});

// Test: Grade Entry Flow
test.describe('Carga de Notas', () => {
    test.beforeEach(async ({ page }) => {
        await page.goto(BASE_URL);
        await page.fill('input[type="email"]', 'docente@oxford.edu');
        await page.fill('input[type="password"]', 'oxford2025');
        await page.click('button[type="submit"]');
        await page.waitForTimeout(2000);
    });

    test('should display grade entry form', async ({ page }) => {
        await page.click('text=Carga de Notas');
        await expect(page.locator('text=Carga de Notas')).toBeVisible();
    });

    test('should select bimester', async ({ page }) => {
        await page.click('text=Carga de Notas');
        await page.selectOption('select', { index: 1 }); // Select first bimester
    });
});

// Test: User Management
test.describe('Gestión de Usuarios', () => {
    test.beforeEach(async ({ page }) => {
        await page.goto(BASE_URL);
        await page.fill('input[type="email"]', 'admin@oxford.edu');
        await page.fill('input[type="password"]', 'oxford2025');
        await page.click('button[type="submit"]');
        await page.waitForTimeout(2000);
    });

    test('should display user list', async ({ page }) => {
        await page.click('text=Gestión de Usuarios');
        await expect(page.locator('table')).toBeVisible();
    });

    test('should open new user modal', async ({ page }) => {
        await page.click('text=Gestión de Usuarios');
        await page.click('text=Nuevo Usuario');
        await expect(page.locator('text=Nombre')).toBeVisible();
    });
});

// Test: Bimester Management
test.describe('Gestión Bimestral', () => {
    test.beforeEach(async ({ page }) => {
        await page.goto(BASE_URL);
        await page.fill('input[type="email"]', 'admin@oxford.edu');
        await page.fill('input[type="password"]', 'oxford2025');
        await page.click('button[type="submit"]');
        await page.waitForTimeout(2000);
    });

    test('should display bimesters', async ({ page }) => {
        await page.click('text=Bimestres');
        await expect(page.locator('text=Gestión Bimestral')).toBeVisible();
    });
});
