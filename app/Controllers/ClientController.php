<?php
namespace App\Controllers;
use App\Models\CompteModel;
use App\Models\PrefixeModel;
use App\Models\BaremeFraisModel;
use App\Models\OperationModel;

class ClientController extends BaseController {
    
    public function login() {
        return view('client/login');
    }

    public function autoLogin() {
        $session = session();
        $phone = $this->request->getPost('numero_telephone');
        
        // Extraction du préfixe (ex: les 3 premiers chiffres)
        $subPrefix = substr($phone, 0, 3);
        $prefixeModel = new PrefixeModel();
        
        if (!$prefixeModel->where('prefixe', $subPrefix)->first()) {
            return redirect()->back()->with('error', 'Opérateur non supporté par ce numéro.');
        }

        // Login automatique : création à la volée s'il n'existe pas
        $compteModel = new CompteModel();
        $compte = $compteModel->where('numero_telephone', $phone)->first();
        
        if (!$compte) {
            $compteModel->insert(['numero_telephone' => $phone, 'solde' => 0.0]);
        }

        $session->set('client_phone', $phone);
        return redirect()->to('/client/space');
    }

    public function space() {
        $session = session();
        $phone = $session->get('client_phone');
        if (!$phone) return redirect()->to('/client/login');

        $compteModel = new CompteModel();
        $operationModel = new OperationModel();

        $data['compte'] = $compteModel->where('numero_telephone', $phone)->first();
        $data['historique'] = $operationModel->where('numero_expediteur', $phone)
                                             ->orWhere('numero_destinataire', $phone)
                                             ->orderBy('date_operation', 'DESC')
                                             ->findAll();

        return view('client/space', $data);
    }

    public function transaction() {
        $session = session();
        $expediteur = $session->get('client_phone');
        if (!$expediteur) return redirect()->to('/client/login');

        $type = $this->request->getPost('type'); // 'depot', 'retrait', 'transfert'
        $montant = floatval($this->request->getPost('montant'));
        $destinataire = $this->request->getPost('destinataire') ?: null;

        $compteModel = new CompteModel();
        $baremeModel = new BaremeFraisModel();
        $operationModel = new OperationModel();

        $compteExp = $compteModel->where('numero_telephone', $expediteur)->first();

        // Mapping ID opération : 1 = depot, 2 = retrait, 3 = transfert
        $typeIds = ['depot' => 1, 'retrait' => 2, 'transfert' => 3];
        $idTypeOp = $typeIds[$type];

        // Calcul des frais
        $fraisRow = $baremeModel->getFrais($idTypeOp, $montant);
        $frais = $fraisRow ? floatval($fraisRow['frais']) : 0.0;

        // Logique métier des transactions
        if ($type === 'depot') {
            $compteModel->update($compteExp['id'], ['solde' => $compteExp['solde'] + $montant]);
        } 
        elseif ($type === 'retrait') {
            if ($compteExp['solde'] < ($montant + $frais)) {
                return redirect()->back()->with('error', 'Solde insuffisant (Frais inclus).');
            }
            $compteModel->update($compteExp['id'], ['solde' => $compteExp['solde'] - ($montant + $frais)]);
        } 
        elseif ($type === 'transfert') {
            if ($compteExp['solde'] < ($montant + $frais)) {
                return redirect()->back()->with('error', 'Solde insuffisant pour le transfert.');
            }
            $compteDest = $compteModel->where('numero_telephone', $destinataire)->first();
            if (!$compteDest) {
                return redirect()->back()->with('error', 'Le numéro destinataire n\'existe pas.');
            }
            
            // Débit source, Crédit cible
            $compteModel->update($compteExp['id'], ['solde' => $compteExp['solde'] - ($montant + $frais)]);
            $compteModel->update($compteDest['id'], ['solde' => $compteDest['solde'] + $montant]);
        }

        // Sauvegarde dans l'historique des transactions
        $operationModel->insert([
            'id_type_operation'  => $idTypeOp,
            'numero_expediteur'   => $expediteur,
            'numero_destinataire' => $destinataire,
            'montant'             => $montant,
            'frais'               => $frais
        ]);

        return redirect()->to('/client/space')->with('success', 'Opération réussie !');
    }

    public function logout() {
        session()->destroy();
        return redirect()->to('/client/login');
    }
}