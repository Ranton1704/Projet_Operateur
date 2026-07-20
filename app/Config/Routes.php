<?php

use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */
$routes->get('/', function() {
    return redirect()->to('/client/login');
});
$routes->get('client/login', 'ClientController::login');
$routes->post('client/login', 'ClientController::autoLogin');
$routes->get('client/space', 'ClientController::space');
$routes->post('client/transaction', 'ClientController::transaction');
$routes->get('client/logout', 'ClientController::logout');

$routes->get('operateur', 'OperateurController::dashboard');
$routes->post('operateur/prefixe', 'OperateurController::ajouterPrefixe');
