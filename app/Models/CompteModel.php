<?php
namespace App\Models;
use CodeIgniter\Model;

class CompteModel extends Model {
    protected $table = 'comptes';
    protected $primaryKey = 'id';
    protected $allowedFields = ['numero_telephone', 'solde'];
}