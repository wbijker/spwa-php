function createNode(value) {
    if (value.type === 0) {
        const node = document.createElement(value.tag);
        for (const key in value.attributes) {

            // handle special cases
            if (key === 'click' || key === 'keydown') {
                node.setAttribute('on' + key, `eventHandler('${value.attributes[key]}', event)`);
                continue;
            }
            if (key === 'bound') {
                node.setAttribute('oninput', `handleInput(event, '${value.attributes[key]}')`);
                continue;
            }

            node.setAttribute(key, value.attributes[key]);
        }

        for (const child of value.children) {
            node.appendChild(createNode(child));
        }
        return node;
    }
    if (value.type === 1) {
        return document.createTextNode(value.text);
    }
    if (value.type === 2) {
        return document.createComment(value.text);
    }
}

function transversePaths(arr) {
    let it = document.body;

    for (const path of arr) {
        it = it.childNodes[path];
    }
    return it;
}

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

function getJsFunction(path) {
    let it = window;
    for (const name of path) {
        it = it[name];
    }
    return it;
}

function getSibling(node, index) {
    let it = node;
    let i = 0;
    while (it && i++ < index) {
        it = it.nextSibling;
    }
    return it;
}

function applyPatch(patch) {
    if (!patch.path) {
        return;
    }

    // update text
    const node = transversePaths(patch.path);
    switch (patch.type) {
        // update text
        case 0:
            node.textContent = patch.value;
            break;
        // update attr
        case 1:
            node.setAttribute(patch.key, patch.value);
            break;
        // delete node
        case 2:
            let child = getSibling(node.nextSibling, patch.index)
            node.parentNode.removeChild(child);
            break;
        // insert node
        case 3:
            const newNode = createNode(patch.value);
            // node should be the comment marking the start of the for loop
            let before = getSibling(node.nextSibling, patch.index)
            if (before)
                node.parentNode.insertBefore(newNode, before);
            else
                node.parentNode.appendChild(newNode);
            break;
        case 4:
            // delete attr
            node.removeAttribute(patch.key);
            break;
    }
}

function serializeEvent(e) {
    if (e instanceof PointerEvent) {
        return {
            type: e.type,
            timeStamp: e.timeStamp,
            clientX: e.clientX,
            clientY: e.clientY,
            button: e.button,
            buttons: e.buttons,
            pointerId: e.pointerId,
            pointerType: e.pointerType,
            isPrimary: e.isPrimary,
            // ... other PointerEvent properties
        };
    }

    if (e instanceof KeyboardEvent) {
        return {
            type: e.type,
            timeStamp: e.timeStamp,
            key: e.key,
            code: e.code,
            location: e.location,
            ctrlKey: e.ctrlKey,
            shiftKey: e.shiftKey,
            altKey: e.altKey,
            metaKey: e.metaKey,
            repeat: e.repeat,
            // ... other KeyboardEvent properties
        };
    }
    if (e instanceof MouseEvent) {
        return {
            type: e.type,
            timeStamp: e.timeStamp,
            clientX: e.clientX,
            clientY: e.clientY,
            button: e.button,
            buttons: e.buttons,
            // ... other MouseEvent properties
        };
    }
}

const inputs = {};

function handleInput(e, path) {
    const name = path.join(',');
    inputs[name] = {
        change: true,
        value: e.target.value
    }
}

function eventHandler(action, e) {
    const changes = {};
    for (const name in inputs) {
        if (inputs[name].change) {
            changes[name] = inputs[name].value
        }
    }
    const path = pathToBody(e.target);
    postData('/', {action, path, event: serializeEvent(e), inputs: changes}, (err, data) => {
        for (const patch of data.patches) {
            applyPatch(patch);
        }

        // execute JS calls
        for (const call of data.js) {
            const path = call[0];
            const args = call[1];
            const fn = getJsFunction(path);
            fn.apply(null, args);
        }

        // mark all inputs as not changed
        for (const name in inputs) {
            inputs[name].change = false;
        }

    });
}

function postData(url, data, callback) {
    var xhr = new XMLHttpRequest();
    xhr.open("POST", url, true);
    xhr.setRequestHeader("Content-Type", "application/json");

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
