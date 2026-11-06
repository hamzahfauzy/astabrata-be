import {renderer} from './template/Renderer.js'

const templateCache = new Map();

let renderContext = {};

function setRenderContext(context) {
    renderContext = context;
}

async function readFile(path) {

    if (templateCache.has(path)) {
        return templateCache.get(path);
    }

    const response = await fetch(path);

    if (!response.ok) {
        throw new Error(`Tidak dapat membaca ${path}`);
    }

    const text = await response.text();

    templateCache.set(path, text);

    return text;

}

const renderString = async function(htmlString, data) {
    return await renderer(htmlString, data, renderContext)
}

const render = async function(viewFile, data){

    var htmlString = await readFile('/src/views/'+viewFile + '.vtp')

    return await renderString(htmlString, data)

}

const initLayout = async function(viewFile) {
    const htmlString = await readFile('/src/'+viewFile+'.vtp')

    const layout = await renderer(htmlString, {}, renderContext)

    document.querySelector('#app').innerHTML = layout
}

export default {
    render,
    renderString,
    setRenderContext,
    initLayout
}