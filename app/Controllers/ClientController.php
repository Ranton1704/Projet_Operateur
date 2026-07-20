<?php
namespace App\Controllers;
use App\Models\CompteModel;
use App\Models\PrefixeModel;
use App\Models\BaremeFraisModel;
use App\Models\OperationModel;
use App\Models\PrefixeAutreOperateurModel;

class ClientController extends BaseController {
    
    private function normalizePhone(string $number): string {
        $value = preg_replace('/\s+/', '', trim($number));
        if (substr($value, 0, 4) === '+261') {
            $value = substr($value, 4);
        }
        if (substr($value, 0, 3) === '261') {
            $value = substr($value, 3);
        }
        if ($value !== '' && $value[0] !== '0') {
            $value = '0' . $value;
        }

        return $value;
    }

    private function findCompteByPhone(\App\Models\CompteModel $compteModel, string $phone): ?array {
        $normalizedPhone = $this->normalizePhone($phone);

        $compte = $compteModel->where('numero_telephone', $normalizedPhone)->first();
        if ($compte) {
            return $compte;
        }

        foreach ($compteModel->findAll() as $row) {
            if ($this->normalizePhone((string) $row['numero_telephone']) === $normalizedPhone) {
                return $row;
            }
        }

        return null;
    }

    public function login() {
        return view('client/login');
    }

    public function autoLogin() {
        $session = session();
        $phone = $this->normalizePhone((string) $this->request->getPost('numero_telephone'));
        
        // Extraction du préfixe (ex: les 3 premiers chiffres)
        $subPrefix = substr($phone, 0, 3);
        $prefixeModel = new PrefixeModel();
        
        if (!$prefixeModel->where('prefixe', $subPrefix)->first()) {
            return redirect()->back()->with('error', 'Opérateur non supporté par ce numéro.');
        }

        // Login automatique : création à la volée s'il n'existe pas
        $compteModel = new CompteModel();
        $compte = $this->findCompteByPhone($compteModel, $phone);
        
        if (!$compte) {
            $compteModel->insert(['numero_telephone' => $phone, 'solde' => 0.0]);
            $compte = ['numero_telephone' => $phone, 'solde' => 0.0];
        }

        $session->set('client_phone', $compte['numero_telephone']);
        return redirect()->to('/client/space');
    }

    public function space() {
        $session = session();
        $phone = $session->get('client_phone');
        if (!$phone) return redirect()->to('/client/login');

        $compteModel = new CompteModel();
        $operationModel = new OperationModel();
        $baremeModel = new BaremeFraisModel();

        $data['compte'] = $this->findCompteByPhone($compteModel, $phone);
        
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

        $compteExp = $this->findCompteByPhone($compteModel, $expediteur);
        if (!$compteExp) return redirect()->back()->with('error', 'Compte expediteur introuvable.');
        $expediteur = $compteExp['numero_telephone'];

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
            // Normalisation du numéro destinataire
            $destinataire = $this->normalizePhone((string) $destinataire);

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
            
            $compteDest = $this->findCompteByPhone($compteModel, $destinataire);
            if (!$compteDest) {
                return redirect()->back()->with('error', 'Le numéro destinataire n\'existe pas.');
            }
            $destinataire = $compteDest['numero_telephone'];
            
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

        

        // Accept both legacy textarea or the current recipients array
        $postDest = $this->request->getPost('destinataires');
        $montantTotal = floatval($this->request->getPost('montant_total'));

        $compteModel = new CompteModel();
        $baremeModel = new BaremeFraisModel();
        $operationModel = new OperationModel();
        $prefixeAutreOperateurModel = new PrefixeAutreOperateurModel();

        $compteExp = $this->findCompteByPhone($compteModel, $expediteur);
        if (!$compteExp) return redirect()->back()->with('error', 'Compte expediteur introuvable.');
        $expediteur = $compteExp['numero_telephone'];

        // Construire la liste de destinataires et montants
        $pairs = [];
        if (is_array($postDest)) {
            // Nouvelle interface: destinataires[] + montant total partagé
            $dests = array_values(array_filter(array_map('trim', $postDest)));
            if (!empty($dests) && $montantTotal > 0) {
                $montantParDestinataire = $montantTotal / count($dests);
                foreach ($dests as $dest) {
                    $pairs[] = ['num' => $this->normalizePhone($dest), 'mont' => $montantParDestinataire];
                }
            }
        } else {
            // Ancienne interface: textarea et montant_total réparti
            $destinatairesText = $this->request->getPost('destinataires');
            $dests = array_filter(array_map('trim', explode("\n", $destinatairesText)));
            if (!empty($dests)) {
                $per = $montantTotal / count($dests);
                foreach ($dests as $d) {
                    $pairs[] = ['num' => $this->normalizePhone($d), 'mont' => $per];
                }
            }
        }

        if (empty($pairs)) {
            return redirect()->back()->with('error', 'Veuillez entrer au moins un destinataire valide.');
        }

        $commonPrefix = null;
        foreach ($pairs as $pair) {
            $prefix = substr($pair['num'], 0, 3);
            if ($commonPrefix === null) {
                $commonPrefix = $prefix;
                continue;
            }

            if ($prefix !== $commonPrefix) {
                return redirect()->back()->with('error', 'Tous les numéros du transfert multiple doivent appartenir au même opérateur.');
            }
        }

        $operatorInfo = $prefixeAutreOperateurModel->isAutreOperateur($commonPrefix);
        $commissionPourcentage = $operatorInfo ? floatval($operatorInfo['commission_pourcentage']) : 0.0;

        // Calculer les frais par destinataire séparément
        $fraisParDestinataire = [];
        $totalMontant = 0;
        foreach ($pairs as $p) {
            $totalMontant += $p['mont'];
            $f = $baremeModel->getFrais(3, $p['mont']);
            $fraisBase = $f ? floatval($f['frais']) : 0.0;
            $commission = $commissionPourcentage > 0 ? (($p['mont'] * $commissionPourcentage) / 100) : 0.0;
            $fraisParDestinataire[] = $fraisBase + $commission;
        }
        $totalFrais = array_sum($fraisParDestinataire);
        $totalADebiter = $totalMontant + $totalFrais;

        // Vérifier le solde global
        if ($compteExp['solde'] < $totalADebiter) {
            return redirect()->back()->with('error', 'Solde insuffisant pour le transfert multiple.');
        }

        // Effectuer les transferts en parcourant les paires (num, mont)
        $transfertsReussis = 0;
        $transfertsEchoues = [];
        $currentSolde = $compteExp['solde'];

        foreach ($pairs as $index => $p) {
            $destinataire = $p['num'];
            $montantPar = $p['mont'];

            $compteDest = $this->findCompteByPhone($compteModel, $destinataire);
            if (!$compteDest) {
                $transfertsEchoues[] = "$destinataire (numéro inexistant)";
                continue;
            }
            $destinataire = $compteDest['numero_telephone'];

            // Calcul des frais pour ce montant
            $frais = isset($fraisParDestinataire[$index]) ? $fraisParDestinataire[$index] : 0.0;

            // Débit source et crédit cible (mettre à jour le solde courant)
            $currentSolde -= ($montantPar + $frais);
            $compteModel->update($compteExp['id'], ['solde' => $currentSolde]);

            $compteModel->update($compteDest['id'], ['solde' => $compteDest['solde'] + $montantPar]);

            // Enregistrer l'opération
            $operationData = [
                'id_type_operation'  => 3,
                'numero_expediteur'   => $expediteur,
                'numero_destinataire' => $destinataire,
                'montant'             => $montantPar,
                'frais'               => $frais
            ];

            if ($operatorInfo) {
                $operationData['est_autre_operateur'] = 1;
                $operationData['id_autre_operateur'] = $operatorInfo['id_autre_operateur'];
                $operationData['commission_supplementaire'] = $commission;
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