import { test, expect } from '@playwright/test';

/**
 * SMOKE TESTS - Critical User Flows
 * 
 * E2E-001: Admin login
 * E2E-002: Create academic level
 * E2E-003: Docente loads grades (with selection)
 * E2E-004: Student views own grades
 * E2E-005: API error handling (toast)
 * E2E-006: Unauthorized redirect
 * E2E-007: Create/persist Student
 * E2E-008: Create/persist Teacher
 * E2E-009: Enrollment process
 * E2E-010: Task creation by Teacher
 * E2E-011: PDF Generation (Report Card)
 * E2E-012: Admin dashboard stats load
 */

test.describe('SMOKE TESTS - Oxford Platform', () => {

    // --- E2E-001: Admin Login ---
    test('E2E-001: Admin login successful', async ({ page }) => {
        await page.goto('/login');
        await page.fill('#email', 'admin@oxford.edu.gt');
        await page.fill('#password', 'Oxford2024!');
        await page.click('button[type="submit"]');

        await expect(page).toHaveURL(/dashboard/);
        await expect(page.locator('text=Dashboard')).toBeVisible();
    });

    // --- E2E-002: CRUD Academic Level ---
    test('E2E-002: Create and Delete academic level', async ({ page }) => {
        // Login as Admin
        await page.goto('/login');
        await page.fill('#email', 'admin@oxford.edu.gt');
        await page.fill('#password', 'Oxford2024!');
        await page.click('button[type="submit"]');

        // Navigate to Levels
        await page.goto('/academico/cursos');

        // Create
        const testCode = `TEST-${Date.now()}`;
        await page.click('text=Nuevo Nivel'); // Adjust selector based on UI
        await page.fill('input[name="name"]', 'Start Up School Level');
        await page.fill('input[name="code"]', testCode);
        await page.click('button:has-text("Guardar")');

        // Verify creation
        await expect(page.locator(`text=${testCode}`)).toBeVisible();

        // Delete (Clean up)
        page.on('dialog', dialog => dialog.accept());
        await page.click(`div:has-text("${testCode}") button[aria-label="Eliminar"]`);
        await expect(page.locator(`text=${testCode}`)).not.toBeVisible();
    });

    // --- E2E-003: Teacher Grade Loading ---
    test('E2E-003: Docente loads grades page and selects subject', async ({ page }) => {
        // Login as Teacher
        await page.goto('/login');
        await page.fill('#email', 'docente@oxford.edu.gt');
        await page.fill('#password', 'Oxford2024!');
        await page.click('button[type="submit"]');

        await page.goto('/carga-notas');

        // Verify selects are present and not empty (PR3 fix check)
        const subjectSelect = page.locator('select[name="subject"]');
        await expect(subjectSelect).toBeVisible();

        // Wait for options to load (useSelectOptions hook check)
        await expect(subjectSelect).not.toHaveValue('');
        // Or check option count > 1

        // Select first subject
        await subjectSelect.selectOption({ index: 1 });

        // Verify table appears
        await expect(page.locator('table')).toBeVisible();
    });

    // --- E2E-004: Student Portal ---
    test('E2E-004: Student views own grades', async ({ page }) => {
        // Login as Student
        await page.goto('/login');
        await page.fill('#email', 'alumno@oxford.edu.gt');
        await page.fill('#password', 'Oxford2024!');
        await page.click('button[type="submit"]');

        await page.goto('/portal/mis-notas');
        await expect(page.locator('h1:has-text("Mis Calificaciones")')).toBeVisible();
        await expect(page.locator('table')).toBeVisible();
    });

    // --- E2E-005: API Error Handling ---
    test('E2E-005: API 500 shows toast error', async ({ page }) => {
        await page.goto('/login');
        await page.fill('#email', 'admin@oxford.edu.gt');
        await page.fill('#password', 'Oxford2024!');
        await page.click('button[type="submit"]');

        // Force 500 on level fetch
        await page.route('**/api/academic_levels', route => {
            route.fulfill({ status: 500, body: JSON.stringify({ message: 'Simulated Server Error' }) });
        });

        await page.goto('/academico/cursos');

        // Verify Toast
        await expect(page.locator('text=Simulated Server Error')).toBeVisible();
        // Verify no crash
        await expect(page.locator('body')).toBeVisible();
    });

    // --- E2E-006: Unauthorized Redirect ---
    test('E2E-006: Unauthorized access redirects to login', async ({ page }) => {
        await page.context().clearCookies();
        await page.goto('/academico/cursos');
        await expect(page).toHaveURL(/login/);
    });

    // --- E2E-010: Task Creation ---
    test('E2E-010: Teacher creates a task', async ({ page }) => {
        // Login as Teacher
        await page.goto('/login');
        await page.fill('#email', 'docente@oxford.edu.gt');
        await page.fill('#password', 'Oxford2024!');
        await page.click('button[type="submit"]');

        await page.goto('/gestion-tareas');
        await page.click('text=Nueva Tarea');

        const taskTitle = `Homework ${Date.now()}`;
        await page.fill('input[name="title"]', taskTitle);
        await page.fill('textarea[name="description"]', 'Please complete chapter 1');
        // Select subject/grade if needed
        // await page.selectOption('select[name="subject"]', { index: 1 }); // if logic requires

        await page.click('button:has-text("Crear Tarea")');

        // Verify in list
        await expect(page.locator(`text=${taskTitle}`)).toBeVisible();
    });

    // --- E2E-012: Admin Dashboard ---
    test('E2E-012: Admin dashboard stats load', async ({ page }) => {
        await page.goto('/login');
        await page.fill('#email', 'admin@oxford.edu.gt');
        await page.fill('#password', 'Oxford2024!');
        await page.click('button[type="submit"]');

        // Check for stats widgets
        await expect(page.locator('text=Total Alumnos')).toBeVisible();
        await expect(page.locator('text=Docentes Activos')).toBeVisible();
        // Check API call success for stats
        const response = await page.waitForResponse(resp => resp.url().includes('/dashboard/stats') && resp.status() === 200);
        expect(response.ok()).toBeTruthy();
    });

});
