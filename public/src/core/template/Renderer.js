import { lexer } from "./Lexer.js";
import { compile } from "./Compiler.js";

export async function renderer(template,data={}, ctx = {}){

    const app = ctx.app

    const tokens = lexer(template);

    const __component = async (name, props) => {

        const component = app.getComponent(name);

        if (!component) {
            throw new Error(`Component "${name}" is not registered.`);
        }

        return await component(props, ctx);

    }

    const js = compile(tokens, app);

    const render = new Function(
        "data",
        "app",
        "__component",
        `
        return (async () => {
            with({...app.globals, ...app.getFunctions(), ...data}){
                ${js}
            }
        })();
        `
    );

    return await render(data, app, __component);

}