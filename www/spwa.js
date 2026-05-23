// SPWA Style Manager with compression support
var SPWA = (function() {
    // Arrays for decompression (index = array position)
    var P = ["display","flex-direction","justify-content","align-items","gap","padding","margin","width","height","background-color","color","font-size","font-weight","border-radius","box-shadow","flex-wrap","flex-grow","flex-shrink","position","top","right","bottom","left","text-align","text-decoration","line-height","overflow","opacity","z-index","cursor","grid-template-columns","column-gap","row-gap","max-width","min-width","max-height","min-height","padding-top","padding-right","padding-bottom","padding-left","margin-top","margin-right","margin-bottom","margin-left","object-fit","object-position","border-width","border-style","border-color","visibility","transition-property","transition-duration","transition-timing-function","transform","flex-basis","align-self","justify-self","overflow-x","overflow-y"];
    var V = ["flex","block","inline","inline-block","grid","none","row","column","row-reverse","column-reverse","flex-start","flex-end","center","space-between","space-around","space-evenly","stretch","baseline","wrap","nowrap","absolute","relative","fixed","sticky","auto","0","0px","100%","1","pointer","inherit","transparent","#ffffff","#000000","cover","contain","fill","underline","bold","normal","hidden","visible","scroll","solid","dashed","dotted","all","inline-flex","fit-content","min-content","max-content"];
    var B = ["(min-width: 640px)","(min-width: 768px)","(min-width: 1024px)","(min-width: 1280px)","(min-width: 1536px)"];
    var C = ["(prefers-color-scheme: dark)","(prefers-color-scheme: light)"];
    var X = [":hover",":active",":focus",":focus-visible",":focus-within",":visited",":disabled",":enabled",":checked",":required",":valid",":invalid","::placeholder",":first-child",":last-child",":only-child",":nth-child(odd)",":nth-child(even)",":empty"];

    var styleEl = null;
    var knownClasses = new Set();

    function getStyleElement() {
        if (!styleEl) {
            styleEl = document.getElementById('spwa-styles');
            if (!styleEl) {
                styleEl = document.createElement('style');
                styleEl.id = 'spwa-styles';
                document.head.appendChild(styleEl);
            }
        }
        return styleEl;
    }

    function initKnownClasses() {
        // Parse existing stylesheet to track known classes
        var el = getStyleElement();
        var css = el.textContent || '';
        var matches = css.match(/\.([^\s{:]+)/g);
        if (matches) {
            matches.forEach(function(m) {
                // Unescape and add to known set
                knownClasses.add(m.slice(1).replace(/\\([.:\[\]\/])/g, '$1'));
            });
        }
    }

    function escapeClass(cls) {
        return cls.replace(/([.:\[\]\/])/g, '\\$1');
    }

    // Decompress and add styles
    // Format: { className: [breakpoint, colorScheme, [pseudos], prop1, val1, prop2, val2, ...] }
    function addCompressedStyles(styles) {
        var css = '';
        for (var cls in styles) {
            if (knownClasses.has(cls)) continue;
            knownClasses.add(cls);

            var data = styles[cls];
            var breakpoint = data[0];
            var colorScheme = data[1];
            var pseudos = data[2];

            // Build selector
            var selector = '.' + escapeClass(cls);
            for (var i = 0; i < pseudos.length; i++) {
                selector += X[pseudos[i]];
            }

            // Build properties (starting at index 3)
            var rules = [];
            for (var j = 3; j < data.length; j += 2) {
                var prop = data[j];
                var val = data[j + 1];
                // Resolve property: number = index, string = literal
                var propStr = typeof prop === 'number' ? P[prop] : prop;
                // Resolve value: number = index, string = literal
                var valStr = typeof val === 'number' ? V[val] : val;
                rules.push(propStr + ':' + valStr);
            }

            var rule = selector + '{' + rules.join(';') + '}';

            // Wrap in media query if needed
            var mediaConditions = [];
            if (breakpoint !== null) mediaConditions.push(B[breakpoint]);
            if (colorScheme !== null) mediaConditions.push(C[colorScheme]);

            if (mediaConditions.length > 0) {
                rule = '@media ' + mediaConditions.join(' and ') + '{' + rule + '}';
            }

            css += rule;
        }

        if (css) {
            var el = getStyleElement();
            el.textContent += css;
        }
    }

    // Add raw CSS rules (className => CSS rule string)
    function addRawStyles(styles) {
        var css = '';
        for (var cls in styles) {
            if (!knownClasses.has(cls)) {
                css += styles[cls];
                knownClasses.add(cls);
            }
        }
        if (css) {
            var el = getStyleElement();
            el.textContent += css;
        }
    }

    // Initialize on load
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', initKnownClasses);
    } else {
        initKnownClasses();
    }

    // --- State management ---
    var stateHandlers = {};

    function addStateHandler(name, handler) {
        if (typeof handler.load !== 'function' ||
            typeof handler.save !== 'function' ||
            typeof handler.clear !== 'function') {
            console.error('SPWA: State handler must have load, save, and clear functions');
            return;
        }
        stateHandlers[name] = handler;
    }

    function getState(name, path) {
        var handler = stateHandlers[name];
        if (!handler) return {};
        var all = handler.load();
        return all[path] || {};
    }

    function saveState(name, path, state) {
        var handler = stateHandlers[name];
        if (!handler) return;
        var all = handler.load();
        all[path] = state;
        handler.save(all);
    }

    function getAll() {
        var result = {};
        var hasAny = false;
        for (var name in stateHandlers) {
            result[name] = stateHandlers[name].load();
            hasAny = true;
        }
        return hasAny ? result : null;
    }

    function setAll(state) {
        if (!state) return;
        for (var name in state) {
            if (stateHandlers[name]) {
                stateHandlers[name].save(state[name]);
            }
        }
    }

    function isStateEnabled() {
        for (var name in stateHandlers) {
            return true;
        }
        return false;
    }

    /**
     * Fire an empty request at the server, run the response through the
     * patcher. Used to pull in changes that aren't tied to a user event —
     * external data, server-side state that drifted, time-dependent UI
     * that's been marked invalidate()/invalidateAttr(). Same wire shape as
     * any other event, so hash echo, replay-on-reload, and bindings all
     * work transparently.
     */
    function refresh() {
        post({ event: '', path: '', value: null });
    }

    function resolveObject(path) {
        const last = path.pop();
        const resolved = path.reduce((acc, cur) => acc[cur], window);
        return [resolved, last];
    }

    function executeJsDump(dump) {
        for (const [mode, path, args] of dump) {
            const [obj, bind] = resolveObject(path);
            if (mode === 'invoke')
                obj[bind](...args);
            else if (mode === 'assign')
                obj[bind] = args;
        }
    }

    function findNodeByPath(path, includeTextNodes = false) {
        // Start from body's first child (the root element)
        let node = document.body.firstElementChild;
        for (let i = 0; i < path.length; i++) {
            if (!node) return null;
            const index = path[i];
            if (includeTextNodes) {
                // Use childNodes to include text nodes
                node = node.childNodes[index];
            } else {
                // Use children for elements only
                node = node.children[index];
            }
        }
        return node;
    }

    function applyPatches(patches) {
        for (const patch of patches) {
            const path = patch.path;

            switch (patch.type) {
                case 'replace_node': {
                    const node = findNodeByPath(path);
                    if (node) {
                        const temp = document.createElement('div');
                        temp.innerHTML = patch.html;
                        node.replaceWith(temp.firstElementChild);
                    }
                    break;
                }
                case 'insert_node': {
                    const parentPath = path.slice(0, -1);
                    const index = path[path.length - 1];
                    const parent = parentPath.length === 0
                        ? document.body.firstElementChild
                        : findNodeByPath(parentPath);
                    if (parent) {
                        const temp = document.createElement('div');
                        temp.innerHTML = patch.html;
                        const newNode = temp.firstElementChild;
                        if (index >= parent.children.length) {
                            parent.appendChild(newNode);
                        } else {
                            parent.insertBefore(newNode, parent.children[index]);
                        }
                    }
                    break;
                }
                case 'delete_node': {
                    const node = findNodeByPath(path);
                    if (node) {
                        node.remove();
                    }
                    break;
                }
                case 'set_attribute': {
                    const node = findNodeByPath(path);
                    if (node) {
                        node.setAttribute(patch.name, patch.value);
                        // For form controls, the `value` attribute only sets the default;
                        // the displayed value is the `.value` property once the user has typed.
                        if (patch.name === 'value' && 'value' in node) {
                            node.value = patch.value;
                            // Keep the localStorage draft in sync with the
                            // server-pushed value (empty string → drop entry).
                            if (node.hasAttribute && node.hasAttribute('data-bind')) {
                                captureDraft(node);
                            }
                        } else if (patch.name === 'checked' && 'checked' in node) {
                            node.checked = patch.value === 'checked' || patch.value === true;
                        }
                    }
                    break;
                }
                case 'remove_attribute': {
                    const node = findNodeByPath(path);
                    if (node) {
                        node.removeAttribute(patch.name);
                    }
                    break;
                }
                case 'replace_text': {
                    const node = findNodeByPath(path);
                    if (node) {
                        node.textContent = patch.text;
                    }
                    break;
                }
                case 'insert_at': {
                    // Insert child at specific index in a list
                    const parent = findNodeByPath(path);
                    if (parent) {
                        const temp = document.createElement('div');
                        temp.innerHTML = patch.html;
                        const newNode = temp.firstElementChild;
                        const index = patch.index;
                        if (index >= parent.children.length) {
                            parent.appendChild(newNode);
                        } else {
                            parent.insertBefore(newNode, parent.children[index]);
                        }
                    }
                    break;
                }
                case 'remove_at': {
                    // Remove child at specific index in a list
                    const parent = findNodeByPath(path);
                    if (parent && parent.children[patch.index]) {
                        parent.children[patch.index].remove();
                    }
                    break;
                }
                case 'update_at': {
                    // Replace child at specific index in a list
                    const parent = findNodeByPath(path);
                    if (parent && parent.children[patch.index]) {
                        const temp = document.createElement('div');
                        temp.innerHTML = patch.html;
                        parent.children[patch.index].replaceWith(temp.firstElementChild);
                    }
                    break;
                }
            }
        }
    }

    // --- Replay-after-reload constants (used by callback, maybeReplay, post) ---
    var SPWA_REPLAY_KEY = '__spwa_replay';
    var SPWA_REPLAY_COUNT_KEY = '__spwa_replay_count';
    var SPWA_MAX_REPLAYS = 2;
    var __spwa_lastPayload = null;

    // The path the current DOM was last rendered for. Sent on every POST as
    // `previousPath` so the server can render OLD against the URL the user
    // actually has on screen, not whatever location.href happens to be at the
    // moment of the request (which differs after popstate). Updated after
    // every successful response so it tracks pushState navigations too.
    var __spwa_renderedPath = location.pathname + location.search;

    // --- Value bindings ---
    function initBindings() {
        document.querySelectorAll('[data-bind]').forEach(function(el) {
            if (el._spwaBound) return;
            el._spwaBound = true;

            // Persist every keystroke to localStorage. If the tab is reloaded
            // before the user submits, restoreDrafts() will repopulate it.
            el.addEventListener('input', function () { captureDraft(el); });

            // Browsers fire `change` on Enter only when the value differs
            // from the value-at-focus. If the field was prefilled by us
            // (autofocus, draft restore, server set_attribute), pressing
            // Enter without typing would silently do nothing. Track the
            // baseline and synthesise a change event ourselves in that case.
            el.addEventListener('focus', function () {
                el._spwaFocusValue = el.value;
            });
            el.addEventListener('keydown', function (e) {
                if (e.key !== 'Enter') return;
                var baseline = el._spwaFocusValue !== undefined
                    ? el._spwaFocusValue
                    : el.defaultValue;
                if (el.value === baseline) {
                    el.dispatchEvent(new Event('change'));
                }
            });

            // If the element is already focused at attach time (autofocus
            // fired before bootstrap), seed the baseline now.
            if (document.activeElement === el) {
                el._spwaFocusValue = el.value;
            }
        });
    }

    function collectBindings() {
        var bindings = {};
        document.querySelectorAll('[data-bind]').forEach(function(el) {
            bindings[computePath(el)] = el.value;
        });
        return Object.keys(bindings).length > 0 ? bindings : null;
    }

    // --- Draft persistence for bound inputs (localStorage) ---
    // Save the value of every bound input on the `input` event, keyed by its
    // computed path. On page load, walk the bound inputs again and apply any
    // saved draft. Drafts also follow server-driven value changes so a
    // re-render that pushes value="" wipes the draft instead of resurrecting
    // it on the next reload.
    var SPWA_DRAFTS_KEY = '__spwa_drafts';

    function loadDrafts() {
        try {
            var raw = localStorage.getItem(SPWA_DRAFTS_KEY);
            return raw ? JSON.parse(raw) : {};
        } catch (e) {
            return {};
        }
    }

    function saveDrafts(drafts) {
        try {
            if (Object.keys(drafts).length === 0) {
                localStorage.removeItem(SPWA_DRAFTS_KEY);
            } else {
                localStorage.setItem(SPWA_DRAFTS_KEY, JSON.stringify(drafts));
            }
        } catch (e) { /* quota / disabled — give up silently */ }
    }

    function captureDraft(el) {
        var path = computePath(el);
        if (!path) return;
        var drafts = loadDrafts();
        if (el.value === '' || el.value == null) {
            delete drafts[path];
        } else {
            drafts[path] = el.value;
        }
        saveDrafts(drafts);
    }

    function restoreDrafts() {
        var drafts = loadDrafts();
        if (Object.keys(drafts).length === 0) return;
        document.querySelectorAll('[data-bind]').forEach(function(el) {
            var path = computePath(el);
            if (Object.prototype.hasOwnProperty.call(drafts, path)) {
                el.value = drafts[path];
                // Mirror the focus-baseline so a first Enter is recognized as
                // a no-op-since-focus and the keydown handler synthesises a
                // change event.
                el._spwaFocusValue = el.value;
            }
        });
    }

    function maybeReplay() {
        var stored = sessionStorage.getItem(SPWA_REPLAY_KEY);
        if (!stored) return;
        // Clear the payload immediately so a failure here doesn't loop;
        // SPWA_REPLAY_COUNT_KEY stays until a successful response clears it.
        sessionStorage.removeItem(SPWA_REPLAY_KEY);
        var data;
        try {
            data = JSON.parse(stored);
        } catch (e) {
            return;
        }
        // Use the fresh hash from this render; the stashed one is stale.
        if (typeof window.__SPWA_HASH === 'string') {
            data.hash = window.__SPWA_HASH;
        }
        // Bypass post() so we keep the original payload's bindings/value
        // (the new page has empty inputs that would otherwise overwrite them).
        setBusy(true);
        __spwa_lastPayload = data;
        var xhr = new XMLHttpRequest();
        xhr.open("POST", window.location.href, true);
        xhr.setRequestHeader("Content-Type", "application/json");
        xhr.onreadystatechange = function () {
            if (xhr.readyState === 4) {
                if (xhr.status === 200) callback(null, JSON.parse(xhr.responseText));
                else callback(new Error("Request failed: " + xhr.status));
            }
        };
        xhr.send(JSON.stringify(data));
    }

    function bootstrap() {
        initBindings();
        // Restore any drafts BEFORE a possible replay, so the replayed event
        // sees the restored input value via collectBindings().
        restoreDrafts();
        maybeReplay();
    }

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', bootstrap);
    } else {
        bootstrap();
    }

    function callback(error, data) {
        if (error) {
            console.error("Error:", error);
            // Don't leave the queue stuck behind a failed request.
            drainQueue();
            return;
        }

        // Server signaled it cleared its state (shape mismatch / hash mismatch);
        // reload the page so we start from a fresh server render — then replay
        // the original request once the new page is up. Capped at SPWA_MAX_REPLAYS
        // to avoid an infinite loop if state keeps drifting.
        if (data && data.reload) {
            if (__spwa_lastPayload) {
                var count = parseInt(sessionStorage.getItem(SPWA_REPLAY_COUNT_KEY) || '0', 10);
                if (count < SPWA_MAX_REPLAYS) {
                    try {
                        sessionStorage.setItem(SPWA_REPLAY_KEY, JSON.stringify(__spwa_lastPayload));
                        sessionStorage.setItem(SPWA_REPLAY_COUNT_KEY, String(count + 1));
                    } catch (e) { /* quota / disabled storage — give up on replay */ }
                } else {
                    sessionStorage.removeItem(SPWA_REPLAY_KEY);
                    sessionStorage.removeItem(SPWA_REPLAY_COUNT_KEY);
                }
            }
            // A full reload supersedes anything queued — drop it.
            __spwa_queue.length = 0;
            window.location.reload();
            return;
        }

        // A normal successful response — past any reload spiral. Clear counters.
        sessionStorage.removeItem(SPWA_REPLAY_KEY);
        sessionStorage.removeItem(SPWA_REPLAY_COUNT_KEY);

        // Refresh the state-fingerprint we echo back to the server on the next
        // request. This is set on initial page render and updated after each
        // successful event.
        if (data && typeof data.hash === 'string') {
            window.__SPWA_HASH = data.hash;
        }

        // Save state if client state management is enabled
        if (data.state) {
            SPWA.setAll(data.state);
        }

        // Add new styles before applying patches
        // Supports both raw (className => CSS string) and compressed formats
        if (data.styles) {
            SPWA.addRawStyles(data.styles);
        }
        if (data.compressedStyles) {
            SPWA.addCompressedStyles(data.compressedStyles);
        }

        // Execute JS commands from server
        if (data.js) {
            executeJsDump(data.js);
        }

        // Apply DOM patches
        if (data.patches && data.patches.length > 0) {
            // console.log("Applying patches:", data.patches);
            applyPatches(data.patches);
        }

        // Re-initialize bindings after patches (new elements may have data-bind)
        initBindings();

        // Snapshot the URL the DOM is now rendered for. Includes any
        // pushState/replaceState issued by the server in data.js above. Sent
        // back to the server on the next POST as `previousPath` so OLD/NEW
        // diff stays correct across popstate navigations.
        __spwa_renderedPath = location.pathname + location.search;

        // DOM is now hydrated for this request — release the queue.
        drainQueue();
    }

    // --- Request queue + loader visibility ---
    // Only one request may be in flight at a time. Anything that comes in while
    // busy is queued and dispatched after the current response has been applied
    // (patches + bindings re-init). A `[data-spwa-loader]` element on the page,
    // if present, is toggled to visible while a request is active.
    var __spwa_busy = false;
    var __spwa_queue = [];
    var __spwa_loaderEl = null;
    var __spwa_loaderTimer = null;

    function loaderEl() {
        if (__spwa_loaderEl === null) {
            __spwa_loaderEl = document.querySelector('[data-spwa-loader]') || false;
        }
        return __spwa_loaderEl || null;
    }

    function loaderDelay() {
        // Override via `window.__SPWA_LOADER_DELAY = N` (milliseconds).
        // Fast requests (< delay) complete without ever showing the loader,
        // avoiding flashes. Default: 300ms — see UX notes in README.
        var d = typeof window.__SPWA_LOADER_DELAY === 'number'
            ? window.__SPWA_LOADER_DELAY
            : 300;
        return d >= 0 ? d : 0;
    }

    function setBusy(busy) {
        __spwa_busy = busy;
        var el = loaderEl();
        if (!el) return;

        if (busy) {
            // Only schedule a show on the leading edge — once a debounce is
            // pending we don't reset it for follow-up requests in the queue,
            // so a sequence of small requests still triggers the loader if
            // their total time exceeds the delay.
            if (__spwa_loaderTimer === null && el.style.display === 'none') {
                __spwa_loaderTimer = setTimeout(function () {
                    __spwa_loaderTimer = null;
                    if (__spwa_busy) el.style.display = 'block';
                }, loaderDelay());
            }
        } else {
            if (__spwa_loaderTimer !== null) {
                clearTimeout(__spwa_loaderTimer);
                __spwa_loaderTimer = null;
            }
            el.style.display = 'none';
        }
    }

    function post(data, headers) {
        if (__spwa_busy) {
            __spwa_queue.push({ kind: 'json', data: data, headers: headers });
            return;
        }
        setBusy(true);
        sendJson(data, headers);
    }

    function postMultipart(data, files) {
        if (__spwa_busy) {
            __spwa_queue.push({ kind: 'multipart', data: data, files: files });
            return;
        }
        setBusy(true);
        sendMultipart(data, files);
    }

    function drainQueue() {
        if (__spwa_queue.length === 0) {
            setBusy(false);
            return;
        }
        var next = __spwa_queue.shift();
        if (next.kind === 'multipart') {
            sendMultipart(next.data, next.files);
        } else {
            sendJson(next.data, next.headers);
        }
    }

    function sendJson(data, headers) {
        var xhr = new XMLHttpRequest();
        xhr.open("POST", window.location.href, true);
        xhr.setRequestHeader("Content-Type", "application/json");

        for (var key in headers ?? {}) {
            if (headers.hasOwnProperty(key)) {
                xhr.setRequestHeader(key, headers[key]);
            }
        }

        __spwa_lastPayload = data;

        var bindings = collectBindings();
        if (bindings) {
            data.bindings = bindings;
        }
        if (typeof window.__SPWA_HASH === 'string') {
            data.hash = window.__SPWA_HASH;
        }
        data.previousPath = __spwa_renderedPath;
        if (SPWA.isStateEnabled()) {
            var clientState = SPWA.getAll();
            if (clientState) data.state = clientState;
        }

        xhr.onreadystatechange = function () {
            if (xhr.readyState !== 4) return;
            if (xhr.status === 200) {
                callback(null, JSON.parse(xhr.responseText));
            } else {
                callback(new Error("Request failed: " + xhr.status));
            }
        };
        xhr.send(JSON.stringify(data));
    }

    function sendMultipart(data, files) {
        var bindings = collectBindings();
        if (bindings) data.bindings = bindings;
        if (typeof window.__SPWA_HASH === 'string') {
            data.hash = window.__SPWA_HASH;
        }
        data.previousPath = __spwa_renderedPath;
        if (SPWA.isStateEnabled()) {
            var clientState = SPWA.getAll();
            if (clientState) data.state = clientState;
        }

        var formData = new FormData();
        formData.append('_spwa', JSON.stringify(data));
        for (var i = 0; i < files.length; i++) {
            formData.append('files[]', files[i]);
        }

        var xhr = new XMLHttpRequest();
        xhr.open("POST", window.location.href, true);
        xhr.onreadystatechange = function () {
            if (xhr.readyState !== 4) return;
            if (xhr.status === 200) {
                callback(null, JSON.parse(xhr.responseText));
            } else {
                callback(new Error("Request failed: " + xhr.status));
            }
        };
        xhr.send(formData);
    }

    function extractEventData(evt, el) {
        if (!evt) {
            // Fallback: just return element value
            return (el && el.value !== undefined) ? el.value : null;
        }

        var type = evt.type;

        // Form/input events — return element value or checked state
        if (type === 'change' || type === 'input' || type === 'select' || type === 'invalid') {
            if (el && (el.type === 'checkbox' || el.type === 'radio')) {
                return { value: el.value, checked: el.checked };
            }
            return (el && el.value !== undefined) ? el.value : null;
        }

        // Focus/blur — return relatedTarget info
        if (type === 'focus' || type === 'blur' || type === 'focusin' || type === 'focusout') {
            return null;
        }

        // Submit/reset — prevent default, return null
        if (type === 'submit' || type === 'reset') {
            evt.preventDefault();
            return null;
        }

        // Keyboard events
        if (type === 'keydown' || type === 'keyup' || type === 'keypress') {
            return {
                key: evt.key,
                code: evt.code,
                altKey: evt.altKey,
                ctrlKey: evt.ctrlKey,
                shiftKey: evt.shiftKey,
                metaKey: evt.metaKey,
                repeat: evt.repeat
            };
        }

        // Mouse events
        if (type === 'click' || type === 'dblclick' || type === 'mousedown' || type === 'mouseup' ||
            type === 'mouseover' || type === 'mouseout' || type === 'mouseenter' || type === 'mouseleave' ||
            type === 'mousemove' || type === 'contextmenu') {
            var mouseData = {
                clientX: evt.clientX,
                clientY: evt.clientY,
                offsetX: evt.offsetX,
                offsetY: evt.offsetY,
                button: evt.button,
                altKey: evt.altKey,
                ctrlKey: evt.ctrlKey,
                shiftKey: evt.shiftKey,
                metaKey: evt.metaKey
            };
            if (type === 'contextmenu') {
                evt.preventDefault();
            }
            // For click on form elements, also include value
            if (el && el.value !== undefined) {
                mouseData.value = el.value;
            }
            return mouseData;
        }

        // Pointer events
        if (type.indexOf('pointer') === 0 || type === 'gotpointercapture' || type === 'lostpointercapture') {
            return {
                clientX: evt.clientX,
                clientY: evt.clientY,
                offsetX: evt.offsetX,
                offsetY: evt.offsetY,
                button: evt.button,
                pointerId: evt.pointerId,
                pointerType: evt.pointerType,
                pressure: evt.pressure,
                width: evt.width,
                height: evt.height,
                altKey: evt.altKey,
                ctrlKey: evt.ctrlKey,
                shiftKey: evt.shiftKey,
                metaKey: evt.metaKey
            };
        }

        // Touch events
        if (type === 'touchstart' || type === 'touchend' || type === 'touchmove' || type === 'touchcancel') {
            var touches = [];
            var src = evt.touches || [];
            for (var i = 0; i < src.length; i++) {
                touches.push({ clientX: src[i].clientX, clientY: src[i].clientY, identifier: src[i].identifier });
            }
            var changedTouches = [];
            var csrc = evt.changedTouches || [];
            for (var j = 0; j < csrc.length; j++) {
                changedTouches.push({ clientX: csrc[j].clientX, clientY: csrc[j].clientY, identifier: csrc[j].identifier });
            }
            return { touches: touches, changedTouches: changedTouches };
        }

        // Wheel events
        if (type === 'wheel') {
            return {
                deltaX: evt.deltaX,
                deltaY: evt.deltaY,
                deltaZ: evt.deltaZ,
                deltaMode: evt.deltaMode,
                clientX: evt.clientX,
                clientY: evt.clientY
            };
        }

        // Scroll events
        if (type === 'scroll') {
            return {
                scrollTop: el ? el.scrollTop : 0,
                scrollLeft: el ? el.scrollLeft : 0,
                scrollHeight: el ? el.scrollHeight : 0,
                scrollWidth: el ? el.scrollWidth : 0
            };
        }

        // Drag events
        if (type === 'dragstart' || type === 'drag' || type === 'dragend' ||
            type === 'dragenter' || type === 'dragleave' || type === 'dragover' || type === 'drop') {
            if (type === 'dragover' || type === 'drop') {
                evt.preventDefault();
            }
            var dragData = {
                clientX: evt.clientX,
                clientY: evt.clientY,
                offsetX: evt.offsetX,
                offsetY: evt.offsetY
            };
            if (evt.dataTransfer) {
                try {
                    dragData.text = evt.dataTransfer.getData('text/plain');
                } catch(e) {}
                dragData.types = Array.from(evt.dataTransfer.types || []);
                dragData.dropEffect = evt.dataTransfer.dropEffect;
                dragData.effectAllowed = evt.dataTransfer.effectAllowed;
            }
            return dragData;
        }

        // Clipboard events
        if (type === 'copy' || type === 'cut' || type === 'paste') {
            var clipData = {};
            if (evt.clipboardData) {
                try {
                    clipData.text = evt.clipboardData.getData('text/plain');
                } catch(e) {}
            }
            return clipData;
        }

        // Transition/animation events
        if (type === 'transitionend' || type === 'transitionstart' || type === 'transitioncancel' ||
            type === 'transitionrun') {
            return { propertyName: evt.propertyName, elapsedTime: evt.elapsedTime, pseudoElement: evt.pseudoElement };
        }
        if (type === 'animationend' || type === 'animationstart' || type === 'animationiteration' ||
            type === 'animationcancel') {
            return { animationName: evt.animationName, elapsedTime: evt.elapsedTime, pseudoElement: evt.pseudoElement };
        }

        // Resize events
        if (type === 'resize') {
            return { width: el ? el.offsetWidth : 0, height: el ? el.offsetHeight : 0 };
        }

        // Media events
        if (type === 'play' || type === 'pause' || type === 'ended' || type === 'timeupdate' ||
            type === 'volumechange' || type === 'seeking' || type === 'seeked' ||
            type === 'loadeddata' || type === 'loadedmetadata' || type === 'canplay' || type === 'canplaythrough' ||
            type === 'waiting' || type === 'playing' || type === 'ratechange' || type === 'durationchange' ||
            type === 'progress' || type === 'stalled' || type === 'suspend' || type === 'emptied' || type === 'abort') {
            return {
                currentTime: el ? el.currentTime : 0,
                duration: el ? el.duration : 0,
                paused: el ? el.paused : true,
                volume: el ? el.volume : 1,
                muted: el ? el.muted : false,
                playbackRate: el ? el.playbackRate : 1,
                ended: el ? el.ended : false,
                readyState: el ? el.readyState : 0
            };
        }

        // Default: return element value if available
        return (el && el.value !== undefined) ? el.value : null;
    }

    function computePath(el) {
        var root = document.body.firstElementChild;
        var indices = [];
        while (el && el !== root) {
            var parent = el.parentElement;
            if (!parent) return '';
            indices.unshift(Array.prototype.indexOf.call(parent.children, el));
            el = parent;
        }
        return indices.join(',');
    }

    function handleEvent(evt, event, el) {
        // Anchor with an SPWA click handler = SPA-handled link. Stop the
        // browser from following the href so the server can swap content
        // in-place. Modifier keys / middle-click fall through to the
        // browser so "open in new tab" still works.
        if (event === 'click' && el && el.tagName === 'A') {
            if (evt.ctrlKey || evt.metaKey || evt.shiftKey || evt.button !== 0) {
                return;
            }
            evt.preventDefault();
        }
        var path = computePath(el);
        var data = { event: event, path: path };
        // File upload — send as multipart form
        if (el && el.type === 'file' && el.files && el.files.length > 0) {
            data.value = null;
            postMultipart(data, el.files);
            return;
        }
        data.value = extractEventData(evt, el);
        post(data);
    }
    return {
        addRawStyles: addRawStyles,
        addCompressedStyles: addCompressedStyles,
        addStateHandler: addStateHandler,
        getState: getState,
        saveState: saveState,
        getAll: getAll,
        setAll: setAll,
        isStateEnabled: isStateEnabled,
        refresh: refresh,
        tick: refresh,
        handleEvent: handleEvent
    };
})();

// Dev-only HMR: long-poll /hmr.php; on a detected .php change, reload.
(function () {
    var ctl;
    function poll() {
        if (ctl) ctl.abort();
        ctl = new AbortController();
        fetch('/hmr.php', { signal: ctl.signal, cache: 'no-store' })
            .then(function (r) { return r.json(); })
            .then(function (j) { if (j && j.changed) location.reload(); })
            .catch(function () { /* aborted or network error */ });
    }
    window.addEventListener('beforeunload', function () { if (ctl) ctl.abort(); });
    poll();
    setInterval(poll, 60_000);
})();
