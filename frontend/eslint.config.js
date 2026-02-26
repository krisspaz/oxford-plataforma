import js from '@eslint/js'
import globals from 'globals'
import reactHooks from 'eslint-plugin-react-hooks'
import reactRefresh from 'eslint-plugin-react-refresh'
import unusedImports from 'eslint-plugin-unused-imports'
import { defineConfig, globalIgnores } from 'eslint/config'

export default defineConfig([
    globalIgnores(['dist']),
    {
        files: ['**/*.{js,jsx}'],
        extends: [
            js.configs.recommended,
            reactHooks.configs.flat.recommended,
            reactRefresh.configs.vite,
        ],
        plugins: {
            'unused-imports': unusedImports,
        },
        languageOptions: {
            ecmaVersion: 2020,
            globals: {
                ...globals.browser,
                ...globals.node,
                ...globals.jest,
                vi: 'readonly'
            },
            parserOptions: {
                ecmaVersion: 'latest',
                ecmaFeatures: { jsx: true },
                sourceType: 'module',
            },
        },
        rules: {
            'no-unused-vars': 'off',
            'unused-imports/no-unused-imports': 'error',
            'unused-imports/no-unused-vars': [
                'error',
                { 'vars': 'all', 'varsIgnorePattern': '^[A-Z_]$', 'args': 'after-used', 'argsIgnorePattern': '^_' }
            ],
            'react-refresh/only-export-components': 'error',
            'react/prop-types': 'off',
        },
    },
])
