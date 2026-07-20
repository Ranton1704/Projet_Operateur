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

        // Gains des autres opérateurs (transferts externes)
        $gainsAutresOperateurs = $this->select('autres_operateurs.nom as operateur_nom, types_operation.nom as type_nom, 
                                                    SUM(operations.frais) as total_frais, 
                                                    SUM(operations.commission_supplementaire) as total_commission')
                                        ->join('autres_operateurs', 'autres_operateurs.id = operations.id_autre_operateur')
                                        ->join('types_operation', 'types_operation.id = operations.id_type_operation')
                                        ->where('est_autre_operateur', 1)
                                        ->groupBy('operations.id_autre_operateur, operations.id_type_operation')
                                        ->findAll();

        return [
            'operateur' => $gainsOperateur,
            'autres_operateurs' => $gainsAutresOperateurs
        ];
    }

    // Situation des montants à envoyer à chaque opérateur
    public function getMontantsParOperateur() {
        return $this->select('autres_operateurs.nom, autres_operateurs.commission_pourcentage,
                                COUNT(operations.id) as nombre_transferts,
                                SUM(operations.montant) as total_montant,
                                SUM(operations.commission_supplementaire) as total_commission')
                    ->join('autres_operateurs', 'autres_operateurs.id = operations.id_autre_operateur')
                    ->where('est_autre_operateur', 1)
                    ->where('id_type_operation', 3) // Transferts uniquement
                    ->groupBy('operations.id_autre_operateur')
                    ->findAll();
    }
}