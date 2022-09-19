var htmlToElements = function (html) {
    var template = document.createElement('template');
    template.innerHTML = html;

    const nodes = template.content.childNodes,
        nodesArray = [],
        scriptsArray = [];
    for (var i in nodes) {
        if (nodes[i].nodeType == 1) { // get rid of the whitespace text nodes
            if (nodes[i].nodeName === 'SCRIPT') {
                scriptsArray.push(nodes[i]);
            } else {
                nodesArray.push(nodes[i]);
            }
        }
    }
    return nodesArray.concat(scriptsArray);
}

var loadContent = function (data, index, container, appendData = false) {
    if (index === 0 && !appendData) {
        document.querySelector(container).innerHTML = '';
    }
    if (index <= data.length) {
        var element = data[index];
        if (element !== undefined && element.nodeName === 'SCRIPT') {
            // output scripts
            var script = document.createElement('script');
            script.type = 'text/javascript';
            if (element.src != '') {
                script.src = element.src;
                script.onload = function () {
                    loadContent(data, index + 1, container);
                };
                document.head.appendChild(script);
            } else {
                script.text = element.text;
                document.body.appendChild(script);
                loadContent(data, index + 1, container);
            }
        } else {
            if (element !== undefined) {
                document.querySelector(container).appendChild(element);
            }
            loadContent(data, index + 1, container);
        }
    } else {
        return true;
    }
};

var loadData = async function (data, container, appendData = false) {
    return loadContent(htmlToElements(data), 0, container, appendData);
}
