function resolveNode(path) {
    return path.reduce((acc, cur) => acc.childNodes[cur], document.body);
}