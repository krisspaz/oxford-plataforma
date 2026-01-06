import { useEffect } from 'react';

const useKeyboardShortcut = (key, callback, metaKey = false) => {
    useEffect(() => {
        const handleKeyDown = (event) => {
            if (event.key === key && (!metaKey || (event.metaKey || event.ctrlKey))) {
                event.preventDefault();
                callback();
            }
        };

        window.addEventListener('keydown', handleKeyDown);
        return () => window.removeEventListener('keydown', handleKeyDown);
    }, [key, callback, metaKey]);
};

export default useKeyboardShortcut;
