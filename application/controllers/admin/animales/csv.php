<?php

class Csv extends CI_Controller 
{
    function __construct() 
    {
        parent::__construct();
        $this->load->model('animales/csv_model');
        $this->load->library('Csvimport');
    }

    public function index()
    {
        //$data['addressbook'] = $this->csv_model->get_addressbook();
        $this->load->view('admin/animales/listado/csvindex', $data);
    }

    public function importcsv()
    {
        //$data['addressbook'] = $this->csv_model->get_addressbook();
        $data['error'] = '';    //initialize image upload error array to empty
        $getAnimalListado = $this->csv_model->getAnimalListado();

        $config['upload_path'] = './uploads/';
        $config['allowed_types'] = 'csv';
        $config['max_size'] = '1000';
        $this->upload->initialize($config);

        // If upload failed, display error

        if (!$this->upload->do_upload()) {
            $data['error'] = $this->upload->display_errors();
            $this->load->view('admin/animales/listado/csvindex', $data);

        } else {
            $file_data = $this->upload->data();
            $file_path =  './uploads/'.$file_data['file_name'];
            $data['filepath'] = $file_path;

            if ($this->csvimport->get_array($file_path)) {
                $csv_array = $this->csvimport->get_array($file_path);

                //NumRegIndiv
                foreach ($csv_array as $row) {
                    foreach ($getAnimalListado as $key) {
                        if ($row['NumRegIndiv'] != $key['reg']){
                            $insert_data = array(
                                $key['nombre'] => $row['Nombre'],
                                $key['priv'] => $row['NumEco'],
                                $key['birth'] => $row['FDN'],
                                $key['reg'] =>  $row['NumRegIndiv'],
                                $key['padre'] => $row['NumRegPad'],
                                $key['madre'] => $row['NumRegMad'],
                                $key['sexo'] => $row['Sex'],
                                $key['raza'] => $row['Raza'],
                                $key['propio'] => $row['Prop'],
                                $key['activo'] => $row['Activo']
                            );
                        }
                    }
                }
                echo "<pre>"
                $this->csv_model->insertarCsv($insert_data);
                $this->session->set_flashdata('success', 'Csv Data Imported Succesfully');
                //redirect(base_url().'csv');
                //echo "<pre>"; print_r($csv_array);
            } else {
                $data['error'] = "Error occured";
                $this->load->view('admin/animales/listado/csvindex', $data);
            }
        }
    }
}
/*END OF FILE*/