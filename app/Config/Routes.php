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
$routes->get('operateur/login', 'OperateurController::login');
$routes->post('operateur/login', 'OperateurController::authentifier');
$routes->get('operateur/logout', 'OperateurController::logout');
$routes->get('operateur', 'OperateurController::dashboard');
$routes->get('operateur/prefixes', 'OperateurController::prefixes');
$routes->get('operateur/gains', 'OperateurController::gains');
$routes->get('operateur/comptes', 'OperateurController::comptes');
$routes->get('operateur/frais', 'OperateurController::frais');
$routes->post('operateur/prefixe', 'OperateurController::ajouterPrefixe');
$routes->post('operateur/frais/enregistrer', 'OperateurController::enregistrerFrais');
$routes->get('operateur/frais/supprimer/(:num)', 'OperateurController::supprimerFrais/$1');