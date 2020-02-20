/*
 * Welcome to your app's main JavaScript file!
 *
 * We recommend including the built version of this JavaScript file
 * (and its CSS file) in your base layout (base.html.twig).
 */

// any CSS you require will output into a single css file (app.css in this case)
require('../css/customs_sonata.css');
require('dropzone/dist/min/basic.min.css');

// Need jQuery? Install it with "yarn add jquery", then uncomment to require it.
// const $ = require('jquery');

const routes = require('../../public/js/fos_js_routes.json');
import Routing from '../../vendor/friendsofsymfony/jsrouting-bundle/Resources/public/js/router.min.js';

window.Dropzone = require('dropzone/dist/min/dropzone.min');
// console.log('Hello Webpack Encore! Edit me in assets/js/app.js');

$(document).ready(function() {
    console.log('assets/js/app.js on Ready trigger');
    $('[data-toggle="popover"]').popover();
    // FB.getLoginStatus(function(response) {
    //     console.log('getLoginStatus');
    //     statusChangeCallback(response);
    // });
});

// console.log('Hello Webpack Encore! Edit me in assets/js/app.js');