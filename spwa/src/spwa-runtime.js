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

function handleEvent(name, event) {
    // console.log('We need to handle', event, 'on', path);
    const path = pathToBody(event.target);

    post({
        event: [path, name],
        inputs
    });
}

function resolveObject(path) {
    return path.reduce((acc, cur) => acc[cur], window);
}

function post(data) {
    var xhr = new XMLHttpRequest();
    xhr.open("POST", '/', true);
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

function callback(err, data) {
    if (err) {
        console.error(err);
        return;
    }

    inputs = {};

    for (const patch of data.p) {
        const [type, path, value] = patch;
        const node = resolveNode(path);
        switch (type) {
            case 0:
                // delete
                break;
            case 1:
                // replace
                break;
            case 2:
                // insert
                break;
            case 3:
                // text replace
                node.textContent = value;
                break;
        }
    }
    // execute JS code
    for (const [path, args] of data.j) {
        const obj = resolveObject(path);
        obj(...args);
    }
}