export default class HttpClient {

    constructor(config) {

        this.context = config.context;

        this.defaults = config.defaults;

        this.requestInterceptors = config.requestInterceptors;

        this.responseInterceptors = config.responseInterceptors;

    }

    async request(method, url, data = null, options = {}) {

        let controller;

        if (options.signal) {

            controller = {
                signal: options.signal
            };

        } else if (this.context) {

            controller = this.context.createAbortController();

        } else {

            controller = new AbortController();

        }

        // query string
        if (options.query) {

            const qs = new URLSearchParams(options.query);

            url += (url.includes("?") ? "&" : "?") + qs.toString();

        }

        let config = {

            method,

            headers: {
                ...this.defaults.headers,
                ...(options.headers || {})
            },

            signal: controller.signal

        };

        // body
        if (data !== null) {

            if (data instanceof FormData) {

                config.body = data;

            } else {

                config.headers["Content-Type"] = "application/json";

                config.body = JSON.stringify(data);

            }

        }

        config.url = this.defaults.baseURL + url;

        // request interceptor
        for (const interceptor of this.requestInterceptors) {

            config = await interceptor(config) || config;

        }

        const timeout = options.timeout ?? this.defaults.timeout;

        let timeoutId = null;

        if (timeout > 0) {

            timeoutId = setTimeout(() => {

                controller.abort("timeout");

            }, timeout);

        }

        let response;

        try {

            response = await fetch(config.url, config);

        } finally {

            clearTimeout(timeoutId);

        }

        // response interceptor
        for (const interceptor of this.responseInterceptors) {

            response = await interceptor(response) || response;

        }

        const contentType = response.headers.get("content-type") || "";

        let body;

        if (contentType.includes("application/json")) {

            body = await response.json();

        } else {

            body = await response.text();

        }

        if (!response.ok) {

            const error = new Error(response.statusText);

            error.status = response.status;

            error.data = body;

            const message = body.data ? Object.values(body.data).join("<br>") : ''

            Swal.fire({
                title: "Error!",
                html: body.message + "<br>" + message,
                icon: "error",
                confirmButtonText: "Ok",
            })

            throw error;

        }

        return body;

    }

    get(url, options = {}) {

        return this.request("GET", url, null, options);

    }

    post(url, data, options = {}) {

        return this.request("POST", url, data, options);

    }

    put(url, data, options = {}) {

        data.append('_method', 'PUT')

        return this.request("POST", url, data, options);

    }

    patch(url, data, options = {}) {

        data.append('_method', 'PATCH')

        return this.request("POST", url, data, options);

    }

    delete(url, options = {}) {

        return this.request("DELETE", url, null, options);

    }

}