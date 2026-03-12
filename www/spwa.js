// Style dictionary and dynamic CSS generation
var SPWA = (function() {
    var P = {0:"display",1:"flex-direction",2:"justify-content",3:"align-items",4:"gap",5:"padding",6:"margin",7:"width",8:"height",9:"background-color",10:"color",11:"font-size",12:"font-weight",13:"border-radius",14:"box-shadow",15:"flex-wrap",16:"flex-grow",17:"flex-shrink",18:"position",19:"top",20:"right",21:"bottom",22:"left",23:"text-align",24:"text-decoration",25:"line-height",26:"overflow",27:"opacity",28:"z-index",29:"cursor",30:"grid-template-columns",31:"column-gap",32:"row-gap",33:"max-width",34:"min-width",35:"max-height",36:"min-height",37:"padding-top",38:"padding-right",39:"padding-bottom",40:"padding-left",41:"margin-top",42:"margin-right",43:"margin-bottom",44:"margin-left",45:"object-fit",46:"object-position"};
    var V = {0:"flex",1:"block",2:"inline",3:"inline-block",4:"grid",5:"none",6:"row",7:"column",8:"row-reverse",9:"column-reverse",10:"flex-start",11:"flex-end",12:"center",13:"space-between",14:"space-around",15:"space-evenly",16:"stretch",17:"baseline",18:"wrap",19:"nowrap",20:"absolute",21:"relative",22:"fixed",23:"sticky",24:"auto",25:"0",26:"0px",27:"100%",28:"1",29:"pointer",30:"inherit",31:"transparent",32:"#ffffff",33:"#000000",34:"cover",35:"contain",36:"fill",37:"underline",38:"bold",39:"normal"};
    var B = {"sm":"(min-width: 640px)","md":"(min-width: 768px)","lg":"(min-width: 1024px)","xl":"(min-width: 1280px)","2xl":"(min-width: 1536px)"};
    var C = {"dark":"(prefers-color-scheme: dark)","light":"(prefers-color-scheme: light)"};
    var X = {"hover":":hover","active":":active","focus":":focus","focus-visible":":focus-visible","focus-within":":focus-within","visited":":visited","disabled":":disabled","enabled":":enabled","checked":":checked","required":":required","valid":":valid","invalid":":invalid","placeholder":"::placeholder","first":":first-child","last":":last-child","only":":only-child","odd":":nth-child(odd)","even":":nth-child(even)","empty":":empty"};

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

    function parseClass(cls) {
        var parts = cls.split(':');
        var base = parts.pop();
        var media = [];
        var pseudo = '';
        for (var i = 0; i < parts.length; i++) {
            var p = parts[i];
            if (B[p]) media.push(B[p]);
            else if (C[p]) media.push(C[p]);
            else if (X[p]) pseudo += X[p];
        }
        return {
            media: media.length ? '@media ' + media.join(' and ') : null,
            pseudo: pseudo,
            base: base
        };
    }

    function escapeClass(cls) {
        return cls.replace(/([.:\[\]\/])/g, '\\$1');
    }

    function decodeStyles(styles) {
        var base = [];
        var mediaMap = {};
        for (var cls in styles) {
            var parsed = parseClass(cls);
            var selector = '.' + escapeClass(cls) + parsed.pseudo;
            var props = styles[cls];
            var rules = [];
            for (var p in props) {
                var prop = typeof p === 'number' || /^\d+$/.test(p) ? P[p] : p;
                var val = props[p];
                var isIndex = typeof val === 'number' || /^\d+$/.test(val);
                var value = isIndex && V[val] !== undefined ? V[val] : val;
                rules.push(prop + ':' + value);
            }
            var rule = selector + '{' + rules.join(';') + '}';
            if (parsed.media) {
                if (!mediaMap[parsed.media]) mediaMap[parsed.media] = [];
                mediaMap[parsed.media].push(rule);
            } else {
                base.push(rule);
            }
        }
        var css = base.join('');
        for (var mq in mediaMap) {
            css += mq + '{' + mediaMap[mq].join('') + '}';
        }
        return css;
    }

    function addStyles(styles) {
        var newStyles = {};
        var hasNew = false;
        for (var cls in styles) {
            if (!knownClasses.has(cls)) {
                newStyles[cls] = styles[cls];
                knownClasses.add(cls);
                hasNew = true;
            }
        }
        if (hasNew) {
            var el = getStyleElement();
            el.textContent += decodeStyles(newStyles);
        }
    }

    // Initialize on load
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', initKnownClasses);
    } else {
        initKnownClasses();
    }

    return {
        addStyles: addStyles,
        decodeStyles: decodeStyles
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
    if (data.styles) {
        SPWA.addStyles(data.styles);
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
