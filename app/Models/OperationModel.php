<?php
namespace App\Models;
use CodeIgniter\Model;

class OperationModel extends Model {
    protected $table = 'operations';
    protected $primaryKey = 'id';
    protected $allowedFields = ['id_type_operation', 'numero_expediteur', 'numero_destinataire', 'montant', 'frais', 'date_operation'];

    // Côté Opérateur : Somme des gains cumulés par type de transaction
    public function getGainsParType() {
        return $this->select('types_operation.nom, SUM(operations.frais) as total_gains')
                    ->join('types_operation', 'types_operation.id = operations.id_type_operation')
                    ->groupBy('operations.id_type_operation')
                    ->findAll();
    }
}