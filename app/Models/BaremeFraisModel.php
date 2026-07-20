<?php
namespace App\Models;
use CodeIgniter\Model;

class BaremeFraisModel extends Model {
    protected $table = 'baremes_frais';
    protected $primaryKey = 'id';
    protected $allowedFields = ['id_type_operation', 'montant_min', 'montant_max', 'frais'];

    // Récupérer le frais adapté selon le type d'acte et le montant
    public function getFrais($idTypeOp, $montant) {
        return $this->where('id_type_operation', $idTypeOp)
                    ->where('montant_min <=', $montant)
                    ->where('montant_max >=', $montant)
                    ->first();
    }
}