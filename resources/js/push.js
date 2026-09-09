// Gestion de l'abonnement aux notifications push (Web Push / VAPID).
function metaContent(name) {
    return document.querySelector(`meta[name="${name}"]`)?.getAttribute('content') ?? '';
}

function urlBase64ToUint8Array(base64String) {
    const padding = '='.repeat((4 - (base64String.length % 4)) % 4);
    const base64 = (base64String + padding).replace(/-/g, '+').replace(/_/g, '/');
    const raw = window.atob(base64);
    const output = new Uint8Array(raw.length);

    for (let i = 0; i < raw.length; i++) {
        output[i] = raw.charCodeAt(i);
    }

    return output;
}

window.Alpine.data('pushToggle', () => ({
    supported: false,
    permission: 'default',
    subscribed: false,
    busy: false,
    error: null,

    async init() {
        this.supported = 'serviceWorker' in navigator
            && 'PushManager' in window
            && !!metaContent('vapid-public-key');

        if (! this.supported) {
            return;
        }

        this.permission = Notification.permission;

        try {
            const registration = await navigator.serviceWorker.ready;
            const subscription = await registration.pushManager.getSubscription();
            this.subscribed = !! subscription;
        } catch (e) {
            this.supported = false;
        }
    },

    async toggle() {
        if (this.busy) {
            return;
        }

        this.busy = true;
        this.error = null;

        try {
            if (this.subscribed) {
                await this.unsubscribe();
            } else {
                await this.subscribe();
            }
        } catch (e) {
            this.error = "Impossible de modifier les notifications. Réessaie.";
        } finally {
            this.busy = false;
        }
    },

    async subscribe() {
        this.permission = await Notification.requestPermission();

        if (this.permission !== 'granted') {
            return;
        }

        const registration = await navigator.serviceWorker.ready;
        const subscription = await registration.pushManager.subscribe({
            userVisibleOnly: true,
            applicationServerKey: urlBase64ToUint8Array(metaContent('vapid-public-key')),
        });

        await this.send('POST', subscription.toJSON());
        this.subscribed = true;
    },

    async unsubscribe() {
        const registration = await navigator.serviceWorker.ready;
        const subscription = await registration.pushManager.getSubscription();

        if (subscription) {
            await this.send('DELETE', { endpoint: subscription.endpoint });
            await subscription.unsubscribe();
        }

        this.subscribed = false;
    },

    async send(method, body) {
        const response = await fetch('/push/subscriptions', {
            method,
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': metaContent('csrf-token'),
            },
            body: JSON.stringify(body),
        });

        if (! response.ok) {
            throw new Error('Requête refusée.');
        }
    },
}));
