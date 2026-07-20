<?php
namespace App\Controllers;
use App\Models\CompteModel;
use App\Models\PrefixeModel;
use App\Models\BaremeFraisModel;
use App\Models\OperationModel;
use App\Models\PrefixeAutreOperateurModel;

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
        $baremeModel = new BaremeFraisModel();

        $data['compte'] = $compteModel->where('numero_telephone', $phone)->first();
        
        // Récupération des filtres
        $dateDebut = $this->request->getGet('date_debut');
        $dateFin = $this->request->getGet('date_fin');
        $typeOperation = $this->request->getGet('type_operation');

        // Construction de la requête avec filtres
        $query = $operationModel->where('numero_expediteur', $phone)
                                ->orWhere('numero_destinataire', $phone);
        
        // Filtre par date de début
        if ($dateDebut) {
            $query = $query->where('date_operation >=', $dateDebut);
        }
        
        // Filtre par date de fin
        if ($dateFin) {
            $query = $query->where('date_operation <=', $dateFin . ' 23:59:59');
        }
        
        // Filtre par type d'opération
        if ($typeOperation) {
            $query = $query->where('id_type_operation', $typeOperation);
        }

        $data['historique'] = $query->orderBy('date_operation', 'DESC')->findAll();

        // Récupération des barèmes de frais pour le calcul en temps réel
        $data['baremes_retrait'] = $baremeModel->where('id_type_operation', 2)->findAll();
        $data['baremes_transfert'] = $baremeModel->where('id_type_operation', 3)->findAll();

        return view('client/space', $data);
    }

    public function transaction() {
        $session = session();
        $expediteur = $session->get('client_phone');
        if (!$expediteur) return redirect()->to('/client/login');

        $type = $this->request->getPost('type'); // 'depot', 'retrait', 'transfert'
        $montant = floatval($this->request->getPost('montant'));
        $destinataire = $this->request->getPost('destinataire') ?: null;
        $inclureFraisRetrait = $this->request->getPost('inclure_frais_retrait') === '1';

        $compteModel = new CompteModel();
        $baremeModel = new BaremeFraisModel();
        $operationModel = new OperationModel();
        $prefixeAutreOperateurModel = new PrefixeAutreOperateurModel();

        $compteExp = $compteModel->where('numero_telephone', $expediteur)->first();

        // Mapping ID opération : 1 = depot, 2 = retrait, 3 = transfert
        $typeIds = ['depot' => 1, 'retrait' => 2, 'transfert' => 3];
        $idTypeOp = $typeIds[$type];

        // Calcul des frais
        $fraisRow = $baremeModel->getFrais($idTypeOp, $montant);
        $frais = $fraisRow ? floatval($fraisRow['frais']) : 0.0;

        // Variables pour transfert vers autre opérateur
        $estAutreOperateur = 0;
        $idAutreOperateur = null;
        $commissionSupplementaire = 0.0;

        // Logique métier des transactions
        if ($type === 'depot') {
            $compteModel->update($compteExp['id'], ['solde' => $compteExp['solde'] + $montant]);
        } 
        elseif ($type === 'retrait') {
            $totalADebiter = $inclureFraisRetrait ? ($montant + $frais) : $montant;
            
            if ($compteExp['solde'] < $totalADebiter) {
                return redirect()->back()->with('error', 'Solde insuffisant.');
            }
            
            $montantFinal = $inclureFraisRetrait ? $montant : ($montant - $frais);
            $compteModel->update($compteExp['id'], ['solde' => $compteExp['solde'] - $totalADebiter]);
        } 
        elseif ($type === 'transfert') {
            // Vérifier si le destinataire appartient à un autre opérateur
            $prefixeDest = substr($destinataire, 0, 3);
            $autreOperateur = $prefixeAutreOperateurModel->isAutreOperateur($prefixeDest);
            
            if ($autreOperateur) {
                $estAutreOperateur = 1;
                $idAutreOperateur = $autreOperateur['id_autre_operateur'];
                // Calcul de la commission supplémentaire en pourcentage
                $commissionSupplementaire = ($montant * $autreOperateur['commission_pourcentage']) / 100;
                $frais += $commissionSupplementaire;
            }

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
        $operationData = [
            'id_type_operation'  => $idTypeOp,
            'numero_expediteur'   => $expediteur,
            'numero_destinataire' => $destinataire,
            'montant'             => $montant,
            'frais'               => $frais
        ];

        // Ajouter les champs pour autre opérateur si applicable
        if ($estAutreOperateur) {
            $operationData['est_autre_operateur'] = $estAutreOperateur;
            $operationData['id_autre_operateur'] = $idAutreOperateur;
            $operationData['commission_supplementaire'] = $commissionSupplementaire;
        }

        $operationModel->insert($operationData);

        return redirect()->to('/client/space')->with('success', 'Opération réussie !');
    }

    public function transfertMultiple() {
        $session = session();
        $expediteur = $session->get('client_phone');
        if (!$expediteur) return redirect()->to('/client/login');

        $destinatairesText = $this->request->getPost('destinataires');
        $montantTotal = floatval($this->request->getPost('montant_total'));

        $compteModel = new CompteModel();
        $baremeModel = new BaremeFraisModel();
        $operationModel = new OperationModel();
        $prefixeAutreOperateurModel = new PrefixeAutreOperateurModel();

        $compteExp = $compteModel->where('numero_telephone', $expediteur)->first();

        // Parser les destinataires
        $destinataires = array_filter(array_map('trim', explode("\n", $destinatairesText)));
        
        if (empty($destinataires)) {
            return redirect()->back()->with('error', 'Veuillez entrer au moins un destinataire.');
        }

        $montantParDestinataire = $montantTotal / count($destinataires);
        
        // Calculer les frais totaux
        $fraisRow = $baremeModel->getFrais(3, $montantParDestinataire);
        $fraisParDestinataire = $fraisRow ? floatval($fraisRow['frais']) : 0.0;
        $totalFrais = $fraisParDestinataire * count($destinataires);
        $totalADebiter = $montantTotal + $totalFrais;

        // Vérifier le solde
        if ($compteExp['solde'] < $totalADebiter) {
            return redirect()->back()->with('error', 'Solde insuffisant pour le transfert multiple.');
        }

        // Effectuer les transferts
        $transfertsReussis = 0;
        $transfertsEchoues = [];

        foreach ($destinataires as $destinataire) {
            $compteDest = $compteModel->where('numero_telephone', $destinataire)->first();
            
            if (!$compteDest) {
                $transfertsEchoues[] = "$destinataire (numéro inexistant)";
                continue;
            }

            // Vérifier si autre opérateur
            $prefixeDest = substr($destinataire, 0, 3);
            $autreOperateur = $prefixeAutreOperateurModel->isAutreOperateur($prefixeDest);
            
            $estAutreOperateur = 0;
            $idAutreOperateur = null;
            $commissionSupplementaire = 0.0;
            $frais = $fraisParDestinataire;

            if ($autreOperateur) {
                $estAutreOperateur = 1;
                $idAutreOperateur = $autreOperateur['id_autre_operateur'];
                $commissionSupplementaire = ($montantParDestinataire * $autreOperateur['commission_pourcentage']) / 100;
                $frais += $commissionSupplementaire;
            }

            // Débiter et créditer
            $compteModel->update($compteExp['id'], ['solde' => $compteExp['solde'] - ($montantParDestinataire + $frais)]);
            $compteModel->update($compteDest['id'], ['solde' => $compteDest['solde'] + $montantParDestinataire]);

            // Enregistrer l'opération
            $operationData = [
                'id_type_operation'  => 3,
                'numero_expediteur'   => $expediteur,
                'numero_destinataire' => $destinataire,
                'montant'             => $montantParDestinataire,
                'frais'               => $frais
            ];

            if ($estAutreOperateur) {
                $operationData['est_autre_operateur'] = $estAutreOperateur;
                $operationData['id_autre_operateur'] = $idAutreOperateur;
                $operationData['commission_supplementaire'] = $commissionSupplementaire;
            }

            $operationModel->insert($operationData);
            $transfertsReussis++;
        }

        // Message de résultat
        $message = "Transfert multiple terminé. $transfertsReussis transfert(s) réussi(s).";
        if (!empty($transfertsEchoues)) {
            $message .= " Échecs: " . implode(', ', $transfertsEchoues);
        }

        return redirect()->to('/client/space')->with('success', $message);
    }

    public function logout() {
        session()->destroy();
        return redirect()->to('/client/login');
    }
}