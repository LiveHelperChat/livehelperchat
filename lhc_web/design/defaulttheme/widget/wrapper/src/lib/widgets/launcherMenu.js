function httpsUrl(value) {
    if (typeof value === 'string') {
        try {
            const url = new URL(value);
            if (url.protocol === 'https:' && !url.username && !url.password) return url.href;
        } catch (e) {
            // Ignore a malformed link or icon supplied by a theme or extension.
        }
    }
    return null;
}

// This panel shares the status button's shadow root and stacking context, so
// hiding the widget behind a site dialog also hides its contact options.
export function createLauncherMenu(host, ui, openChat) {
    if (!ui || !Array.isArray(ui.launcher_links)) return null;
    const menu = document.createElement('section');
    menu.id = 'lhc-launcher-menu';
    menu.hidden = true;

    ui.launcher_links.forEach(link => {
        const url = link && httpsUrl(link.url);
        if (!url || typeof link.label !== 'string' || !link.label.trim()) return;
        const anchor = document.createElement('a');
        anchor.href = url;
        anchor.target = '_blank';
        anchor.rel = 'noopener noreferrer';
        anchor.textContent = link.label.trim();
        const icon = httpsUrl(link.icon);
        if (icon) {
            const img = document.createElement('img');
            img.alt = '';
            img.src = icon;
            anchor.prepend(img);
        }
        menu.appendChild(anchor);
    });
    if (!menu.childElementCount) return null;

    const heading = document.createElement('h2');
    heading.id = 'lhc-launcher-menu-heading';
    heading.textContent = typeof ui.launcher_menu_label === 'string' && ui.launcher_menu_label.trim()
        ? ui.launcher_menu_label.trim() : 'Contact us';
    menu.setAttribute('aria-labelledby', heading.id);
    const header = document.createElement('header');
    header.appendChild(heading);
    const infoUrl = httpsUrl(ui.launcher_info_url);
    if (infoUrl && ui.launcher_info_label) {
        const info = document.createElement('a');
        info.className = 'lhc-launcher-info';
        info.href = infoUrl;
        info.target = '_blank';
        info.rel = 'noopener noreferrer';
        info.title = ui.launcher_info_label;
        info.setAttribute('aria-label', ui.launcher_info_label);
        info.textContent = 'ⓘ';
        header.appendChild(info);
    }
    const chat = document.createElement('button');
    chat.type = 'button';
    chat.textContent = typeof ui.launcher_chat_label === 'string' && ui.launcher_chat_label.trim()
        ? ui.launcher_chat_label.trim() : 'Live chat';
    const chatIcon = document.createElement('span');
    chatIcon.className = 'lhc-icon-online';
    chatIcon.setAttribute('aria-hidden', 'true');
    chat.prepend(chatIcon);
    menu.prepend(header, chat);

    const style = document.createElement('style');
    style.textContent = `
        #lhc-launcher-menu {position:absolute;box-sizing:border-box;width:264px;max-width:calc(100vw - 16px);
            overflow:auto;overscroll-behavior:contain;pointer-events:auto;cursor:auto;padding:8px;
            background:#fff;color:#23262b;border:1px solid #d8dadd;border-radius:16px;
            box-shadow:0 8px 28px rgba(0,0,0,.18);font:normal 15px/1.4 system-ui,sans-serif;text-align:start}
        #lhc-launcher-menu[hidden] {display:none}
        #lhc-launcher-menu header {display:flex;align-items:center;justify-content:space-between;gap:4px}
        #lhc-launcher-menu h2 {margin:4px 8px 8px;font:600 16px/1.4 system-ui,sans-serif;overflow-wrap:anywhere}
        #lhc-launcher-menu .lhc-launcher-info {display:grid;place-items:center;width:44px;height:44px;flex:none;
            color:inherit;text-decoration:none;font:20px/1 system-ui,sans-serif;border-radius:8px}
        #lhc-launcher-menu > a, #lhc-launcher-menu > button {box-sizing:border-box;display:flex;align-items:center;
            gap:10px;width:100%;min-height:44px;padding:8px;border:0;border-radius:8px;background:transparent;
            color:inherit;text-decoration:none;text-align:start;font:inherit;overflow-wrap:anywhere;cursor:pointer}
        #lhc-launcher-menu img, #lhc-launcher-menu .lhc-icon-online {width:24px;height:24px;flex:none;object-fit:contain}
        #lhc-launcher-menu .lhc-icon-online:before {line-height:24px;font-size:23px}
        #lhc-launcher-menu a:hover, #lhc-launcher-menu button:hover {background:rgba(127,127,127,.15)}
        #lhc-launcher-menu :focus-visible {outline:2px solid currentColor;outline-offset:-2px}
        @media (prefers-color-scheme:dark) {
            #lhc-launcher-menu {background:#24262a;color:#f4f4f5;border-color:#4a4d52;color-scheme:dark}
        }
        @media (prefers-reduced-motion:no-preference) {
            #lhc-launcher-menu:not([hidden]) {animation:lhc-launcher-appear .15s ease-out}
            @keyframes lhc-launcher-appear {from {opacity:0;transform:translateY(3px)}}
        }`;
    host.shadowRoot.insertBefore(style, host.shadowRoot.querySelector('link'));
    host.shadowRoot.appendChild(menu);

    const trigger = host.shadowRoot.getElementById('status-icon');
    const previousRole = trigger.getAttribute('role');
    trigger.setAttribute('role', 'button');
    trigger.setAttribute('aria-controls', menu.id);
    trigger.setAttribute('aria-expanded', 'false');
    const onTriggerKeydown = event => {
        if (event.key === ' ') {
            event.preventDefault();
            trigger.click();
        }
    };
    trigger.addEventListener('keydown', onTriggerKeydown);

    function place() {
        const bounds = trigger.getBoundingClientRect();
        const hostBounds = host.getBoundingClientRect();
        const width = document.documentElement.clientWidth;
        const height = document.documentElement.clientHeight;
        const above = bounds.top > height - bounds.bottom;
        const available = above ? bounds.top : height - bounds.bottom;
        menu.style.maxHeight = Math.max(0, available - 16) + 'px';
        const aligned = bounds.left + bounds.width / 2 < width / 2 ? bounds.left : bounds.right - menu.offsetWidth;
        menu.style.left = Math.max(8, Math.min(aligned, width - menu.offsetWidth - 8)) - hostBounds.left + 'px';
        menu.style.top = (above ? bounds.top - menu.offsetHeight - 8 : bounds.bottom + 8) - hostBounds.top + 'px';
    }

    function close(restoreFocus) {
        if (menu.hidden) return;
        menu.hidden = true;
        trigger.setAttribute('aria-expanded', 'false');
        document.removeEventListener('click', outside, true);
        document.removeEventListener('focusin', outside, true);
        document.removeEventListener('keydown', escape);
        window.removeEventListener('resize', place);
        if (restoreFocus) trigger.focus();
    }
    function outside(event) {
        if (!event.composedPath().includes(host)) close(false);
    }
    function escape(event) {
        if (event.key === 'Escape') {
            event.preventDefault();
            close(true);
        }
    }
    function toggle() {
        if (!menu.hidden) return close(true);
        menu.hidden = false;
        place();
        trigger.setAttribute('aria-expanded', 'true');
        document.addEventListener('click', outside, true);
        document.addEventListener('focusin', outside, true);
        document.addEventListener('keydown', escape);
        window.addEventListener('resize', place);
        chat.focus();
    }

    // Keep the launcher's drag recognizer and group click handler on the
    // button itself. Links still follow their normal browser activation path.
    ['mousedown', 'touchstart'].forEach(type => menu.addEventListener(type, event => event.stopPropagation()));
    menu.addEventListener('click', event => {
        event.stopPropagation();
        if (!event.target.closest('a,button')) return;
        close(true);
        if (chat.contains(event.target)) openChat(event);
    });
    function destroy() {
        close(false);
        trigger.removeEventListener('keydown', onTriggerKeydown);
        style.remove();
        menu.remove();
        if (previousRole === null) trigger.removeAttribute('role');
        else trigger.setAttribute('role', previousRole);
        trigger.removeAttribute('aria-controls');
        trigger.removeAttribute('aria-expanded');
    }
    return {toggle, close, destroy};
}
