<?php
 
class Csv_model extends CI_Model {
 
    function __construct() {
        parent::__construct();
 
    }
 
    function getAnimalListado() {     
        $query = $this->db->get('animal_listado');
        if ($query->num_rows() > 0) {
            return $query->result_array();
        } else {
            return FALSE;
        }
    }
 
    function insertarCsv($data) {
        $this->db->insert_batch('animal_listado', $data);
    }
}
/*END OF FILE*/