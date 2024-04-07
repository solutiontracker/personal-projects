module.exports = {
    env: {
        es6: true,
        node: true,
        jest: true,
    },
    "extends": [
        'eslint:recommended',
        'plugin:react/recommended',
        'plugin:react-hooks/recommended',
        'plugin:@typescript-eslint/eslint-recommended',
        'plugin:@typescript-eslint/recommended',
        'plugin:@typescript-eslint/recommended-requiring-type-checking',
    ],
    plugins: ['react', 'react-hooks', '@typescript-eslint', 'prettier'],
    parserOptions: {
        project: './tsconfig.json'
    },
    "plugins": [
        "react",
        "@typescript-eslint"
    ],
    "rules": {
        indent: ['error', 2, { SwitchCase: 1 }],
        quotes: ['error', 'single', { avoidEscape: true }],
        'no-empty-function': 'off',
        '@typescript-eslint/no-empty-function': 'off',
        '@typescript-eslint/no-unsafe-member-access': 'off',
        'react/display-name': 'off',
        'react-hooks/exhaustive-deps': 'off',
        '@typescript-eslint/no-unsafe-assignment': 'off',
        '@typescript-eslint/no-unsafe-call': 'off',
        '@typescript-eslint/no-explicit-any': 'off',
        'react/prop-types': 'off',
        'prettier/prettier': 'error',
    },
    settings: {
      react: {
        version: 'detect',
      },
    }
}
