<?php
namespace App\Controllers;
use App\Models\PrefixeModel;
use App\Models\CompteModel;
use App\Models\OperationModel;

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
    $baremeModel = new \App\Models\BaremeFraisModel();

    $data['prefixes'] = $prefixeModel->findAll();
    $data['comptes']  = $compteModel->findAll();
    $data['gains']    = $operationModel->getGainsParType();
    
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

    return view('operateur/dashboard', $data);
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
}