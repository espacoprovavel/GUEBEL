/**
 * Guebel — menu mobile e contador do cesto (fallback sem WooCommerce).
 */
document.addEventListener('DOMContentLoaded', function () {
  var toggle = document.querySelector('[data-menu-toggle]');
  var nav = document.querySelector('[data-mobile-nav]');

  if (toggle && nav) {
    toggle.addEventListener('click', function () {
      var open = nav.hasAttribute('hidden');
      if (open) {
        nav.removeAttribute('hidden');
      } else {
        nav.setAttribute('hidden', '');
      }
      toggle.setAttribute('aria-expanded', open ? 'true' : 'false');
    });

    nav.querySelectorAll('a').forEach(function (link) {
      link.addEventListener('click', function () {
        nav.setAttribute('hidden', '');
        toggle.setAttribute('aria-expanded', 'false');
      });
    });
  }

  // Sem WooCommerce activo os botões são apenas demonstrativos.
  if (document.body.classList.contains('woocommerce-active')) {
    return;
  }

  var count = 0;
  var badge = document.querySelector('[data-cart-count]');
  document.querySelectorAll('[data-add-to-cart]').forEach(function (btn) {
    btn.addEventListener('click', function () {
      count += 1;
      if (badge) {
        badge.textContent = String(count);
      }
    });
  });
});
