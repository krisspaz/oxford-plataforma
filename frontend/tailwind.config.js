/** @type {import('tailwindcss').Config} */
export default {
    content: [
        "./index.html",
        "./src/**/*.{js,ts,jsx,tsx}",
    ],
    theme: {
        extend: {
            colors: {
                oxford: {
                    primary: '#1e3a8a',   // Azul oscuro corporativo
                    secondary: '#1d4ed8', // Azul brillante
                    accent: '#fbbf24',    // Dorado/Amarillo (común en escolares)
                }
            }
        },
    },
    plugins: [],
}
