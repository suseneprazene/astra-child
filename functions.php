<?php
/*
Theme Name: Astra Child
Description: Astra child theme – moderní černobílý design.
Author: tvé_jméno
Template: astra
Version: 1.0.0
*/

// Načtení stylů child + parent theme
function astra_child_enqueue_styles() {
    wp_enqueue_style( 'astra-parent-style', get_template_directory_uri() . '/style.css' );
    wp_enqueue_style( 'astra-child-style', get_stylesheet_uri(), array('astra-parent-style'), '1.0' );
}
add_action( 'wp_enqueue_scripts', 'astra_child_enqueue_styles' );

// Cabinet Grotesk font
add_action( 'wp_enqueue_scripts', function() {
    wp_enqueue_style(
        'cabinet-grotesk-font',
        'https://api.fontshare.com/v2/css?f[]=cabinet-grotesk@700,400&display=swap',
        false,
        null
    );
});

/**
 * Přebití Astra výchozích barev (modrá → černá) pomocí inline stylu.
 * Inline styl se načte PO všech ostatních stylech, takže má vyšší specificitu.
 * Toto je nejspolehlivější způsob, jak přebít Astra customizer hodnoty.
 */
add_action( 'wp_enqueue_scripts', function() {
    // Ujistíme se, že child styl je zaregistrovaný, pak přidáme inline CSS
    wp_add_inline_style( 'astra-child-style', '

        /* === Přebití Astra modré barvy odkazů a ikon === */

        /* Globální barva odkazů */
        a,
        a:visited {
            color: #111 !important;
        }
        a:hover,
        a:focus {
            color: #000 !important;
            text-decoration: none !important;
        }

        /* Astra vlastní CSS proměnné (pokud je téma používá) */
        :root {
            --ast-global-color-0: #111111 !important;
            --ast-global-color-1: #111111 !important;
        }

        /* Ikony košíku a účtu */
        .ast-cart-menu-wrap a,
        .ast-header-woo-cart a,
        .ast-header-account a,
        .ast-masthead-custom-menu-items a {
            color: #111 !important;
        }

        /* SVG ikony – fill */
        .ast-cart-menu-wrap svg,
        .ast-header-woo-cart svg,
        .ast-header-account svg,
        .ast-masthead-custom-menu-items svg {
            fill: #111 !important;
            color: #111 !important;
        }

        /* Badge počtu položek v košíku */
        .ast-cart-menu-wrap .count {
            background-color: #111 !important;
            color: #fff !important;
            border-color: #111 !important;
        }

        /* Menu položky */
        .main-navigation a,
        .main-header-menu a,
        #ast-fixed-header .main-header-menu a,
        .ast-main-header-bar-navigation a {
            color: #111 !important;
        }

        /* Woo: ceny, linky, tabs */
        .woocommerce a,
        .woocommerce-page a,
        .woocommerce ul.products li.product a,
        .woocommerce .woocommerce-breadcrumb a {
            color: #111 !important;
        }

        /* Výjimka – tlačítka v gridu i summary musí mít bílý text */
        .woocommerce ul.products li.product a.button,
        .woocommerce ul.products li.product .astra-shop-summary-wrap a.button,
        .woocommerce-page ul.products li.product a.button,
        .astra-shop-summary-wrap a.button,
        .astra-shop-summary-wrap a.single_add_to_cart_button,
        .astra-shop-summary-wrap a.add_to_cart_button {
            color: #fff !important;
            background-color: #111 !important;
        }

        /* Výjimka – košík ikonka (.ast-on-card-button) průhledná */
        .astra-shop-thumbnail-wrap a.ast-on-card-button {
            color: #111 !important;
            background: transparent !important;
            background-color: transparent !important;
        }

        /* Out-of-stock text */
        .ast-shop-product-out-of-stock {
            color: #111 !important;
        }

    ');
}, 99 ); // priorita 99 – načte se po Astra stylech

/**
 * JS: Přesune tlačítko "Odstranit položku" (.wc-block-cart-item__remove-link)
 * do buňky s cenou (.wc-block-cart-item__total) a zobrazí ho jako malé X.
 * Používáme MutationObserver, protože WC Blocks renderuje přes React.
 */
add_action( 'wp_footer', function() { ?>
<script>
(function() {
  function moveRemoveLinks() {
    document.querySelectorAll('.wc-block-cart-item__total').forEach(function(totalCell) {
      var row = totalCell.closest('tr, .wc-block-cart-item');
      if (!row) return;
      var removeLink = row.querySelector('.wc-block-cart-item__remove-link');
      if (!removeLink) return;
      if (totalCell.querySelector('.wc-block-cart-item__remove-link')) return;

      removeLink.classList.add('remove-x-btn');
      removeLink.innerHTML = '&times;';
      removeLink.setAttribute('title', 'Odstranit položku');
      totalCell.style.position = 'relative';
      totalCell.appendChild(removeLink);
    });
  }

  var observer = new MutationObserver(moveRemoveLinks);
  observer.observe(document.body, { childList: true, subtree: true });
  document.addEventListener('DOMContentLoaded', moveRemoveLinks);
})();
</script>
<?php });

/**
 * JS: Fix mobilního menu – Astra občas neregistruje klik mimo menu jako zavření.
 * Klik mimo .main-header-bar-navigation zavře menu manuálně.
 * Také přesune mini-cart do pravé sekce headeru na mobilu pokud tam není.
 */
add_action( 'wp_footer', function() { ?>
<script>
(function() {
  document.addEventListener('DOMContentLoaded', function() {

    // ── Fix zavírání mobilního menu ──────────────────────────────────────
    // Astra toggle funguje přes aria-expanded na .menu-toggle
    // Zavřeme menu při kliknutí mimo header nebo na odkaz v menu

    function closeMobileMenu() {
      var toggles = document.querySelectorAll('.menu-toggle[aria-expanded="true"]');
      toggles.forEach(function(toggle) {
        toggle.click();
      });
    }

    // Klik na odkaz v mobilním menu → zavřít
    document.addEventListener('click', function(e) {
      var nav = document.querySelector('.main-header-bar-navigation, #ast-fixed-header .main-header-bar-navigation');
      var toggle = document.querySelector('.menu-toggle');
      if (!nav || !toggle) return;

      // Klik byl mimo header → zavřít
      var header = document.querySelector('.main-header-bar, #masthead');
      if (header && !header.contains(e.target)) {
        if (toggle.getAttribute('aria-expanded') === 'true') {
          closeMobileMenu();
        }
        return;
      }

      // Klik na odkaz uvnitř menu → zavřít
      if (e.target.tagName === 'A' && nav.contains(e.target)) {
        setTimeout(closeMobileMenu, 100);
      }
    });

    // Klávesa Escape → zavřít menu
    document.addEventListener('keydown', function(e) {
      if (e.key === 'Escape') closeMobileMenu();
    });

    // ── Mini-cart v pravé sekci headeru na mobilu ────────────────────────
    // Zkontrolujeme, jestli je mini-cart uvnitř .site-header-primary-section-right
    // Pokud ne, zkusíme ho přesunout (záleží na Header Builder konfiguraci)
    function ensureCartInRightSection() {
      var rightSection = document.querySelector('.site-header-primary-section-right');
      if (!rightSection) return;

      // Pokud mini-cart nebo account ještě není v pravé sekci, zkusíme přidat
      // (funguje jen pokud jsou to siblové ve stejném flex containeru)
      var miniCart = document.querySelector('.ast-builder-layout-element .wc-block-mini-cart, .ast-builder-layout-element .ast-site-header-cart');
      var account  = document.querySelector('.ast-builder-layout-element.ast-header-account');

      if (miniCart) {
        var cartEl = miniCart.closest('.ast-builder-layout-element');
        if (cartEl && !rightSection.contains(cartEl)) {
          // Je ve stejném parent containeru? Pak přesuneme.
          if (cartEl.parentElement === rightSection.parentElement) {
            rightSection.insertBefore(cartEl, rightSection.querySelector('.ast-mobile-menu-trigger') || null);
          }
        }
      }
    }

    // Spustíme po načtení a po resize (breakpoint přechod)
    ensureCartInRightSection();
    window.addEventListener('resize', ensureCartInRightSection);
  });
})();
</script>
<?php });

// … další PHP úpravy (widgety, WooCommerce aj.) sem ↓

add_filter( 'woocommerce_get_availability_text', function( $text, $product ) {
    if ( ! $product->is_in_stock() ) {
        return 'Momentálně vyprodáno';
    }
    return $text;
}, 10, 2 );

add_filter( 'woocommerce_sale_flash', function( $html ) {
    return '<span class="onsale ast-on-card-button ast-onsale-card">Výhodněji</span>';
} );

// Překlad Astra labelů (Výprodej!, Nedostupné) přes gettext
add_filter( 'gettext', function( $translated, $text, $domain ) {
    switch ( $translated ) {
        case 'Sale!':
        case 'Výprodej!':
            return 'Výhodnější';
        case 'Unavailable':
        case 'Nedostupné':
            return 'Momentálně vyprodáno';
    }
    return $translated;
}, 20, 3 );