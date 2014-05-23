<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
class Csv extends Admin_Controller
{
    function __construct()
    {
        parent::__construct();

        $this->load->model('animales/listado_model');
        $this->load->library('Csvimport');
        //regiter_css(array('uploadifive','select2','select2-bootstrap'));
        //regiter_script(array('jquery.uploadifive','bootbox.min','select2.min','catalogos'));
    }

    public function index()
    {
        $this->viewAdmin('comunes/top', $this->constantData);
        //if($this->flexi_auth->is_privileged($nivel)){

        //$this->constantData['gridd']=$this->$modelo->Grid();
        //$content['grid']= $this->viewAdmin('comunes/grid', $this->constantData, TRUE);
        $content['constantData'] = $this->constantData;
        //$this->viewAdmin('comunes/contenido',$content);

        $this->load->view('admin/animales/listado/csvindex', $data);
        $this->viewAdmin('comunes/bottom', $this->constantData);
    }

    public function importcsv()
    {
        $data['error'] = ''; //initialize image upload error array to empty
        $getAnimalListadoDb = $this->listado_model->getDatosTabla();
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

                foreach ($getAnimalListadoDb->result_array() as $arrayAnimal) {
                    $numReg[] = $arrayAnimal["reg"];
                }

                $animal =array();
                foreach ($csv_array as $row) {
                    if ( !in_array($row['NumRegIndiv'], $numReg) ) {
                        $nuevoAnimal = array(
                            'id'        => null,
                            'nombre'    => $row['Nombre'],
                            'priv'      => $row['NumEco'],
                            'birth'     => $row['FDN'],
                            'reg'       => $row['NumRegIndiv'],
                            'sexo'      => $row['Sex'],
                            'raza'      => $row['Raza'],
                            'padre'     => $row['NumRegPad'],
                            'madre'     => $row['NumRegMad'],
                            'propio'    => $row['Prop'],
                            'activo'    => $row['Activo']
                        );

                        $animal[] = $nuevoAnimal;
                    }
                }

                $insertar = $this->listado_model->insertarCsv($animal);
                $this->session->set_flashdata('success', 'Csv Data Imported Succesfully');
                if ($insertar){
                     header( 'Location: '.site_url('admin/animales/csv') );
                }
            } else {
                $data['error'] = "Error occured";
                $this->load->view('admin/animales/listado/csvindex', $data);
            }
        } 
    }
}
/*END OF FILE*/