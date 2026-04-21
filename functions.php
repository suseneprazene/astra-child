<?php
/*
Theme Name: GeneratePress Child
Description: SUPR child theme – černobílý design systém pro WooCommerce eshop.
Author: suseneprazene.cz
Template: generatepress
Version: 1.1.0
*/

// Enqueue parent styles
function generatepress_child_enqueue_styles() {
    wp_enqueue_style('generatepress-parent-style', get_template_directory_uri() . '/style.css');
    wp_enqueue_style('generatepress-child-style', get_stylesheet_uri(), array('generatepress-parent-style'));
}
add_action('wp_enqueue_scripts', 'generatepress_child_enqueue_styles');

// ── Cabinet Grotesk font z Fontshare ────────────────────────
function sp_enqueue_fonts() {
    wp_enqueue_style(
        'cabinet-grotesk',
        'https://api.fontshare.com/v2/css?f[]=cabinet-grotesk@800,700,500,400&display=swap',
        [],
        null
    );
}
add_action('wp_enqueue_scripts', 'sp_enqueue_fonts');

// ── Hamburger menu JS (inline) ───────────────────────────────
function sp_hamburger_script() {
    ?>
    <script>
    document.addEventListener('DOMContentLoaded', function() {
        var toggle = document.querySelector('.sp-hamburger');
        if (toggle) {
            toggle.addEventListener('click', function() {
                document.body.classList.toggle('menu-open');
                var expanded = document.body.classList.contains('menu-open');
                toggle.setAttribute('aria-expanded', expanded ? 'true' : 'false');
            });
        }
    });
    </script>
    <?php
}
add_action('wp_footer', 'sp_hamburger_script');

// ── Hamburger button do headeru ──────────────────────────────
function sp_add_hamburger_button() {
    echo '<button class="sp-hamburger" aria-label="Menu" aria-expanded="false">
        <span></span><span></span><span></span>
    </button>';
}
add_action('generate_before_header_content', 'sp_add_hamburger_button');

