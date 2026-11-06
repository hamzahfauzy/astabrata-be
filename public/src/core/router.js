import view from "./view.js";
import Context from "./context.js";
import { isAuthenticated } from "../libs/functions.js";

export default class Router {

    layouts = null
    context = null

    constructor(routes, app, options = {}) {
        this.routes = routes;
        this.app = app;

        this.layouts = null;
        this.context = null;

        this.defaultLayout = options.defaultLayout ?? "views/layouts/app";
    }

    async load() {
        const path = window.location.pathname;
        const matched = this.match(path);

        if (!matched) {
            this.render404();
            return;
        }

        const route = matched.route;
        const params = matched.params;

        if (!route) {
            this.render404();
            return;
        }

        if (this.context) {
            this.context.destroy();
        }

        const context = new Context(this);

        context.params = params;

        this.context = context;

        view.setRenderContext(context);

        const handlers = route.handlers;
        for (const handler of handlers) {
            if (typeof handler === "function") {
                const result = await handler(context);
                if (result === false) {
                    return;
                }

                continue;
            }

            if (Array.isArray(handler)) {
                try {
                    if(this.layouts != route.layout)
                    {
                        this.layouts = route.layout
                        await view.initLayout(route.layout)

                        this.app.mount(document.querySelector('#app'))
                    }
                    
                    const [controllerPath, method] = handler;
                    const module = await import(`../controllers/${controllerPath}.js`);
                    const Controller = module.default;
                    const controller = new Controller();

                    context.controller = controller;
                    context.method = method;

                    context.refresh()

                } catch (e) {
                    console.error(e);
                    document.querySelector("[data-vtprouter]").innerHTML = `
                        <h1>500</h1>
                        <pre>${e.message}</pre>
                    `;
                }
            }

        }

    }

    navigate(path) {
        history.pushState({}, "", path);
        
        this.app.refresh('vtp-sidebar')
        
        this.load();
    }

    start() {
        this.load();

        window.addEventListener("popstate", () => {
            this.load();
            this.app.refresh('vtp-sidebar')
        });

        document.addEventListener("click", (e) => {
            const link = e.target.closest("[router-link]");
            if(!link) return
            e.preventDefault();
            const href = new URL(link.getAttribute("href"), window.location.origin);
            const current = new URL(window.location.href);

            if (
                href.pathname === current.pathname &&
                href.search === current.search &&
                href.hash === current.hash
            ) {
                return;
            }

            this.navigate(link.getAttribute("href"));

            return
        });
    }

    match(path) {

        for (const route of this.routes) {

            const params = {};

            const routeParts = route.path.split("/").filter(Boolean);
            const pathParts = path.split("/").filter(Boolean);

            if (routeParts.length !== pathParts.length) {
                continue;
            }

            let matched = true;

            for (let i = 0; i < routeParts.length; i++) {

                const routePart = routeParts[i];
                const pathPart = pathParts[i];

                if (routePart.startsWith(":")) {

                    params[routePart.substring(1)] = decodeURIComponent(pathPart);

                    continue;
                }

                if (routePart !== pathPart) {
                    matched = false;
                    break;
                }

            }

            if (matched) {

                return {
                    route,
                    params
                };

            }

        }

        return null;

    }

    async render404() {
        const layout = this.defaultLayout;

        const context = new Context(this);

        await isAuthenticated(context)

        console.log(context.app)

        context.params = {};

        this.context = context;

        view.setRenderContext(context);

        if (this.layouts !== layout) {
            await view.initLayout(layout);
            this.layouts = layout;

            this.app.mount(document.querySelector("#app"));
        }

        const outlet = document.querySelector("[data-vtprouter]");

        if (!outlet) {
            console.error(
                '[VTP Router] Outlet "[data-vtprouter]" tidak ditemukan setelah layout diinisialisasi.'
            );
            return;
        }

        outlet.innerHTML = `
            <section class="vtp-error-404">
                <h1>404</h1>
                <p>Halaman yang kamu cari tidak ditemukan.</p>
            </section>
        `;
    }

}