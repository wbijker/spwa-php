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

    return {
        addRawStyles: addRawStyles,
        addCompressedStyles: addCompressedStyles
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
