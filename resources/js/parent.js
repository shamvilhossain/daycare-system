// Parent Portal script (mobile-first). Separate from AdminLTE.
import * as bootstrap from 'bootstrap';

window.bootstrap = bootstrap;

// Photo lightbox: any element with [data-pp-media] opens the shared modal.
document.addEventListener('click', (event) => {
    const trigger = event.target.closest('[data-pp-media]');
    if (!trigger) return;

    event.preventDefault();
    const modalEl = document.getElementById('ppLightbox');
    if (!modalEl) return;

    const body = modalEl.querySelector('.pp-lightbox-body');
    const caption = modalEl.querySelector('.pp-lightbox-caption');
    const src = trigger.getAttribute('href');

    body.innerHTML = '';
    let mediaEl;
    if (trigger.dataset.ppMedia === 'video') {
        mediaEl = document.createElement('video');
        mediaEl.controls = true;
        mediaEl.autoplay = true;
        mediaEl.playsInline = true;
    } else {
        mediaEl = document.createElement('img');
        mediaEl.alt = trigger.dataset.caption || 'Photo';
    }
    mediaEl.src = src;
    body.appendChild(mediaEl);
    caption.textContent = trigger.dataset.caption || '';

    bootstrap.Modal.getOrCreateInstance(modalEl).show();
});

// ---------- PWA: install prompt ----------
const isStandalone = () =>
    window.matchMedia('(display-mode: standalone)').matches || window.navigator.standalone === true;
let deferredInstallPrompt = null;

function refreshInstallUi() {
    const card = document.querySelector('[data-pp-install-card]');
    if (!card) return;

    const isIos = /iphone|ipad|ipod/i.test(navigator.userAgent) && !window.MSStream;
    const canPrompt = !!deferredInstallPrompt;
    const show = !isStandalone() && (canPrompt || isIos);

    card.classList.toggle('d-none', !show);
    card.querySelector('[data-pp-install]')?.classList.toggle('d-none', !canPrompt);
    card.querySelector('[data-pp-ios-install]')?.classList.toggle('d-none', canPrompt || !isIos);
}

window.addEventListener('beforeinstallprompt', (event) => {
    event.preventDefault();
    deferredInstallPrompt = event;
    refreshInstallUi();
});

window.addEventListener('appinstalled', () => {
    deferredInstallPrompt = null;
    refreshInstallUi();
});

document.addEventListener('click', async (event) => {
    if (!event.target.closest('[data-pp-install]') || !deferredInstallPrompt) return;
    deferredInstallPrompt.prompt();
    await deferredInstallPrompt.userChoice;
    deferredInstallPrompt = null;
    refreshInstallUi();
});

document.addEventListener('DOMContentLoaded', refreshInstallUi);

// ---------- PWA: clear cached assets on logout ----------
document.addEventListener('submit', (event) => {
    if (event.target.matches('[data-pp-logout]')) {
        navigator.serviceWorker?.controller?.postMessage('kc:clear-caches');
    }
});

document.addEventListener('hidden.bs.modal', (event) => {
    if (event.target.id === 'ppLightbox') {
        event.target.querySelector('.pp-lightbox-body').innerHTML = '';
    }
});
