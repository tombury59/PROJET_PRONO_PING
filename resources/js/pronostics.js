import { Swiper } from 'swiper';
import { EffectCards, Manipulation } from 'swiper/modules';

import 'swiper/css';
import 'swiper/css/effect-cards';

function csrfToken() {
    return document.querySelector('meta[name="csrf-token"]').getAttribute('content');
}

async function enregistrerPronostic(matchId, scoreJ1, scoreJ2) {
    const response = await fetch(`/pronostics/${matchId}`, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            Accept: 'application/json',
            'X-CSRF-TOKEN': csrfToken(),
        },
        body: JSON.stringify({
            prono_score_j1: scoreJ1,
            prono_score_j2: scoreJ2,
        }),
    });

    const payload = await response.json().catch(() => null);

    if (! response.ok) {
        const message = payload?.errors
            ? Object.values(payload.errors).flat().join(' ')
            : (payload?.message ?? "Une erreur est survenue.");

        throw new Error(message);
    }

    return payload;
}

window.Alpine.data('countdown', (deadlineIso) => ({
    deadline: new Date(deadlineIso).getTime(),
    remaining: '',
    expired: false,
    timer: null,

    init() {
        this.tick();
        this.timer = setInterval(() => this.tick(), 1000);
    },

    destroy() {
        if (this.timer) {
            clearInterval(this.timer);
            this.timer = null;
        }
    },

    tick() {
        const diff = this.deadline - Date.now();

        if (diff <= 0) {
            this.expired = true;
            this.remaining = 'Pronostics clôturés';
            this.destroy();

            return;
        }

        const totalSeconds = Math.floor(diff / 1000);
        const days = Math.floor(totalSeconds / 86400);
        const hours = Math.floor((totalSeconds % 86400) / 3600);
        const minutes = Math.floor((totalSeconds % 3600) / 60);
        const seconds = totalSeconds % 60;
        const pad = (n) => String(n).padStart(2, '0');

        this.remaining = days > 0
            ? `${days}j ${pad(hours)}h ${pad(minutes)}m`
            : `${pad(hours)}:${pad(minutes)}:${pad(seconds)}`;
    },
}));

window.Alpine.data('pronosticCard', () => ({
    loading: false,
    error: null,
    scoreJ1: '',
    scoreJ2: '',

    async submit(matchId) {
        this.loading = true;
        this.error = null;

        try {
            await enregistrerPronostic(matchId, this.scoreJ1, this.scoreJ2);

            const swiper = window.pronosticSwiper;
            if (swiper) {
                swiper.removeSlide(swiper.activeIndex);

                if (swiper.slides.length === 0) {
                    window.location.reload();
                }
            }
        } catch (e) {
            this.error = e.message;
        } finally {
            this.loading = false;
        }
    },
}));

window.Alpine.data('pronosticRow', (initialScoreJ1, initialScoreJ2) => ({
    editing: false,
    loading: false,
    error: null,
    scoreJ1: initialScoreJ1,
    scoreJ2: initialScoreJ2,
    savedScoreJ1: initialScoreJ1,
    savedScoreJ2: initialScoreJ2,

    startEdit() {
        this.editing = true;
        this.error = null;
        this.scoreJ1 = this.savedScoreJ1;
        this.scoreJ2 = this.savedScoreJ2;
    },

    cancelEdit() {
        this.editing = false;
        this.error = null;
    },

    async submit(matchId) {
        this.loading = true;
        this.error = null;

        try {
            await enregistrerPronostic(matchId, this.scoreJ1, this.scoreJ2);
            this.savedScoreJ1 = this.scoreJ1;
            this.savedScoreJ2 = this.scoreJ2;
            this.editing = false;
        } catch (e) {
            this.error = e.message;
        } finally {
            this.loading = false;
        }
    },
}));

document.addEventListener('DOMContentLoaded', () => {
    const container = document.querySelector('.pronostic-swiper');

    if (! container) {
        return;
    }

    window.pronosticSwiper = new Swiper(container, {
        modules: [EffectCards, Manipulation],
        effect: 'cards',
        grabCursor: true,
        cardsEffect: {
            slideShadows: false,
        },
    });

    document.querySelectorAll('[data-swiper-prev]').forEach((button) => {
        button.addEventListener('click', () => window.pronosticSwiper.slidePrev());
    });

    document.querySelectorAll('[data-swiper-next]').forEach((button) => {
        button.addEventListener('click', () => window.pronosticSwiper.slideNext());
    });
});
