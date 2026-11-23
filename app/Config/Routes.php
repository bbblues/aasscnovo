<?php

use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */

// Dashboard (Sprint 4) — protegido pelo filtro global `auth`.
$routes->get('/', 'Home::index');

// Autenticação — público
$routes->match(['GET', 'POST'], 'auth/login', 'Auth::login');
$routes->match(['GET', 'POST'], 'auth/forgot_password', 'Auth::forgotPassword');
$routes->match(['GET', 'POST'], 'auth/reset_password/(:any)', 'Auth::resetPassword/$1');
$routes->get('auth/logout', 'Auth::logout');

// Autenticação — usuário logado
$routes->match(['GET', 'POST'], 'auth/change_password', 'Auth::changePassword');

// Gestão de usuários/grupos — admin
$routes->get('auth', 'Auth::index');
$routes->match(['GET', 'POST'], 'auth/create_user', 'Auth::createUser');
$routes->match(['GET', 'POST'], 'auth/edit_user/(:num)', 'Auth::editUser/$1');
$routes->post('auth/activate/(:num)', 'Auth::activate/$1');
$routes->post('auth/deactivate/(:num)', 'Auth::deactivate/$1');
$routes->match(['GET', 'POST'], 'auth/create_group', 'Auth::createGroup');
$routes->match(['GET', 'POST'], 'auth/edit_group/(:num)', 'Auth::editGroup/$1');
