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

    public function dashboard() {
        if (!session()->get('is_operator')) {
            return redirect()->to('/operateur/login')->with('error', 'Veuillez vous connecter.');
        }

        $prefixeModel = new PrefixeModel();
        $compteModel = new CompteModel();
        $operationModel = new OperationModel();

        $data['prefixes'] = $prefixeModel->findAll();
        $data['comptes'] = $compteModel->findAll();
        $data['gains']   = $operationModel->getGainsParType();

        return view('operateur/dashboard', $data);
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