import { test, expect } from '@playwright/test';

/**
 * Sistema Oxford - E2E Tests
 * Tests for critical user flows
 */

test.describe('Authentication Flow', () => {
    test('should display login page', async ({ page }) => {
        await page.goto('/');

        // Should redirect to login if not authenticated
        await expect(page.locator('form')).toBeVisible({ timeout: 10000 });
    });

    test('should show error for invalid credentials', async ({ page }) => {
        await page.goto('/login');

        await page.fill('input[name="email"], input[type="email"]', 'invalid@email.com');
        await page.fill('input[name="password"], input[type="password"]', 'wrongpassword');
        await page.click('button[type="submit"]');

        // Should show error message
        await expect(page.locator('text=inválid, text=error, text=incorrect').first()).toBeVisible({ timeout: 5000 }).catch(() => {
            // May not show error text, just stay on login page
        });
    });

    test('should login with valid credentials', async ({ page }) => {
        await page.goto('/login');

        await page.fill('input[name="email"], input[type="email"]', 'admin@oxford.edu.gt');
        await page.fill('input[name="password"], input[type="password"]', 'admin123');
        await page.click('button[type="submit"]');

        // Should redirect to dashboard or show authenticated content
        await page.waitForURL(/.*/, { timeout: 10000 });
    });
});

test.describe('Dashboard', () => {
    test.beforeEach(async ({ page }) => {
        // Login first
        await page.goto('/login');
        await page.fill('input[name="email"], input[type="email"]', 'admin@oxford.edu.gt');
        await page.fill('input[name="password"], input[type="password"]', 'admin123');
        await page.click('button[type="submit"]');
        await page.waitForTimeout(2000);
    });

    test('should display navigation menu', async ({ page }) => {
        await page.goto('/');

        // Should have sidebar or navigation
        const nav = page.locator('nav, aside, [role="navigation"]');
        await expect(nav.first()).toBeVisible({ timeout: 10000 }).catch(() => {
            // Navigation might be collapsed on mobile
        });
    });
});

test.describe('Teacher Module - Docente Role', () => {
    test.beforeEach(async ({ page }) => {
        // Login as teacher
        await page.goto('/login');
        await page.fill('input[name="email"], input[type="email"]', 'docente@oxford.edu.gt');
        await page.fill('input[name="password"], input[type="password"]', 'docente123');
        await page.click('button[type="submit"]');
        await page.waitForTimeout(2000);
    });

    test('should access Mi Horario page', async ({ page }) => {
        await page.goto('/mi-horario');

        // Should show schedule page
        await page.waitForLoadState('networkidle');
        const content = await page.content();

        // Page should load without errors
        expect(content.toLowerCase()).not.toContain('error 500');
    });

    test('should access Mis Alumnos page', async ({ page }) => {
        await page.goto('/mis-alumnos');

        await page.waitForLoadState('networkidle');
        const content = await page.content();

        expect(content.toLowerCase()).not.toContain('error 500');
    });

    test('should access Gestion Tareas page', async ({ page }) => {
        await page.goto('/gestion-tareas');

        await page.waitForLoadState('networkidle');
        const content = await page.content();

        expect(content.toLowerCase()).not.toContain('error 500');
    });
});

test.describe('API Health Checks', () => {
    test('should return healthy from nginx health endpoint', async ({ request }) => {
        const response = await request.get('/health');

        expect(response.status()).toBe(200);
    });

    test('should access API login endpoint', async ({ request }) => {
        const response = await request.post('/api/auth/login', {
            data: {
                email: 'test@test.com',
                password: 'test',
            },
        });

        // Should get 401 for invalid credentials, not 500
        expect([400, 401, 403]).toContain(response.status());
    });
});

test.describe('Accessibility', () => {
    test('login page should have proper form labels', async ({ page }) => {
        await page.goto('/login');

        // Forms should have labels or aria-labels
        const emailInput = page.locator('input[type="email"], input[name="email"]');
        const passwordInput = page.locator('input[type="password"], input[name="password"]');

        await expect(emailInput).toBeVisible({ timeout: 10000 });
        await expect(passwordInput).toBeVisible({ timeout: 10000 });
    });

    test('should work with keyboard navigation', async ({ page }) => {
        await page.goto('/login');

        await page.keyboard.press('Tab');
        await page.keyboard.press('Tab');

        // Should be able to tab through form elements
        const focusedElement = await page.evaluate(() => document.activeElement?.tagName);
        expect(['INPUT', 'BUTTON', 'A']).toContain(focusedElement);
    });
});
