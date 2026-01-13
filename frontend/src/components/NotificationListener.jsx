import React, { useEffect } from 'react';

const NotificationListener = () => {
    useEffect(() => {
        const url = new URL('http://localhost:3000/.well-known/mercure');
        url.searchParams.append('topic', 'http://oxford.edu/notifications');

        const eventSource = new EventSource(url);

        eventSource.onmessage = (event) => {
            const data = JSON.parse(event.data);
            if (data.status === 'COMPLETED') {
                // Determine if we can use the native Notification API or just an alert/toast
                // For MVP, alert or custom toast

                // You can replace this with a nice Toast component later (e.g. Sonner, React-Hot-Toast)
                const shouldDownload = window.confirm(`${data.message}\n\n¿Descargar ahora?`);
                if (shouldDownload && data.downloadUrl) {
                    window.open(data.downloadUrl, '_blank');
                }
            }
        };

        return () => {
            eventSource.close();
        };
    }, []);

    return null; // This component handles side effects only
};

export default NotificationListener;
