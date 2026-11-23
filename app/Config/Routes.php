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

// Comentários (Sprint 6)
$routes->match(['GET', 'POST'], 'coments', 'Coments::index');
$routes->get('coments/limpar', 'Coments::limpar');
$routes->post('coments/add/(:num)', 'Coments::add/$1');
$routes->post('coments/update/(:num)', 'Coments::update/$1');

// Relatório de atividades (Sprint 6)
$routes->match(['GET', 'POST'], 'relatorios', 'Relatorios::index');
$routes->get('relatorios/limpar', 'Relatorios::limpar');

// Cadastros auxiliares (Sprint 6) — cidades
$routes->get('cidades', 'Cidades::index');
$routes->match(['GET', 'POST'], 'cidades/add', 'Cidades::add');
$routes->match(['GET', 'POST'], 'cidades/edit/(:num)', 'Cidades::edit/$1');
$routes->get('cidades/remove/(:num)', 'Cidades::remove/$1');

// Cadastros auxiliares (Sprint 6) — procedimentos
$routes->get('procedimentos', 'Procedimentos::index');
$routes->match(['GET', 'POST'], 'procedimentos/add', 'Procedimentos::add');
$routes->match(['GET', 'POST'], 'procedimentos/edit/(:num)', 'Procedimentos::edit/$1');
$routes->get('procedimentos/remove/(:num)', 'Procedimentos::remove/$1');

// Cadastros auxiliares (Sprint 6) — catpessoas
$routes->get('catpessoas', 'Catpessoas::index');
$routes->match(['GET', 'POST'], 'catpessoas/add', 'Catpessoas::add');
$routes->match(['GET', 'POST'], 'catpessoas/edit/(:num)', 'Catpessoas::edit/$1');
$routes->get('catpessoas/remove/(:num)', 'Catpessoas::remove/$1');

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
