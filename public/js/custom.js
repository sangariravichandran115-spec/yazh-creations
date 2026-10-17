(function () {
    'use strict';

    // Product gallery: swap main image on thumbnail click.
    document.querySelectorAll('.product-gallery-thumb').forEach(function (thumb) {
        thumb.addEventListener('click', function () {
            var src = thumb.getAttribute('data-gallery-src');
            var alt = thumb.getAttribute('data-gallery-alt');
            var main = document.getElementById('gallery-main');

            if (!main || !src) {
                return;
            }

            main.src = src;
            main.alt = alt || main.alt;

            document.querySelectorAll('.product-gallery-thumb').forEach(function (other) {
                other.classList.remove('active');
            });
            thumb.classList.add('active');
        });
    });

    // WhatsApp button on the product page: rebuild the message with the chosen size.
    var waButton = document.querySelector('[data-wa-product]');
    var waSizeSelect = document.getElementById('wa_size_select');

    if (waButton && waSizeSelect) {
        var waBase = waButton.href.split('?')[0];

        function buildWhatsAppLink(size) {
            var lines = [
                'Hi Yazh Creations,',
                '',
                'I\'m interested in:',
                '',
                'Product: ' + waButton.getAttribute('data-wa-product'),
                'Price: \u20B9' + waButton.getAttribute('data-wa-price')
            ];

            if (size) {
                lines.push('Size: ' + size);
            }

            lines.push('', 'Is this product available?');

            waButton.href = waBase + '?' + new URLSearchParams({ text: lines.join('\n') });
        }

        waSizeSelect.addEventListener('change', function () {
            buildWhatsAppLink(waSizeSelect.value);
        });
    }

    // Admin sidebar: persist collapsed state through the Bootstrap collapse element.
    var sidebar = document.getElementById('adminSidebar');

    if (sidebar) {
        var bsSidebar = new bootstrap.Collapse(sidebar, { toggle: false });

        if (window.innerWidth < 992) {
            sidebar.classList.remove('show');
        }

        sidebar.addEventListener('hidden.bs.collapse', function () {
            document.querySelectorAll('[data-bs-toggle="collapse"][data-bs-target="#adminSidebar"]').forEach(function (toggle) {
                toggle.setAttribute('aria-expanded', 'false');
            });
        });
    }
})();