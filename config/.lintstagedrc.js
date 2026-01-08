module.exports = {
    '*.{js,jsx,ts,tsx}': ['eslint --fix', 'prettier --write'],
    '*.{json,md,yml,yaml}': ['prettier --write'],
    '*.php': ['php -l'],
    '*.py': ['ruff check --fix'],
};
