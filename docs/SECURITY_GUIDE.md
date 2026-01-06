# Security Guide & Best Practices

## 1. Authentication
- **Strong Passwords**: Enforced minimum 8 chars, uppercase, lowercase, numbers.
- **Rate Limiting**: Configuration in `rate_limiter.yaml` protects against brute force (5 attempts/min).
- **JWT**: Tokens expire hourly. Refresh tokens used for sessions.

## 2. Input Validation
- **Global Validation**: All controllers use Symfony Validator or Pydantic (AI Service).
- **Prepared Statements**: Doctrine ORM handles SQL injection protection automatically.
- **XSS Protection**: Frontend React escapes content by default. Use `purify` if `dangerouslySetInnerHTML` is ever needed.

## 3. Data Protection
- **Secrets Management**: Setup `.env` files outside web root. Never commit production secrets.
- **Backups**: Scripts available in `scripts/backup_db.sh`.
- **Encryption**: Passwords hashed with Argon2i.

## 4. Network
- **HTTPS**: Mandatory in production.
- **CORS**: Strict allow-list for frontend domain only.

## 5. Audit
- **Logs**: Monolog configured for JSON output. Review `var/log/prod.log` regularily.
