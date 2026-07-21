

import Alpine from 'alpinejs';

window.Alpine = Alpine;

document.addEventListener('DOMContentLoaded', () => Alpine.start());

// Permet au serveur d'adapter certains réglages (ex: taille de pagination)
// à la largeur d'écran réelle, via un cookie lu côté back-end.
function syncViewportCookie() {
    const viewport = window.matchMedia('(max-width: 767px)').matches ? 'mobile' : 'desktop';
    document.cookie = `viewport=${viewport};path=/;max-age=31536000;samesite=lax`;
}

syncViewportCookie();
window.addEventListener('resize', syncViewportCookie);
