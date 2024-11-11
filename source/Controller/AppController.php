<?php

namespace Controller;

use HTTP\Response;
use Service\Auth;

/**
 * Handles the main application logic for the root endpoint
 */
class AppController extends AbstractController
{
    /**
     * Displays the homepage or redirects based on user authentication status
     * @return Response
     */
    public function index(): Response
    {
        if ($this->request->isAjax()) {
            if (Auth::isLoggedIn()) {
                return $this->response->redirect('/news', $this->request->isRefererEmpty());
            }
            return $this->response->redirect('/login', true);
        }

        return $this->response->page('index', [
            'title' => 'cgrd',
        ]);
    }

    /**
     * Displays a basic 404 page
     * @return Response
     */
    public function notFound(): Response
    {
        return $this->response->view('notFound');
    }
}