import Router from "./Router.js";

/**
 * Router to handle AJAX requests
 *
 * @type {Router}
 */
const router = new Router();


/** Initializes the page based on the current URL. If the pathname is not '/', it loads the corresponding content via a GET request */
window.addEventListener('load', async () => {
    if (window.location.pathname !== '/') {
        await router.get(window.location.pathname);
    } else {
        await router.init();
    }
});

/**
 * This event is triggered when navigating through the browser history (e.g., back button)
 * It fetches the page corresponding to the current `location.pathname` using the router
 */
window.addEventListener('popstate', async () => {
    await router.get(location.pathname);
});

/** Intercepts clicks on elements with a `data-route` attribute to handle routing */
document.body.addEventListener('click', (event) => {
    let target = event.target;

    // Find the closest element with the `data-route` attribute
    if (target.dataset.route === undefined) {
        target = target.closest('[data-route]');
    }

    // If no matching element, return early
    if (target === null) {
        return;
    }

    // If the element does not have a `data-route`, return early
    if (target.dataset.route === undefined) {
        return;
    }

    // Handle form submissions if `data-serialize-form` is present
    if (target.dataset.serializeForm) {
        const formData = new FormData(target.closest('form'));
        if (target.dataset.method === 'PUT') {
            return router.put(target.dataset.route, formData);
        }
        return router.post(target.dataset.route, formData);
    }

    // Handle DELETE request
    if (target.dataset.method === 'DELETE') {
        return router.delete(target.dataset.route);
    }

    // Handle GET request
    return router.get(target.dataset.route);
})