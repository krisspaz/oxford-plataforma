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
                    primary: '#002046',   // OBS Navy
                    secondary: '#D0006F', // OBS Pink
                    accent: '#69B32D',    // OBS Green
                    blue: '#009CDC',      // OBS Blue
                    orange: '#F39200',    // OBS Orange
                },
                obs: {
                    navy: '#002046',
                    pink: '#D0006F',
                    green: '#69B32D',
                    blue: '#009CDC',
                    orange: '#F39200',
                    purple: '#5D2E86',    // OBS Purple
                }
            }
        },
    },
    plugins: [],
}
