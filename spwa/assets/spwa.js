var prevLocation = window.location.pathname + window.location.search;

window.addEventListener('popstate', function(event) {
    // we have to skip the previous URL
    post(null,{Url: prevLocation});
    prevLocation = window.location.href;
});

function resolveNode(path) {
    return path.reduce((acc, cur) => acc.childNodes[cur], document.body);
}

// two-way bindings path, value pair

let inputs = {};

function childIndex(node) {
    for (var i = 0; i < node.parentNode.childNodes.length; i++) {
        if (node.parentNode.childNodes[i] === node) {
            return i;
        }
    }
    return -1;
}

function pathToBody(node) {
    var path = [];
    var it = node;
    while (it !== document.body) {
        path.unshift(childIndex(it));
        it = it.parentNode;
    }
    return path;
}

function setValue(event, name, value) {
    const path = JSON.stringify(pathToBody(event.target));
    post({
        state: [path, name, value]
    })
}

function handleInput(event) {
    const path = pathToBody(event.target);
    inputs[JSON.stringify(path)] = event.target.value;
}

function buidlArgs(event) {
    switch (event.type) {
        case 'input':
        case 'change':
            return [event.target.value];
        default:
            return [];
    }
}

function handleEvent(name, event) {
    event.preventDefault();
    const path = pathToBody(event.currentTarget);
    const args = buidlArgs(event);

    post({
        event: [path, name, args],
        inputs
    });
}

// return the resolved node and the caller node
// when assigning the whole resolved node we loose the function binding
// ie. x = resolve(['location', 'replace']), x(..args) cause an invalid invocation, because we've lost the binding context
// x.call(window.location, args)
// But also for assignment to keep the reference. resolveNode[['document', 'title']) = 'Changed';
function resolveObject(path) {
    const last = path.pop();
    const resolved = path.reduce((acc, cur) => acc[cur], window);
    return [resolved, last];
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

function executeJsDump(dump) {
    for (const [mode, path, args] of dump) {
        const [obj, bind] = resolveObject(path);
        if (mode === 'invoke')
            obj[bind](...args);
        else if (mode === 'assign')
            obj[bind] = args;
    }
}

function callback(err, data) {
    if (err) {
        console.error(err);
        return;
    }

    // execute JS code
    executeJsDump(data.j);

    inputs = {};

    for (const patch of data.p) {
        const [path, type, value] = patch;
        const node = resolveNode(path);
        switch (type) {
            case 'text':
                // text replace
                if (node.type === 'textarea')
                    node.value = value;
                else
                    node.textContent = value;
                break;
            case 'replace':
                node.outerHTML = value;
                break;
        }
    }
    prevLocation = window.location.pathname + window.location.search;
}


(function (global) {
    if (!global.spwa) {
        global.spwa = {};
    }

    global.spwa.refresh = function () {
        post(null);
    };

})(window);


