import view from "./view.js";

export default class App {

    #state = {};

    events = [];

    globals = {};

    flash = new Map();

    functions = new Map();

    components = new Map();

    function(name, callback) {

        this.functions.set(name, callback);

    }

    getFunctions() {

        return Object.fromEntries(this.functions);

    }

    component(name, callback) {

        if(typeof callback === "string"){

            callback = (props) =>
                view.renderString(callback, props);

        }

        this.components.set(name, callback);

    }

    getComponent(name) {

        return this.components.get(name);

    }

    provide(name, value) {
        this.globals[name] = value;
    }

    getGlobal(name)
    {
        return this.globals[name]
    }

    on(selector, event, callback) {

        this.events.push({
            selector,
            event,
            callback
        });

    }

    mount(root = document) {

        for (const item of this.events) {

            const elements = root.querySelectorAll(item.selector);

            elements.forEach(element => {

                if (element.__vtpEvents == null) {
                    element.__vtpEvents = {};
                }

                // Hindari event ganda
                if (element.__vtpEvents[item.event]) {
                    element.removeEventListener(
                        item.event,
                        element.__vtpEvents[item.event]
                    );
                }

                element.addEventListener(
                    item.event,
                    item.callback
                );

                element.__vtpEvents[item.event] =
                    item.callback;

            });

        }

    }

    state(key, value) {
        this.#state[key] = value;
    }

    getState(key) {
        return this.#state[key];
    }

    can(userPermissions, actionPermissions) {

        // pastikan actionPermissions selalu array
        if (!Array.isArray(actionPermissions)) {
            actionPermissions = [actionPermissions];
        }

        // super admin
        if (userPermissions.includes('*')) {
            return true;
        }

        return actionPermissions.some(action => {

            return userPermissions.some(permission => {

                // exact match
                if (permission === action) {
                    return true;
                }

                // wildcard
                if (permission.endsWith('*')) {
                    const prefix = permission.slice(0, -1);
                    return action.startsWith(prefix);
                }

                return false;

            });

        });

    }

    async refresh(componentName, props){
        const component = this.getComponent(componentName);

        const oldElement = document.querySelector(
            `[data-vtp-component="${componentName}"]`
        );

        if (!oldElement) {
            return;
        }

        const html = await component(props);

        const template = document.createElement("template");
        template.innerHTML = html.trim();

        const newElement = template.content.firstElementChild;

        oldElement.replaceWith(newElement);

        this.mount()
    }

}