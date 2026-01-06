# Testing Guide

## Backend (Symfony)
Run unit and integration tests using PHPUnit.

### Commands
```bash
# Run all tests
php bin/phpunit

# Run specific suite
php bin/phpunit --testsuite=Unit
```

### Writing Tests
- **Controllers**: Extend `WebTestCase`. Mock authentication.
- **Services**: Extend `TestCase`. Mock repositories.
- **Coverage**: target > 80%.

## Frontend (React)
Uses Vitest + React Testing Library.

### Commands
```bash
# Run tests
npm test

# Coverage report
npm run coverage
```

## AI Service (Python)
Uses Pytest.

### Commands
```bash
pytest
```
