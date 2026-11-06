import { compileDirective } from "./Directive.js";

export function compile(tokens, app) {

    let code = "let html = `";

    for (const token of tokens) {

        if (token.type === "html") {

            code += token.value.replace(
                /\{\{\s*(.*?)\s*\}\}/g,
                '${$1}'
            );

            continue;

        }

        code += compileDirective(
            token.name,
            token.expression
        );

    }

    code += "`;\n";

    code += "return html;";

    return code;

}