import vue from "eslint-plugin-vue";
import prettier from "eslint-config-prettier";
import vueParser from "vue-eslint-parser";

export default [
    {
        ignores: ['vendor/**', 'public/build/**'],
        files: ['**/*.js', '**/*.jsx', '**/*.vue'],
        languageOptions: {
            parser: vueParser,
            ecmaVersion: "latest",
            sourceType: "module",
        },
        plugins: {
            vue,
        },
        rules: {
            ...vue.configs["vue3-recommended"].rules,
            "no-console": "warn",
            "no-unused-vars": "warn",
            'vue/multi-word-component-names': 'off',
        },
    },
    prettier,
];
