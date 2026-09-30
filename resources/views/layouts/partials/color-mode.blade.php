{{-- Applique le mode sombre avant le rendu pour éviter tout clignotement. --}}
<script>
    (function () {
        try {
            var saved = localStorage.getItem('dark');
            var dark = saved === null
                ? window.matchMedia('(prefers-color-scheme: dark)').matches
                : saved === '1';
            document.documentElement.classList.toggle('dark', dark);
        } catch (e) {}
    })();

    // Bascule clair/sombre : révélation circulaire du nouveau mode depuis le
    // point cliqué, par-dessus une capture figée de l'ancien écran.
    window.toggleColorMode = function (event) {
        var apply = function () {
            var dark = document.documentElement.classList.toggle('dark');
            try { localStorage.setItem('dark', dark ? '1' : '0'); } catch (e) {}
        };

        var reduce = window.matchMedia('(prefers-reduced-motion: reduce)').matches;

        // Repli sans animation si l'API n'existe pas ou animations réduites.
        if (!document.startViewTransition || reduce || !event) {
            apply();
            return;
        }

        var x = event.clientX;
        var y = event.clientY;
        // Rayon atteignant le coin le plus éloigné (ici, en bas à gauche).
        var endRadius = Math.hypot(
            Math.max(x, window.innerWidth - x),
            Math.max(y, window.innerHeight - y)
        );

        var transition = document.startViewTransition(apply);

        transition.ready.then(function () {
            document.documentElement.animate(
                {
                    clipPath: [
                        'circle(0px at ' + x + 'px ' + y + 'px)',
                        'circle(' + endRadius + 'px at ' + x + 'px ' + y + 'px)',
                    ],
                },
                {
                    duration: 550,
                    easing: 'ease-in-out',
                    pseudoElement: '::view-transition-new(root)',
                }
            );
        });
    };
</script>
