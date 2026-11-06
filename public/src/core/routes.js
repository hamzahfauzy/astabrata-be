class Route {

    static routes = [];

    static add(path, layout, ...handlers) {

        this.routes.push({
            path,
            layout,
            handlers
        });

        return this;
    }

    static getRoutes() {
        return this.routes;
    }

}

export default Route;