// SPWA Style Manager
var SPWA = (function() {
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

    return {
        addRawStyles: addRawStyles
    };
})();

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
                // Get parent element and child index
                const parentPath = path.slice(0, -1);
                const childIndex = path[path.length - 1];
                const parent = findNodeByPath(parentPath);
                if (parent && parent.childNodes[childIndex]) {
                    parent.childNodes[childIndex].textContent = patch.text;
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

function callback(error, data) {
    if (error) {
        console.error("Error:", error);
        return;
    }

    // Add new styles before applying patches (raw CSS format)
    if (data.styles) {
        SPWA.addRawStyles(data.styles);
    }

    // Execute JS commands from server
    if (data.js) {
        executeJsDump(data.js);
    }

    // Apply DOM patches
    if (data.patches && data.patches.length > 0) {
        console.log("Applying patches:", data.patches);
        applyPatches(data.patches);
    }

    console.log("Response:", data);
}

function post(data, headers) {
    var xhr = new XMLHttpRequest();
    xhr.open("POST", window.location.href, true);
    xhr.setRequestHeader("Content-Type", "application/json");

    // Set custom headers
    for (var key in headers ?? {}) {
        if (headers.hasOwnProperty(key)) {
            xhr.setRequestHeader(key, headers[key]);
        }
    }

    xhr.onreadystatechange = function () {
        if (xhr.readyState === 4) { // 4 means request is done
            if (xhr.status === 200) { // 200 means "OK"
                callback(null, JSON.parse(xhr.responseText));
            } else {
                callback(new Error("Request failed: " + xhr.status));
            }
        }
    };

    xhr.send(JSON.stringify(data));
}

function handleEvent(event, path) {
    console.log('Event:', event, 'Path:', path);
    post({ event, path });
}
