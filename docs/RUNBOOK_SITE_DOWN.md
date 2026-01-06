# Runbook: Sitio Caído

## Síntomas
- Error 502/504 en navegador
- Alertas de UptimeRobot

## Pasos de Resolución
1. Verificar status de servidor
2. Reiniciar servicios (`systemctl restart nginx php8.3-fpm`)
3. Verificar logs (`tail -f /var/log/nginx/error.log`)
