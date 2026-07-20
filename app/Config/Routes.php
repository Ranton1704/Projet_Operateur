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
$routes->post('client/transfert-multiple', 'ClientController::transfertMultiple');
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
$routes->get('operateur/autres-operateurs', 'OperateurController::autresOperateurs');
$routes->post('operateur/ajouter-autre-operateur', 'OperateurController::ajouterAutreOperateur');
$routes->post('operateur/modifier-autre-operateur', 'OperateurController::modifierAutreOperateur');
$routes->get('operateur/supprimer-autre-operateur/(:num)', 'OperateurController::supprimerAutreOperateur/$1');
$routes->post('operateur/ajouter-prefixe-autre-operateur', 'OperateurController::ajouterPrefixeAutreOperateur');
$routes->get('operateur/supprimer-prefixe-autre-operateur/(:num)', 'OperateurController::supprimerPrefixeAutreOperateur/$1');
$routes->get('operateur/gains-separes', 'OperateurController::gainsSepares');
$routes->get('operateur/situation-operateurs', 'OperateurController::situationOperateurs');