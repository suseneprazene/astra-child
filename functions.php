<?php
/*
Theme Name: Astra Child
Description: Astra child theme – moderní černobílý design.
Author: tvé_jméno
Template: astra
Version: 1.0.0
*/

// Načtení stylů child + parent theme + Google fonts
function astra_child_enqueue_styles() {
    wp_enqueue_style( 'astra-parent-style', get_template_directory_uri() . '/style.css' );
    wp_enqueue_style( 'astra-child-style', get_stylesheet_uri(), array('astra-parent-style'), '1.0' );
}
add_action( 'wp_enqueue_scripts', 'astra_child_enqueue_styles' );

// Volitelně – načti Cabinet Grotesk zvlášť, pokud Astra nebo pluginy blokují @import v CSS:
add_action( 'wp_enqueue_scripts', function() {
    wp_enqueue_style(
        'cabinet-grotesk-font',
        'https://api.fontshare.com/v2/css?f[]=cabinet-grotesk@700,400&display=swap',
        false,
        null
    );
});

// Tady můžeš přidat vlastní funkce/hooky pro Astra (viz https://wpastra.com/docs/astra-action-hooks/)

/* Př. vlastní hook do headeru:
add_action('astra_header', function() {
   ?>
   // sem dej HTML/JS pro custom header věci podle potřeby
   <?php
});
*/

// … další PHP úpravy (widgety, WooCommerce aj.) sem ↓