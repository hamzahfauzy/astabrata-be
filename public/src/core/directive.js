import { directive } from "./template/Directive.js";

directive("if", exp => `
\`;
if(${exp}){
html += \`
`);

directive("elseif", exp => `
\`;
}else if(${exp}){
html += \`
`);

directive("else", () => `
\`;
}else{
html += \`
`);

directive("endif", () => `
\`;
}
html += \`
`);

directive("for", exp => `
\`;
for(${exp}){
html += \`
`);

// directive("console", exp => `console.log(${exp});`);

directive("endfor", () => `
\`;
}
html += \`
`);

directive("component", exp => `
\`;
html += await __component(${exp});
html += \`
`);