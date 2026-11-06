import HttpClient from "./httpclient.js";

export default class Http {

    static defaults = {
        baseURL: "",
        timeout: 30000,
        headers: {}
    };

    static requestInterceptors = [];

    static responseInterceptors = [];

    static beforeRequest(callback) {
        this.requestInterceptors.push(callback);
    }

    static afterResponse(callback) {
        this.responseInterceptors.push(callback);
    }

    static create(context = null) {

        return new HttpClient({
            context,
            defaults: this.defaults,
            requestInterceptors: this.requestInterceptors,
            responseInterceptors: this.responseInterceptors
        });

    }

}