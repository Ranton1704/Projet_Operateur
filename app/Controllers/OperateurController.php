<?php
namespace App\Controllers;
use App\Models\PrefixeModel;
use App\Models\CompteModel;
use App\Models\OperationModel;

class OperateurController extends BaseController {
    
    public function dashboard() {
        $prefixeModel = new PrefixeModel();
        $compteModel = new CompteModel();
        $operationModel = new OperationModel();

        $data['prefixes'] = $prefixeModel->findAll();
        $data['comptes'] = $compteModel->findAll();
        $data['gains']   = $operationModel->getGainsParType();

        return view('operateur/dashboard', $data);
    }

    public function ajouterPrefixe() {
        $prefixeModel = new PrefixeModel();
        $prefixe = $this->request->getPost('prefixe');

        if ($prefixe) {
            $prefixeModel->insert(['prefixe' => $prefixe]);
        }
        return redirect()->to('/operateur');
    }
}