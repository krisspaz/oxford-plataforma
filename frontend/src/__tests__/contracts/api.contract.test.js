// Contract tests - Pact Consumer Tests
// Validates API contracts between frontend and backend

import { pactWith } from 'jest-pact';
import { Matchers } from '@pact-foundation/pact';

const { like, eachLike, term, boolean, integer, string } = Matchers;

// ==========================================
// STUDENT API CONTRACT
// ==========================================

pactWith(
    { consumer: 'OxfordFrontend', provider: 'OxfordBackend' },
    (provider) => {
        describe('Student API Contract', () => {

            // List Students
            it('should return paginated students', async () => {
                await provider.addInteraction({
                    state: 'students exist',
                    uponReceiving: 'a request for students list',
                    withRequest: {
                        method: 'GET',
                        path: '/api/v1/students',
                        query: { page: '1', limit: '20' },
                        headers: {
                            Authorization: term({
                                matcher: '^Bearer .+$',
                                generate: 'Bearer eyJtoken',
                            }),
                        },
                    },
                    willRespondWith: {
                        status: 200,
                        headers: {
                            'Content-Type': 'application/json',
                        },
                        body: {
                            items: eachLike({
                                id: integer(1),
                                code: string('EST-001'),
                                firstName: string('Juan'),
                                lastName: string('Pérez'),
                                email: like('student@oxford.edu'),
                                grade: like({
                                    id: integer(1),
                                    name: string('3ero Básico'),
                                }),
                                status: term({
                                    matcher: 'active|inactive|graduated',
                                    generate: 'active',
                                }),
                            }),
                            total: integer(100),
                            page: integer(1),
                            limit: integer(20),
                            totalPages: integer(5),
                        },
                    },
                });

                // Make request and verify
                const response = await fetch(
                    `${provider.mockService.baseUrl}/api/v1/students?page=1&limit=20`,
                    {
                        headers: {
                            Authorization: 'Bearer eyJtoken',
                        },
                    }
                );

                expect(response.status).toBe(200);
                const data = await response.json();
                expect(data.items).toBeDefined();
                expect(Array.isArray(data.items)).toBe(true);
            });

            // Get Single Student
            it('should return a single student', async () => {
                await provider.addInteraction({
                    state: 'student with id 1 exists',
                    uponReceiving: 'a request for student with id 1',
                    withRequest: {
                        method: 'GET',
                        path: '/api/v1/students/1',
                        headers: {
                            Authorization: like('Bearer token'),
                        },
                    },
                    willRespondWith: {
                        status: 200,
                        body: {
                            success: boolean(true),
                            data: {
                                id: integer(1),
                                code: string('EST-001'),
                                firstName: string('Juan'),
                                lastName: string('Pérez'),
                            },
                        },
                    },
                });

                const response = await fetch(
                    `${provider.mockService.baseUrl}/api/v1/students/1`,
                    {
                        headers: { Authorization: 'Bearer token' },
                    }
                );

                expect(response.status).toBe(200);
            });

            // Student Not Found
            it('should return 404 for non-existent student', async () => {
                await provider.addInteraction({
                    state: 'student with id 9999 does not exist',
                    uponReceiving: 'a request for non-existent student',
                    withRequest: {
                        method: 'GET',
                        path: '/api/v1/students/9999',
                        headers: {
                            Authorization: like('Bearer token'),
                        },
                    },
                    willRespondWith: {
                        status: 404,
                        body: {
                            success: boolean(false),
                            message: string('Estudiante no encontrado'),
                        },
                    },
                });

                const response = await fetch(
                    `${provider.mockService.baseUrl}/api/v1/students/9999`,
                    {
                        headers: { Authorization: 'Bearer token' },
                    }
                );

                expect(response.status).toBe(404);
            });
        });

        // ==========================================
        // AUTH API CONTRACT
        // ==========================================

        describe('Auth API Contract', () => {
            it('should return JWT token on successful login', async () => {
                await provider.addInteraction({
                    state: 'user admin@oxford.edu exists',
                    uponReceiving: 'a login request with valid credentials',
                    withRequest: {
                        method: 'POST',
                        path: '/api/login_check',
                        headers: {
                            'Content-Type': 'application/json',
                        },
                        body: {
                            username: string('admin@oxford.edu'),
                            password: string('oxford123'),
                        },
                    },
                    willRespondWith: {
                        status: 200,
                        body: {
                            token: term({
                                matcher: '^eyJ.+$',
                                generate: 'eyJhbGciOiJSUzI1NiJ9...',
                            }),
                        },
                    },
                });

                const response = await fetch(
                    `${provider.mockService.baseUrl}/api/login_check`,
                    {
                        method: 'POST',
                        headers: { 'Content-Type': 'application/json' },
                        body: JSON.stringify({
                            username: 'admin@oxford.edu',
                            password: 'oxford123',
                        }),
                    }
                );

                expect(response.status).toBe(200);
                const data = await response.json();
                expect(data.token).toMatch(/^eyJ/);
            });

            it('should return 401 for invalid credentials', async () => {
                await provider.addInteraction({
                    state: 'user does not exist',
                    uponReceiving: 'a login request with invalid credentials',
                    withRequest: {
                        method: 'POST',
                        path: '/api/login_check',
                        headers: {
                            'Content-Type': 'application/json',
                        },
                        body: {
                            username: string('invalid@test.com'),
                            password: string('wrongpassword'),
                        },
                    },
                    willRespondWith: {
                        status: 401,
                        body: {
                            message: string('Invalid credentials.'),
                        },
                    },
                });

                const response = await fetch(
                    `${provider.mockService.baseUrl}/api/login_check`,
                    {
                        method: 'POST',
                        headers: { 'Content-Type': 'application/json' },
                        body: JSON.stringify({
                            username: 'invalid@test.com',
                            password: 'wrongpassword',
                        }),
                    }
                );

                expect(response.status).toBe(401);
            });
        });

        // ==========================================
        // GRADES API CONTRACT
        // ==========================================

        describe('Grades API Contract', () => {
            it('should return grades for a student', async () => {
                await provider.addInteraction({
                    state: 'grades exist for student 1',
                    uponReceiving: 'a request for student grades',
                    withRequest: {
                        method: 'GET',
                        path: '/api/grades',
                        query: { student_id: '1' },
                        headers: {
                            Authorization: like('Bearer token'),
                        },
                    },
                    willRespondWith: {
                        status: 200,
                        body: eachLike({
                            id: integer(1),
                            studentId: integer(1),
                            subjectId: integer(1),
                            subjectName: string('Matemáticas'),
                            grade: like(85.5),
                            period: string('1'),
                            status: term({
                                matcher: 'passed|failed',
                                generate: 'passed',
                            }),
                        }),
                    },
                });

                const response = await fetch(
                    `${provider.mockService.baseUrl}/api/grades?student_id=1`,
                    {
                        headers: { Authorization: 'Bearer token' },
                    }
                );

                expect(response.status).toBe(200);
            });
        });
    }
);
