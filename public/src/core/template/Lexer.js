export function lexer(template) {

    const tokens = [];

    const directives = [
        "elseif",
        "else",
        "endif",
        "foreach",
        "endforeach",
        "endfor",
        "for",
        "if",
        "component"
    ];

    let i = 0;
    let html = "";

    while (i < template.length) {

        if (template[i] !== "@") {
            html += template[i++];
            continue;
        }

        // Simpan html sebelumnya
        if (html) {
            tokens.push({
                type: "html",
                value: html
            });
            html = "";
        }

        i++; // skip @

        // -------------------------
        // Baca nama directive
        // -------------------------
        let name = "";

        while (
            i < template.length &&
            /[A-Za-z_]/.test(template[i])
        ) {
            name += template[i++];
        }

        // Bukan directive
        if (!directives.includes(name)) {
            html += "@" + name;
            continue;
        }

        // Skip whitespace
        while (
            i < template.length &&
            /\s/.test(template[i])
        ) {
            i++;
        }

        let expression = "";

        // -------------------------
        // Directive tanpa parameter
        // -------------------------
        if (template[i] !== "(") {

            tokens.push({
                type: "directive",
                name,
                expression
            });

            continue;

        }

        // -------------------------
        // Baca expression
        // -------------------------
        i++; // skip (

        let depth = 1;

        while (i < template.length && depth > 0) {

            const ch = template[i];

            if (ch === "(") {
                depth++;
            }
            else if (ch === ")") {
                depth--;

                if (depth === 0) {
                    i++;
                    break;
                }
            }

            if (depth > 0) {
                expression += ch;
            }

            i++;
        }

        tokens.push({
            type: "directive",
            name,
            expression: expression.trim()
        });

    }

    if (html) {

        tokens.push({
            type: "html",
            value: html
        });

    }

    return tokens;

}