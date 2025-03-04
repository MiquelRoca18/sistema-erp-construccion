<?php
namespace App\Model;
use App\Utils\BaseModel;

class Budget extends BaseModel {

    public function __construct() {
        parent::__construct('presupuestos');
    }

    // Retorna los presupuestos con el nombre del proyecto y la suma total de equipos, mano_obra y materiales
    public function getBudgetsWithDetails() {
        $query = "SELECT 
                    p.presupuestos_id, 
                    p.proyectos_id, 
                    p.equipos, 
                    p.mano_obra, 
                    p.materiales, 
                    (p.equipos + p.mano_obra + p.materiales) AS total,
                    pr.nombre_proyecto
                  FROM presupuestos p
                  INNER JOIN proyectos pr ON p.proyectos_id = pr.proyectos_id";
        $stmt = $this->db->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }
}
?>
