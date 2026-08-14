<?php
/* =========================================================
   FALM - Comparador Antes/Después (tarjeta en grilla + fullscreen)
   ========================================================= */

function falm_compare_shortcode( $atts ) {
    $atts = shortcode_atts( array(
        'before'       => '',
        'after'        => '',
        'label_before' => 'Antes',
        'label_after'  => 'Ahora',
    ), $atts );

    ob_start();
    ?>
    <style>
        .falm-compare-wrap {
            position: relative;
            width: 100%;
            overflow: hidden;
        }

        .falm-compare {
            position: relative;
            width: 100%;
            height: 100%;
            overflow: hidden;
            cursor: ew-resize;
            user-select: none;
            touch-action: none;
            -webkit-user-select: none;
            -webkit-touch-callout: none;
            background: #000;
        }

        .falm-compare:fullscreen,
        .falm-compare:-webkit-full-screen {
            width: 100%;
            height: 100%;
        }

        .falm-compare img {
            position: absolute !important;
            top: 0 !important;
            left: 0 !important;
            width: 100% !important;
            height: 100% !important;
            max-width: none !important;
            max-height: none !important;
            object-fit: cover;
            object-position: center;
            display: block;
            pointer-events: none;
        }

        .falm-compare .falm-after {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            overflow: hidden;
        }

        .falm-compare .falm-handle {
            position: absolute;
            top: 0;
            bottom: 0;
            left: 50%;
            width: 3px;
            background: #fff;
            transform: translateX(-50%);
            pointer-events: none;
            box-shadow: 0 0 6px rgba(0,0,0,.5);
        }

        .falm-compare .falm-handle::after {
            content: '';
            position: absolute;
            top: 50%;
            left: 50%;
            width: 36px;
            height: 36px;
            border-radius: 50%;
            background: #fff;
            transform: translate(-50%, -50%);
            box-shadow: 0 0 8px rgba(0,0,0,.4);
        }

        .falm-compare .falm-label {
            position: absolute;
            top: 12px;
            padding: 5px 12px;
            background: rgba(0,0,0,.6);
            color: #fff;
            font-family: sans-serif;
            font-size: 20px;
            border-radius: 4px;
            pointer-events: none;
            z-index: 3;
        }

        .falm-compare .falm-label-before { left: 12px; }
        .falm-compare .falm-label-after  { right: 12px; }
    </style>

    <div class="falm-compare-wrap">
        <div class="falm-compare">
            <img src="<?php echo esc_url( $atts['before'] ); ?>" alt="Antes">

            <div class="falm-after">
                <img src="<?php echo esc_url( $atts['after'] ); ?>" alt="Ahora">
            </div>

            <div class="falm-handle"></div>
            <div class="falm-label falm-label-before"><?php echo esc_html( $atts['label_before'] ); ?></div>
            <div class="falm-label falm-label-after"><?php echo esc_html( $atts['label_after'] ); ?></div>
        </div>
    </div>

    <script>
    (function () {
        function setHeight(wrap) {
            var w = wrap.getBoundingClientRect().width;
            wrap.style.height = Math.round(w * 9 / 16) + 'px';
        }

        function initFalmCompare(container) {
            if (container.dataset.falmInit) return;
            container.dataset.falmInit = '1';

            var wrap    = container.closest('.falm-compare-wrap');
            var afterEl = container.querySelector('.falm-after');
            var handleEl= container.querySelector('.falm-handle');
            var dragging = false;

            setHeight(wrap);
            window.addEventListener('resize', function () { setHeight(wrap); });

            function setPosition(percent) {
                percent = Math.max(0, Math.min(100, percent));
                // Recortamos desde la IZQUIERDA, así lo que se ve de "after"
                // queda del lado derecho (y "before" queda visible a la izquierda).
                afterEl.style.clipPath = 'inset(0 0 0 ' + percent + '%)';
                handleEl.style.left = percent + '%';
            }

            function percentFromX(clientX) {
                var rect = container.getBoundingClientRect();
                return ( (clientX - rect.left) / rect.width ) * 100;
            }

            container.addEventListener('mousedown', function (e) {
                dragging = true;
                setPosition( percentFromX(e.clientX) );
            });
            window.addEventListener('mousemove', function (e) {
                if (!dragging) return;
                setPosition( percentFromX(e.clientX) );
            });
            window.addEventListener('mouseup', function () {
                dragging = false;
            });

            container.addEventListener('touchstart', function (e) {
                dragging = true;
                setPosition( percentFromX(e.touches[0].clientX) );
                e.preventDefault();
            }, { passive: false });

            container.addEventListener('touchmove', function (e) {
                if (!dragging) return;
                setPosition( percentFromX(e.touches[0].clientX) );
                e.preventDefault();
            }, { passive: false });

            container.addEventListener('touchend', function (e) {
                dragging = false;
                e.preventDefault();
            }, { passive: false });

            container.addEventListener('touchcancel', function () {
                dragging = false;
            });

            setPosition(50);
        }

        function initAll() {
            document.querySelectorAll('.falm-compare').forEach(initFalmCompare);
        }

        if (document.readyState === 'loading') {
            document.addEventListener('DOMContentLoaded', initAll);
        } else {
            initAll();
        }
    })();
    </script>
    <?php
    return ob_get_clean();
}
add_shortcode( 'falm_compare', 'falm_compare_shortcode' );


// 2. Accesorios: sticker "Deslízame"
function falm_compare_accessories() {
    ?>
    <style>
        .falm-compare .falm-hint {
            position: absolute !important;
            top: 25% !important;
            left: 50%;
            transform: translate(-50%, -50%) !important;
            pointer-events: none !important;
            opacity: 1;
            transition: opacity .6s ease;
            z-index: 10 !important;
        }

        .falm-compare .falm-hint.falm-hint-hidden {
            opacity: 0;
        }

        .falm-compare .falm-hint img {
            display: block !important;
            width: 44px !important;
            height: 44px !important;
            min-width: 44px !important;
            min-height: 44px !important;
            max-width: 44px !important;
            max-height: 44px !important;
            margin: 0 !important;
            padding: 0 !important;
            border: none !important;
            border-radius: 0 !important;
            object-fit: contain;
            filter: drop-shadow(0 2px 5px rgba(0,0,0,.65)) drop-shadow(0 0 3px rgba(0,0,0,.5));
        }

        .falm-compare:fullscreen .falm-hint img,
        .falm-compare:-webkit-full-screen .falm-hint img {
            width: 64px !important;
            height: 64px !important;
            min-width: 64px !important;
            min-height: 64px !important;
            max-width: 64px !important;
            max-height: 64px !important;
        }
    </style>

    <script>
    (function () {
        function initHint(container) {
            if (container.querySelector('.falm-hint')) return;

            var handleEl = container.querySelector('.falm-handle');
            var afterEl  = container.querySelector('.falm-after');
            if (!handleEl || !afterEl) return;

            // 👉 URL de tu sticker en la Media Library
            var hintImageUrl = 'https://falm.cl/wp-content/uploads/2026/08/Deslizar.webp';

            var hintEl = document.createElement('div');
            hintEl.className = 'falm-hint';
            hintEl.innerHTML = '<img width="44" height="44" src="' + hintImageUrl + '" alt="Deslizar">';
            container.appendChild(hintEl);

            var idleFrameId   = null;
            var idleTimeoutId = null;
            var idleStartTime = null;
            var interacting   = false;

            function setSwayPosition(percent) {
                // Misma dirección de recorte que setPosition() en el snippet base,
                // así el comportamiento durante el vaivén automático coincide
                // con el del arrastre manual.
                afterEl.style.clipPath = 'inset(0 0 0 ' + percent + '%)';
                handleEl.style.left   = percent + '%';
                hintEl.style.left     = percent + '%';
            }

            function idleTick(now) {
                if (idleStartTime === null) idleStartTime = now;
                var elapsed = (now - idleStartTime) / 1000;
                var percent = 50 + Math.sin(elapsed * 0.8) * 10;
                setSwayPosition(percent);
                idleFrameId = requestAnimationFrame(idleTick);
            }

            function startIdle() {
                hintEl.classList.remove('falm-hint-hidden');
                idleStartTime = null;
                if (!idleFrameId) idleFrameId = requestAnimationFrame(idleTick);
            }

            function stopIdle() {
                if (idleFrameId) { cancelAnimationFrame(idleFrameId); idleFrameId = null; }
                hintEl.classList.add('falm-hint-hidden');
            }

            function onInteractionStart() {
                interacting = true;
                stopIdle();
                if (idleTimeoutId) { clearTimeout(idleTimeoutId); idleTimeoutId = null; }
            }

            function onInteractionEnd() {
                interacting = false;
                idleTimeoutId = setTimeout(startIdle, 2000);
            }

            container.addEventListener('mousedown', onInteractionStart);
            window.addEventListener('mouseup', function () {
                if (interacting) onInteractionEnd();
            });

            container.addEventListener('touchstart', onInteractionStart, { passive: true });
            container.addEventListener('touchend', function () {
                if (interacting) onInteractionEnd();
            });
            container.addEventListener('touchcancel', function () {
                if (interacting) onInteractionEnd();
            });

            startIdle();
        }

        function initAll() {
            document.querySelectorAll('.falm-compare').forEach(initHint);
        }

        if (document.readyState === 'loading') {
            document.addEventListener('DOMContentLoaded', initAll);
        } else {
            initAll();
        }
    })();
    </script>
    <?php
}
add_action( 'wp_footer', 'falm_compare_accessories', 999 );


// 3. Botón de pantalla completa
function falm_compare_buttons() {
    ?>
    <style>
        .falm-compare .falm-fullscreen-btn {
            position: absolute;
            bottom: 16px;
            right: 16px;
            width: 40px;
            height: 40px;
            border-radius: 50%;
            background: rgba(0,0,0,.55);
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            z-index: 100000;
            border: none;
            padding: 0;
            touch-action: manipulation;
            opacity: 1;
            transition: opacity .6s ease;
        }

        .falm-compare .falm-fullscreen-btn.falm-btn-hidden {
            opacity: 0;
            pointer-events: none;
        }

        .falm-compare .falm-fullscreen-btn svg {
            width: 20px;
            height: 20px;
            display: block;
            pointer-events: none;
        }
    </style>

    <script>
    (function () {
        function initButton(container) {
            if (container.querySelector('.falm-fullscreen-btn')) return;

            var expandIcon =
                '<svg viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">' +
                    '<path d="M4 9V4h5M20 9V4h-5M4 15v5h5M20 15v5h-5" stroke="white" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>' +
                '</svg>';

            var collapseIcon =
                '<svg viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">' +
                    '<path d="M9 4v5H4M15 4v5h5M9 20v-5H4M15 20v-5h5" stroke="white" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>' +
                '</svg>';

            var btn = document.createElement('button');
            btn.className = 'falm-fullscreen-btn';
            btn.innerHTML = expandIcon;
            btn.setAttribute('aria-label', 'Pantalla completa');
            container.appendChild(btn);

            function isFullscreen() {
                return document.fullscreenElement === container || document.webkitFullscreenElement === container;
            }
            function enterFullscreen() {
                if (container.requestFullscreen) container.requestFullscreen();
                else if (container.webkitRequestFullscreen) container.webkitRequestFullscreen();
            }
            function exitFullscreen() {
                if (document.exitFullscreen) document.exitFullscreen();
                else if (document.webkitExitFullscreen) document.webkitExitFullscreen();
            }
            function toggleFullscreen() {
                if (isFullscreen()) exitFullscreen();
                else enterFullscreen();
            }
            function updateIcon() {
                btn.innerHTML = isFullscreen() ? collapseIcon : expandIcon;
            }

            btn.addEventListener('mousedown', function (e) {
                e.stopPropagation();
            });
            btn.addEventListener('click', function (e) {
                e.stopPropagation();
                toggleFullscreen();
            });
            btn.addEventListener('touchstart', function (e) {
                e.stopPropagation();
            }, { passive: true });
            btn.addEventListener('touchend', function (e) {
                e.stopPropagation();
                e.preventDefault();
                toggleFullscreen();
            }, { passive: false });

            document.addEventListener('fullscreenchange', updateIcon);
            document.addEventListener('webkitfullscreenchange', updateIcon);

            var hideTimeoutId = null;

            function showButton() {
                btn.classList.remove('falm-btn-hidden');
                if (hideTimeoutId) clearTimeout(hideTimeoutId);
                hideTimeoutId = setTimeout(function () {
                    btn.classList.add('falm-btn-hidden');
                }, 3000);
            }

            container.addEventListener('mousemove', showButton);
            container.addEventListener('mousedown', showButton);
            container.addEventListener('touchstart', showButton, { passive: true });
            btn.addEventListener('mouseenter', showButton);

            showButton();
        }

        function initAll() {
            document.querySelectorAll('.falm-compare').forEach(initButton);
        }

        if (document.readyState === 'loading') {
            document.addEventListener('DOMContentLoaded', initAll);
        } else {
            initAll();
        }
    })();
    </script>
    <?php
}
add_action( 'wp_footer', 'falm_compare_buttons', 999 );
