<?php
namespace App\Controllers;
use App\Models\PrefixeModel;
use App\Models\CompteModel;
use App\Models\OperationModel;
use App\Models\AutreOperateurModel;
use App\Models\PrefixeAutreOperateurModel;

class OperateurController extends BaseController {
    
    public function login() {
        if (session()->get('is_operator')) {
            return redirect()->to('/operateur');
        }
        return view('operateur/login');
    }

    public function authentifier() {
        $username = $this->request->getPost('username');
        $password = $this->request->getPost('password');

        // Vérification simple lors de la soumission
        if ($username === 'admin' && $password === 'admin123') {
            session()->set('is_operator', true);
            return redirect()->to('/operateur');
        }

        return redirect()->back()->with('error', 'Identifiants opérateur incorrects.');
    }

    public function dashboard()
{
    if (!session()->get('is_operator')) {
        return redirect()->to('/operateur/login')->with('error', 'Veuillez vous connecter.');
    }

    $prefixeModel = new PrefixeModel();
    $compteModel = new CompteModel();
    $operationModel = new OperationModel();

    $data['prefixes'] = $prefixeModel->findAll();
    $data['comptes']  = $compteModel->findAll();
    $data['gains']    = $operationModel->getGainsParType();

    return view('operateur/dashboard', $data);
}

public function prefixes()
{
    if (!session()->get('is_operator')) {
        return redirect()->to('/operateur/login')->with('error', 'Veuillez vous connecter.');
    }

    $prefixeModel = new PrefixeModel();
    $data['prefixes'] = $prefixeModel->findAll();

    return view('operateur/prefixes', $data);
}

public function gains()
{
    if (!session()->get('is_operator')) {
        return redirect()->to('/operateur/login')->with('error', 'Veuillez vous connecter.');
    }

    $operationModel = new OperationModel();
    $data['gains'] = $operationModel->getGainsParType();

    return view('operateur/gains', $data);
}

public function comptes()
{
    if (!session()->get('is_operator')) {
        return redirect()->to('/operateur/login')->with('error', 'Veuillez vous connecter.');
    }

    $compteModel = new CompteModel();
    $data['comptes'] = $compteModel->findAll();

    return view('operateur/comptes', $data);
}

public function frais()
{
    if (!session()->get('is_operator')) {
        return redirect()->to('/operateur/login')->with('error', 'Veuillez vous connecter.');
    }

    $baremeModel = new \App\Models\BaremeFraisModel();

    // Barème des Retraits (ID = 2)
    $data['baremes_retrait'] = $baremeModel->select('baremes_frais.*, types_operation.nom as type_nom')
                                           ->join('types_operation', 'types_operation.id = baremes_frais.id_type_operation')
                                           ->where('id_type_operation', 2)
                                           ->orderBy('montant_min', 'ASC')
                                           ->findAll();

    // Barème des Transferts (ID = 3)
    $data['baremes_transfert'] = $baremeModel->select('baremes_frais.*, types_operation.nom as type_nom')
                                             ->join('types_operation', 'types_operation.id = baremes_frais.id_type_operation')
                                             ->where('id_type_operation', 3)
                                             ->orderBy('montant_min', 'ASC')
                                             ->findAll();

    return view('operateur/frais', $data);
}

// CRUD : Ajouter ou Modifier une tranche
public function enregistrerFrais() {
    if (!session()->get('is_operator')) return redirect()->to('/operateur/login');

    $baremeModel = new \App\Models\BaremeFraisModel();
    $id = $this->request->getPost('id'); // Présent uniquement en cas de modification

    $data = [
        'id_type_operation' => $this->request->getPost('id_type_operation'),
        'montant_min'       => $this->request->getPost('montant_min'),
        'montant_max'       => $this->request->getPost('montant_max'),
        'frais'             => $this->request->getPost('frais'),
    ];

    if ($id) {
        $baremeModel->update($id, $data);
        $message = "Tranche de frais modifiée avec succès !";
    } else {
        $baremeModel->insert($data);
        $message = "Nouvelle tranche de frais ajoutée !";
    }

    return redirect()->to('/operateur')->with('success', $message);
}

// CRUD : Supprimer une tranche
public function supprimerFrais($id) {
    if (!session()->get('is_operator')) return redirect()->to('/operateur/login');

    $baremeModel = new \App\Models\BaremeFraisModel();
    $baremeModel->delete($id);

    return redirect()->to('/operateur')->with('success', "Tranche de frais supprimée.");
}

    public function ajouterPrefixe() {
        if (!session()->get('is_operator')) return redirect()->to('/operateur/login');

        $prefixeModel = new PrefixeModel();
        $prefixe = $this->request->getPost('prefixe');

        if ($prefixe) {
            $prefixeModel->insert(['prefixe' => $prefixe]);
        }
        return redirect()->to('/operateur');
    }

    public function logout() {
        session()->remove('is_operator');
        return redirect()->to('/operateur/login');
    }

    // Gestion des autres opérateurs
    public function autresOperateurs() {
        if (!session()->get('is_operator')) {
            return redirect()->to('/operateur/login')->with('error', 'Veuillez vous connecter.');
        }

        $autreOperateurModel = new AutreOperateurModel();
        $prefixeAutreOperateurModel = new PrefixeAutreOperateurModel();
        
        $data['operateurs'] = $autreOperateurModel->findAll();
        
        // Récupérer les préfixes pour chaque opérateur
        foreach ($data['operateurs'] as &$operateur) {
            $operateur['prefixes'] = $prefixeAutreOperateurModel->getPrefixesByOperateur($operateur['id']);
        }

        return view('operateur/autres_operateurs', $data);
    }

    public function ajouterAutreOperateur() {
        if (!session()->get('is_operator')) return redirect()->to('/operateur/login');

        $autreOperateurModel = new AutreOperateurModel();
        $nom = $this->request->getPost('nom');
        $commission = $this->request->getPost('commission_pourcentage');

        if ($nom && $commission !== null) {
            $autreOperateurModel->insert([
                'nom' => $nom,
                'commission_pourcentage' => floatval($commission)
            ]);
        }
        return redirect()->to('/operateur/autres-operateurs')->with('success', 'Opérateur ajouté avec succès !');
    }

    public function modifierAutreOperateur($id) {
        if (!session()->get('is_operator')) return redirect()->to('/operateur/login');

        $autreOperateurModel = new AutreOperateurModel();
        $nom = $this->request->getPost('nom');
        $commission = $this->request->getPost('commission_pourcentage');

        if ($nom && $commission !== null) {
            $autreOperateurModel->update($id, [
                'nom' => $nom,
                'commission_pourcentage' => floatval($commission)
            ]);
        }
        return redirect()->to('/operateur/autres-operateurs')->with('success', 'Opérateur modifié avec succès !');
    }

    public function supprimerAutreOperateur($id) {
        if (!session()->get('is_operator')) return redirect()->to('/operateur/login');

        $autreOperateurModel = new AutreOperateurModel();
        $autreOperateurModel->delete($id);

        return redirect()->to('/operateur/autres-operateurs')->with('success', 'Opérateur supprimé.');
    }

    public function ajouterPrefixeAutreOperateur() {
        if (!session()->get('is_operator')) return redirect()->to('/operateur/login');

        $prefixeAutreOperateurModel = new PrefixeAutreOperateurModel();
        $idOperateur = $this->request->getPost('id_autre_operateur');
        $prefixe = $this->request->getPost('prefixe');

        if ($idOperateur && $prefixe) {
            $prefixeAutreOperateurModel->insert([
                'id_autre_operateur' => $idOperateur,
                'prefixe' => $prefixe
            ]);
        }
        return redirect()->to('/operateur/autres-operateurs')->with('success', 'Préfixe ajouté avec succès !');
    }

    public function supprimerPrefixeAutreOperateur($id) {
        if (!session()->get('is_operator')) return redirect()->to('/operateur/login');

        $prefixeAutreOperateurModel = new PrefixeAutreOperateurModel();
        $prefixeAutreOperateurModel->delete($id);

        return redirect()->to('/operateur/autres-operateurs')->with('success', 'Préfixe supprimé.');
    }

    // Page des gains séparés
    public function gainsSepares() {
        if (!session()->get('is_operator')) {
            return redirect()->to('/operateur/login')->with('error', 'Veuillez vous connecter.');
        }

        $operationModel = new OperationModel();
        $data['gains'] = $operationModel->getGainsSepares();

        return view('operateur/gains_separes', $data);
    }

    // Situation des montants à envoyer aux opérateurs
    public function situationOperateurs() {
        if (!session()->get('is_operator')) {
            return redirect()->to('/operateur/login')->with('error', 'Veuillez vous connecter.');
        }

        $operationModel = new OperationModel();
        $data['montants'] = $operationModel->getMontantsParOperateur();

        return view('operateur/situation_operateurs', $data);
    }
}