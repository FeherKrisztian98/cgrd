export default class Router {
    /** @var {String} csrfToken CSRF token to protect API requests */
    csrfToken = '';

    /**
     * Headers to send with every request
     *
     * @type {{Accept: string, "X-SPA-Request": string}}
     */
    headers = {
        'Accept': 'application/json',
        'X-SPA-Request': 'true',
    };

    /**
     * Retrieves the headers for the request with the CSRF token and any extra headers provided
     *
     * @param {Object} [extraHeaders={}] - Optional additional headers
     *
     * @returns {Object} The merged headers object, including the default headers and any extra headers
     */
    getHeaders(extraHeaders = {}) {
        return {
            ...this.headers,
            ...extraHeaders,
            'X-CSRF-Token': this.csrfToken,
            'X-SPA-FROM': window.location.pathname,
        };
    }

    /**
     * Sends an HTTP request to the specified route using the given method, body, and headers
     * Handles the response and returns the processed data
     *
     * @param {string} route - The route to send the request to
     * @param {string} [method='GET'] - The HTTP method to use (default is 'GET')
     * @param {Object|null} [body=null] - The body of the request. Typically used with methods like 'POST', 'PUT', etc
     * @param {Object} [extraHeaders={}] - Additional headers to include in the request
     * @param {string} [extraHeaders.key] - A key-value pair for additional headers
     * @param {boolean} noHistory - Whether to save the route into the browser history
     *
     * @returns {Promise<Object|null>} A promise that resolves to the processed response data or null if an error occurs
     */
    async request(route, method = 'GET', body = null, extraHeaders = {}, noHistory = false) {
        const options = {
            method,
            headers: this.getHeaders(extraHeaders),
        };

        if (body) {
            options.body = new URLSearchParams(body).toString();
        }

        try {
            const response = await fetch(route, options);
            const data = await response.json();
            return this.handleResponse(route, data, noHistory);
        } catch (error) {
            console.error(`Fetch error on ${method} ${route}:`, error);
        }
    }

    /**
     * Sends a GET request to the specified route
     *
     * @param {string} route - The route to send the GET request to
     *
     * @returns {Promise<Object|null>} A promise that resolves to the processed response data or null if an error occurs
     */
    async get(route, noHistory = false) {
        return this.request(route, 'GET', null, {}, noHistory);
    }

    /**
     * Sends a POST request to the specified route with the provided body
     * The body is sent as 'application/x-www-form-urlencoded'
     *
     * @param {string} route - The route to send the POST request to
     * @param {Object} body - The body to send with the request, typically a key-value pair
     *
     * @returns {Promise<Object|null>} A promise that resolves to the processed response data or null if an error occurs
     */
    async post(route, body) {
        return this.request(route, 'POST', body, {
            'Content-Type': 'application/x-www-form-urlencoded',
        });
    }

    /**
     * Sends a PUT request to the specified route with the provided body
     * The body is sent as 'application/x-www-form-urlencoded'
     *
     * @param {string} route - The route to send the PUT request to
     * @param {Object} body - The body to send with the request, typically a key-value pair
     *
     * @returns {Promise<Object|null>} A promise that resolves to the processed response data or null if an error occurs
     */
    async put(route, body) {
        return this.request(route, 'PUT', body, {
            'Content-Type': 'application/x-www-form-urlencoded',
        });
    }

    /**
     * Sends a DELETE request to the specified route
     *
     * @param {string} route - The route to send the DELETE request to
     *
     * @returns {Promise<Object|null>} A promise that resolves to the processed response data or null if an error occurs
     */
    async delete(route) {
        return this.request(route, 'DELETE');
    }

    /**
     * Displays a notification with a message and a notification type class
     * The notification will automatically hide after 3 seconds
     *
     * @param {string} notificationMessage - The message to display in the notification
     * @param {string} notificationTypeClass - The CSS class for the notification type
     */
    showNotification(notificationMessage, notificationTypeClass) {
        const container = document.querySelector('.notification');
        if (!container) {
            return;
        }
        container.classList.add(notificationTypeClass);
        container.innerHTML = notificationMessage;
        container.style.display = 'block';

        // Automatically hide notification after 3 seconds
        setTimeout(() => {
            container.style.display = 'none';
            container.classList.remove(notificationTypeClass);
        }, 3000);
    }

    /**
     * Pushes a new route to the browser's history or replaces the current history entry.
     *
     * @param {string} route - The route to push to history.
     * @param {boolean} [replaceHistory=false] - Whether to replace the current history entry (defaults to false).
     */
    pushToHistory(route, replaceHistory = false) {
        const newPath = new URL(route, window.location.origin).pathname;
        if (window.location.pathname !== newPath) {
            replaceHistory
                ? history.replaceState({}, '', newPath)
                : history.pushState({}, '', newPath);
        }
    }

    /**
     * Updates the content of the page by replacing the inner HTML of the #app container.
     *
     * @param {string} content - The new HTML content to insert into the #app container.
     */
    updateContent(content) {
        const appContainer = document.querySelector('#app');
        if (appContainer) {
            appContainer.innerHTML = content;
        }
    }

    /**
     * Handles the response from the server
     *
     * @param {string} route - The route that was requested
     * @param {Object} response - The response object from the server
     * @param {string} [response.redirect] - URL to redirect to
     * @param {boolean} [response.replaceHistory] - Whether to replace the browser history
     * @param {string} [response.content] - HTML content
     * @param {Object} [response.notification] - Notification data
     * @param {string} [response.notification.message] - Notification message
     * @param {string} [response.notification.type] - Notification type class (e.g., 'notification--success')
     * @param {string} [response.csrfToken] - CSRF token for the next request
     * @param {boolean} noHistory Whether to save the route into the browser history
     *
     * @returns {Promise<Object>} The original response object.
     */
    async handleResponse(route, response, noHistory = false) {
        // Handle redirection with history replacement
        if (response.redirect) {
            await this.get(response.redirect, response.replaceHistory);
            if (response.replaceHistory) {
                this.pushToHistory(response.redirect, true);
            }
        }

        // Update page content if available
        if (response.content) {
            this.updateContent(response.content);

            if (!noHistory) {
                this.pushToHistory(route);
            }
        }

        // Show notifications if present
        if (response.notification) {
            this.showNotification(response.notification.message, response.notification.type);
        }

        // Update CSRF token if provided
        if (response.csrfToken) {
            this.csrfToken = response.csrfToken;
        }

        return response;
    }

    /**
     * Initializes the application by performing a GET request to the root route ('/')
     *
     * @returns {Promise<void>} A promise that resolves when the GET request is completed
     */
    async init() {
        await this.get('/', true);
    }
}
