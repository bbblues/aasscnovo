<?php

use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */

// Dashboard (Sprint 4) — protegido pelo filtro global `auth`.
$routes->get('/', 'Home::index');

// Pessoas (Sprint 5)
$routes->match(['GET', 'POST'], 'pessoas', 'Pessoas::index');
$routes->get('pessoas/limpar', 'Pessoas::limpar');
$routes->match(['GET', 'POST'], 'pessoas/edit/(:num)', 'Pessoas::edit/$1');

// Solicitações (Sprint 5)
$routes->match(['GET', 'POST'], 'solicitacoes', 'Solicitacoes::index');
$routes->get('solicitacoes/limpar', 'Solicitacoes::limpar');
$routes->match(['GET', 'POST'], 'solicitacoes/abertas', 'Solicitacoes::abertas');
$routes->get('solicitacoes/edit/(:num)', 'Solicitacoes::edit/$1');
$routes->post('solicitacoes/update/(:num)', 'Solicitacoes::update/$1');
$routes->post('solicitacoes/add', 'Solicitacoes::add');
$routes->match(['GET', 'POST'], 'solicitacoes/add_pessoa', 'Solicitacoes::addPessoa');

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
