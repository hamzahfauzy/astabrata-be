const directives = new Map();

export function directive(name, compiler) {
    directives.set(name, compiler);
}

export function compileDirective(name, expression = "") {

    if (!directives.has(name)) {
        throw new Error(`Unknown directive @${name}`);
    }

    return directives.get(name)(expression);

}