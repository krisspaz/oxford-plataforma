import i18n from 'i18next';
import { initReactI18next } from 'react-i18next';

// Import translation files
import esTranslation from './locales/es.json';
import enTranslation from './locales/en.json';

// Get browser language or stored preference
const getInitialLanguage = () => {
    // Check localStorage for saved preference
    const saved = localStorage.getItem('oxford-language');
    if (saved && ['es', 'en'].includes(saved)) {
        return saved;
    }

    // Check browser language
    const browserLang = navigator.language.split('-')[0];
    return ['es', 'en'].includes(browserLang) ? browserLang : 'es';
};

i18n
    .use(initReactI18next)
    .init({
        resources: {
            es: { translation: esTranslation },
            en: { translation: enTranslation },
        },
        lng: getInitialLanguage(),
        fallbackLng: 'es',

        interpolation: {
            escapeValue: false, // React already escapes
        },

        // Enable debugging in development
        debug: import.meta.env.DEV,

        // Keys without translation return the key itself
        returnEmptyString: false,

        // Detect changes to language
        react: {
            useSuspense: true,
        },
    });

// Save language preference when changed
i18n.on('languageChanged', (lng) => {
    localStorage.setItem('oxford-language', lng);
    document.documentElement.lang = lng;
});

export default i18n;
