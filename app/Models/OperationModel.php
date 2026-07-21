<?php
namespace App\Models;
use CodeIgniter\Model;

class OperationModel extends Model {
    protected $table = 'operations';
    protected $primaryKey = 'id';
    protected $allowedFields = ['id_type_operation', 'numero_expediteur', 'numero_destinataire', 'montant', 'frais', 'est_autre_operateur', 'id_autre_operateur', 'commission_supplementaire', 'date_operation'];

    // Côté Opérateur : Somme des gains cumulés par type de transaction
    public function getGainsParType() {
        return $this->select('types_operation.nom, SUM(operations.frais) as total_gains')
                    ->join('types_operation', 'types_operation.id = operations.id_type_operation')
                    ->groupBy('operations.id_type_operation')
                    ->findAll();
    }

    // Gains séparés : opérateur vs autres opérateurs
    public function getGainsSepares() {
        // Gains de l'opérateur principal (transferts internes)
        $gainsOperateur = $this->select('types_operation.nom, SUM(operations.frais) as total_gains')
                                ->join('types_operation', 'types_operation.id = operations.id_type_operation')
                                ->where('est_autre_operateur', 0)
                                ->groupBy('operations.id_type_operation')
                                ->findAll();

        // Gains des autres opérateurs (détection par id_autre_operateur ou par préfixe)
        $operationsExternes = $this->select('operations.id_autre_operateur, operations.frais, operations.commission_supplementaire, types_operation.nom as type_nom,
                                            COALESCE(operations.id_autre_operateur, pa.id_autre_operateur) as operateur_id,
                                            COALESCE(ao_direct.nom, ao_prefixe.nom) as operateur_nom')
                                    ->join('types_operation', 'types_operation.id = operations.id_type_operation')
                                    ->join('prefixes p', "p.prefixe = substr(replace(operations.numero_destinataire, ' ', ''), 1, 3)", 'left', false)
                                    ->join('prefixes_autres_operateurs pa', 'pa.id_prefixe = p.id', 'left')
                                    ->join('autres_operateurs ao_direct', 'ao_direct.id = operations.id_autre_operateur', 'left')
                                    ->join('autres_operateurs ao_prefixe', 'ao_prefixe.id = pa.id_autre_operateur', 'left')
                                    ->groupStart()
                                        ->where('operations.est_autre_operateur', 1)
                                        ->orWhere('operations.id_autre_operateur IS NOT NULL', null, false)
                                        ->orWhere('pa.id_autre_operateur IS NOT NULL', null, false)
                                    ->groupEnd()
                                    ->findAll();

        $gainsAutresOperateursMap = [];
        foreach ($operationsExternes as $op) {
            $operateurId = $op['operateur_id'] ?? null;
            $operateurNom = $op['operateur_nom'] ?? null;

            if (!$operateurId || !$operateurNom) {
                continue;
            }

            $key = $operateurId . '|' . $op['type_nom'];
            if (!isset($gainsAutresOperateursMap[$key])) {
                $gainsAutresOperateursMap[$key] = [
                    'operateur_nom' => $operateurNom,
                    'type_nom' => $op['type_nom'],
                    'total_frais' => 0,
                    'total_commission' => 0,
                ];
            }

            $gainsAutresOperateursMap[$key]['total_frais'] += floatval($op['frais']);
            $gainsAutresOperateursMap[$key]['total_commission'] += floatval($op['commission_supplementaire']);
        }

        return [
            'operateur' => $gainsOperateur,
            'autres_operateurs' => array_values($gainsAutresOperateursMap)
        ];
    }

    // Situation des montants à envoyer à chaque opérateur
    public function getMontantsParOperateur() {
        $operations = $this->select('operations.id_autre_operateur, operations.numero_destinataire, operations.montant, operations.commission_supplementaire,
                                    COALESCE(operations.id_autre_operateur, pa.id_autre_operateur) as operateur_id,
                                    COALESCE(ao_direct.nom, ao_prefixe.nom) as nom,
                                    COALESCE(ao_direct.commission_pourcentage, ao_prefixe.commission_pourcentage) as commission_pourcentage')
                            ->join('prefixes p', "p.prefixe = substr(replace(operations.numero_destinataire, ' ', ''), 1, 3)", 'left', false)
                            ->join('prefixes_autres_operateurs pa', 'pa.id_prefixe = p.id', 'left')
                            ->join('autres_operateurs ao_direct', 'ao_direct.id = operations.id_autre_operateur', 'left')
                            ->join('autres_operateurs ao_prefixe', 'ao_prefixe.id = pa.id_autre_operateur', 'left')
                            ->where('operations.id_type_operation', 3)
                            ->groupStart()
                                ->where('operations.est_autre_operateur', 1)
                                ->orWhere('operations.id_autre_operateur IS NOT NULL', null, false)
                                ->orWhere('pa.id_autre_operateur IS NOT NULL', null, false)
                            ->groupEnd()
                            ->findAll();

        $grouped = [];
        foreach ($operations as $op) {
            if (empty($op['operateur_id']) || empty($op['nom'])) {
                continue;
            }

            $key = $op['operateur_id'];
            if (!isset($grouped[$key])) {
                $grouped[$key] = [
                    'nom' => $op['nom'],
                    'commission_pourcentage' => floatval($op['commission_pourcentage']),
                    'nombre_transferts' => 0,
                    'total_montant' => 0,
                    'total_commission' => 0,
                ];
            }

            $grouped[$key]['nombre_transferts']++;
            $grouped[$key]['total_montant'] += floatval($op['montant']);
            $grouped[$key]['total_commission'] += floatval($op['commission_supplementaire']);
        }

        return array_values($grouped);
    }
}