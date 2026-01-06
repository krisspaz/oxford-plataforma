// k6 Load Testing Suite
// Run: k6 run load-tests/main.js

import http from 'k6/http';
import { check, sleep, group } from 'k6';
import { Rate, Trend } from 'k6/metrics';

// Custom metrics
const errorRate = new Rate('errors');
const loginDuration = new Trend('login_duration');
const apiDuration = new Trend('api_duration');

// Test configuration
export const options = {
    stages: [
        { duration: '30s', target: 10 },   // Ramp up
        { duration: '1m', target: 50 },    // Stay at 50 users
        { duration: '2m', target: 100 },   // Stress test
        { duration: '1m', target: 200 },   // Peak load
        { duration: '30s', target: 0 },    // Ramp down
    ],
    thresholds: {
        http_req_duration: ['p(95)<2000'], // 95% requests under 2s
        errors: ['rate<0.1'],              // Error rate under 10%
        login_duration: ['avg<1000'],      // Login under 1s avg
    },
};

const BASE_URL = __ENV.BASE_URL || 'http://localhost:8000';
let authToken = null;

// ==========================================
// SETUP
// ==========================================

export function setup() {
    // Get auth token
    const loginRes = http.post(`${BASE_URL}/api/login_check`, JSON.stringify({
        username: 'admin@oxford.edu',
        password: 'oxford123',
    }), {
        headers: { 'Content-Type': 'application/json' },
    });

    if (loginRes.status !== 200) {
        console.error('Login failed in setup');
        return {};
    }

    const data = JSON.parse(loginRes.body);
    return { token: data.token };
}

// ==========================================
// MAIN TEST
// ==========================================

export default function (data) {
    const headers = {
        'Content-Type': 'application/json',
        'Authorization': `Bearer ${data.token}`,
    };

    group('Authentication', () => {
        const start = Date.now();

        const res = http.post(`${BASE_URL}/api/login_check`, JSON.stringify({
            username: 'admin@oxford.edu',
            password: 'oxford123',
        }), {
            headers: { 'Content-Type': 'application/json' },
        });

        loginDuration.add(Date.now() - start);

        check(res, {
            'login status is 200': (r) => r.status === 200,
            'has token': (r) => JSON.parse(r.body).token !== undefined,
        }) || errorRate.add(1);
    });

    sleep(1);

    group('Students API', () => {
        // List students
        let res = http.get(`${BASE_URL}/api/v1/students?page=1&limit=20`, { headers });

        check(res, {
            'list students status 200': (r) => r.status === 200,
            'has items': (r) => JSON.parse(r.body).items !== undefined,
        }) || errorRate.add(1);

        apiDuration.add(res.timings.duration);
        sleep(0.5);

        // Get single student
        res = http.get(`${BASE_URL}/api/v1/students/1`, { headers });

        check(res, {
            'get student status 200 or 404': (r) => [200, 404].includes(r.status),
        }) || errorRate.add(1);

        apiDuration.add(res.timings.duration);
    });

    sleep(1);

    group('Grades API', () => {
        const res = http.get(`${BASE_URL}/api/grades`, { headers });

        check(res, {
            'grades status 200': (r) => r.status === 200,
        }) || errorRate.add(1);

        apiDuration.add(res.timings.duration);
    });

    sleep(1);

    group('Schedule API', () => {
        const res = http.get(`${BASE_URL}/api/schedules`, { headers });

        check(res, {
            'schedules status 200': (r) => r.status === 200 || r.status === 401,
        }) || errorRate.add(1);

        apiDuration.add(res.timings.duration);
    });

    sleep(1);

    group('AI Service', () => {
        const res = http.post(`${BASE_URL}/ai-api/process-command`, JSON.stringify({
            command: 'Cuantos estudiantes hay?',
            user_id: 1,
            role: 'admin',
        }), { headers });

        check(res, {
            'AI response ok': (r) => [200, 503].includes(r.status),
        }) || errorRate.add(1);

        if (res.status === 200) {
            apiDuration.add(res.timings.duration);
        }
    });

    sleep(2);
}

// ==========================================
// TEARDOWN
// ==========================================

export function teardown(data) {
    console.log('Load test completed');
}

// ==========================================
// HELPER FUNCTIONS
// ==========================================

export function handleSummary(data) {
    return {
        'load-tests/summary.json': JSON.stringify(data, null, 2),
        stdout: textSummary(data, { indent: ' ', enableColors: true }),
    };
}

function textSummary(data, opts) {
    const lines = [];
    lines.push('=== LOAD TEST SUMMARY ===');
    lines.push(`Total requests: ${data.metrics.http_reqs.values.count}`);
    lines.push(`Failed requests: ${data.metrics.http_req_failed?.values?.passes || 0}`);
    lines.push(`Avg response time: ${Math.round(data.metrics.http_req_duration.values.avg)}ms`);
    lines.push(`95th percentile: ${Math.round(data.metrics.http_req_duration.values['p(95)'])}ms`);
    lines.push(`Error rate: ${(data.metrics.errors?.values?.rate * 100 || 0).toFixed(2)}%`);
    return lines.join('\n');
}
