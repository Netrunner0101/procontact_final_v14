// Idle session timeout with confirmation modal.
//
// The server's EnforceIdleTimeout middleware is the security boundary; this
// module exists for UX so the user gets a chance to keep their session
// alive instead of suddenly hitting a "session expired" page mid-task.

const ACTIVITY_PING_DEBOUNCE_MS = 30 * 1000; // ping server at most every 30s
const ACTIVITY_EVENTS = ['mousemove', 'mousedown', 'keydown', 'scroll', 'touchstart'];

function getMeta(name) {
    const el = document.querySelector(`meta[name="${name}"]`);
    return el ? el.getAttribute('content') : null;
}

async function postKeepalive() {
    const csrf = getMeta('csrf-token');
    try {
        const res = await fetch('/session/keepalive', {
            method: 'POST',
            credentials: 'same-origin',
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'X-CSRF-TOKEN': csrf || '',
                'Accept': 'application/json',
            },
        });
        if (res.status === 401) {
            window.location.href = '/login';
            return false;
        }
        return res.ok;
    } catch (_) {
        return false;
    }
}

function logoutNow() {
    const csrf = getMeta('csrf-token');
    const form = document.createElement('form');
    form.method = 'POST';
    form.action = '/logout';
    const input = document.createElement('input');
    input.type = 'hidden';
    input.name = '_token';
    input.value = csrf || '';
    form.appendChild(input);
    document.body.appendChild(form);
    form.submit();
}

function init() {
    const cfg = window.IDLE_TIMEOUT_CONFIG;
    if (!cfg || !cfg.idleSeconds || cfg.idleSeconds <= 0) return;

    const modal = document.getElementById('idle-timeout-modal');
    const countdownEl = document.getElementById('idle-timeout-countdown');
    const stayBtn = document.getElementById('idle-timeout-stay');
    const logoutBtn = document.getElementById('idle-timeout-logout');
    if (!modal || !countdownEl || !stayBtn || !logoutBtn) return;

    let lastActivityAt = Date.now();
    let lastPingAt = 0;
    let warningTimer = null;
    let countdownTimer = null;

    function clearTimers() {
        if (warningTimer) { clearTimeout(warningTimer); warningTimer = null; }
        if (countdownTimer) { clearInterval(countdownTimer); countdownTimer = null; }
    }

    function showModal() {
        modal.classList.remove('hidden');
        modal.classList.add('flex');
        let remaining = cfg.graceSeconds;
        countdownEl.textContent = remaining;
        countdownTimer = setInterval(() => {
            remaining -= 1;
            countdownEl.textContent = remaining;
            if (remaining <= 0) {
                clearInterval(countdownTimer);
                countdownTimer = null;
                logoutNow();
            }
        }, 1000);
    }

    function hideModal() {
        modal.classList.add('hidden');
        modal.classList.remove('flex');
    }

    function scheduleWarning() {
        clearTimers();
        warningTimer = setTimeout(showModal, cfg.idleSeconds * 1000);
    }

    function onActivity() {
        // Ignore activity while the modal is up — the user must click Stay.
        if (!modal.classList.contains('hidden')) return;

        lastActivityAt = Date.now();
        scheduleWarning();

        const now = Date.now();
        if (now - lastPingAt > ACTIVITY_PING_DEBOUNCE_MS) {
            lastPingAt = now;
            postKeepalive();
        }
    }

    stayBtn.addEventListener('click', async () => {
        const ok = await postKeepalive();
        if (ok) {
            hideModal();
            lastActivityAt = Date.now();
            lastPingAt = Date.now();
            scheduleWarning();
        } else {
            window.location.href = '/login';
        }
    });

    logoutBtn.addEventListener('click', () => {
        clearTimers();
        logoutNow();
    });

    ACTIVITY_EVENTS.forEach((ev) => {
        document.addEventListener(ev, onActivity, { passive: true });
    });

    // If the page was background-tabbed, the timeout might have already fired
    // server-side — re-sync from /session/status when it becomes visible.
    document.addEventListener('visibilitychange', async () => {
        if (document.visibilityState !== 'visible') return;
        try {
            const res = await fetch('/session/status', {
                credentials: 'same-origin',
                headers: { 'X-Requested-With': 'XMLHttpRequest', 'Accept': 'application/json' },
            });
            if (res.status === 401) { window.location.href = '/login'; return; }
            if (!res.ok) return;
            const data = await res.json();
            const remaining = data.idle_seconds_limit - data.idle_seconds_elapsed;
            if (remaining <= 0 && modal.classList.contains('hidden')) {
                showModal();
            } else if (remaining > 0) {
                clearTimers();
                warningTimer = setTimeout(showModal, remaining * 1000);
            }
        } catch (_) { /* offline — keep local timer */ }
    });

    scheduleWarning();
}

if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', init);
} else {
    init();
}
