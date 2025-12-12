const mix = require("laravel-mix")

/*
 |--------------------------------------------------------------------------
 | Mix Asset Management
 |--------------------------------------------------------------------------
 |
 | Mix provides a clean, fluent API for defining some Webpack build steps
 | for your Laravel application. By default, we are compiling the Sass
 | file for the application as well as bundling up all the JS files.
 |
 */

import ClassicEditor from "@ckeditor/ckeditor5-build-classic"

document.addEventListener("DOMContentLoaded", () => {
    const editors = document.querySelectorAll(".ckeditor")
    editors.forEach((editor) => {
        ClassicEditor.create(editor).catch((error) => {
            console.error(error)
        })
    })
})
