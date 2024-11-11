<?php

namespace Controller;

use ENUM\NotificationType;
use HTTP\Response;
use Service\Auth;

/**
 * Handles user authentication processes, including login, authentication checks, and logout
 */
class AuthController extends AbstractController
{
    /**
     * Authenticates a user based on provided username and password
     *
     * @return Response
     */
    public function auth(): Response
    {
        $username = $this->request->getParam('username');
        $password = $this->request->getParam('password');

        if (Auth::login($username, $password)) {
            return $this->response->redirect('/news', true);
        }

        $this->response->setNotification('Wrong Login Data', NotificationType::ERROR);

        return $this->response->redirect('/login');
    }

    /**
     * Renders the login page or redirects if already logged in
     *
     * @return Response
     */
    public function login(): Response
    {
        if (Auth::isLoggedIn()) {
            return $this->response->redirect('/news', true);
        }
        return $this->response->view('AuthController/login');
    }

    /**
     * Logs out the current user and redirects to the login page
     *
     * @return Response
     */
    public function logout(): Response
    {
        Auth::logout();

        return $this->response->redirect('/login');
    }
}