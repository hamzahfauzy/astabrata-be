import Http from "./http.js";

export default class Context {

    events = [];

    abortControllers = [];

    constructor(router) {

        this.router = router;

        this.path = window.location.pathname;

        this.http = Http.create(this);

        this.app = router.app

        this.params = {};

        this.query = Object.fromEntries(
            new URLSearchParams(location.search)
        );

        this._mounted = [];
        this._destroyed = [];
    }

    on(selector, event, callback) {

        const elements = document.querySelectorAll(selector);

        if(elements.length)
        {
            elements.forEach(element => {
                element.addEventListener(event, callback);
        
                this.events.push({
                    element,
                    event,
                    callback
                });
            })
        }



    }

    createAbortController() {
        const controller = new AbortController();
        this.abortControllers.push(controller);
        return controller;
    }

    onMounted(callback) {
        this._mounted.push(callback);
    }

    onDestroyed(callback) {
        this._destroyed.push(callback);
    }

    mount() {
        for (const callback of this._mounted) {
            callback(this);
        }
    }

    destroy() {
        for (const callback of this._destroyed) {
            callback(this);
        }

        for (const e of this.events) {
            e.element.removeEventListener(e.event, e.callback);
        }

        this.events = [];

        for(const controller of this.abortControllers){
            controller.abort();
        }

        this.abortControllers = [];

        this._mounted = [];

        this._destroyed = [];
    }

    redirect(path) {
        this.router.navigate(path);
    }

    async refresh() {

        this.destroy();

        const html = await this.controller[this.method](this);

        const routerView = document.querySelector("[data-vtprouter]");

        routerView.innerHTML = html;

        this.mount();

    }

    flash(key, value) {

        if (arguments.length === 2) {
            this.router.app.flash.set(key, value);
            return;
        }

        const valueResult = this.router.app.flash.get(key);

        this.router.app.flash.delete(key);

        return valueResult;

    }

    loadScript(scriptUrl, loadedMessage = '') {
        const myScript = document.createElement('script');
    
        // 3. Set the source to your external JS file
        myScript.src = scriptUrl;
        
        // 4. (Optional) Run code right after the script finishes downloading
        if(loadedMessage)
        {
            myScript.addEventListener('load', () => {
                    console.log(loadedMessage);
                // console.log('Script loaded successfully and is ready to use!');
                // You can safely call functions defined inside your-script.js here
            });
        }

        // 5. Append the script to the body to trigger the network fetch
        document.body.appendChild(myScript);
    }

}