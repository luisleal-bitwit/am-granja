<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Secciones extends Admin_Controller
{
	public function __construct()
	{
		parent::__construct();

		$this->load->model($this->constantData['ruta_modelo'],'',TRUE);
		regiter_script(array('catalogos'));
	}

	public function index()
	{
		$modelo = $this->constantData['modelo'];
		$nivel = $this->constantData['privilegos']['ver'];
		/**************************************************************
			Si quisieramos que una seccion tenga un titulo diferente:
			$data['titulo'] = 'Titulo de la seccion;
			$this->constantData['titulo'] = 'Titulo de la pagina';
		**************************************************************/
		$this->constantData['botones_r']['agregar']['text'] = 'Agregar Sección';
		$this->viewAdmin('comunes/top', $this->constantData);
		//if ($this->flexi_auth->is_privileged($nivel) ) {

			$this->constantData['gridd'] = $this->$modelo->Grid();
			$content['grid'] = $this->viewAdmin('comunes/grid', $this->constantData, TRUE);
			$content['constantData'] = $this->constantData;
			$this->viewAdmin('comunes/contenido',$content);
		//} else {
		//	$this->load->view('admin/error/403');
		//}
		$this->viewAdmin('comunes/bottom');
	}

	public function agregar()
	{
		$modelo = $this->constantData['modelo'];
		$nivel = $this->constantData['privilegos']['insertar'];
		
		if ( !$this->input->is_ajax_request() /*|| !$this->flexi_auth->is_privileged($nivel)*/ ) {
			show_error("No no no, así no se puede",403);
		}

		$row['constant'] = $this->constantData;
		$row['nivelSelect'] = $this->_rellenaSelect( $this->flexi_auth_model->get_groups()->result_array() );
		$this->viewAdmin($this->constantData['ruta'].'/add',$row);
	}

	public function editar($id)
	{
		$modelo = $this->constantData['modelo'];
		$nivel = $this->constantData['privilegos']['editar'];

		if ( !$this->input->is_ajax_request() /*|| !$this->flexi_auth->is_privileged($nivel)*/ ) {
			show_error("No no no, asi no se puede",403);
		}

		$row['data'] = $this->$modelo->datosInfo($id);
		$row['constant'] = $this->constantData;
		//$row['images'] = $this->images_model->getDatosByRel($id);
		//$row['categoria']['list']=$this->categorias_model->getDatos(true);
		//$row['categoria']['select']=returnFieldByCommas($this->prod_mod->getDatosByRelExtended($id),'categoria_nombre');
		if($row) {
			$this->viewAdmin($this->constantData['ruta'].'/edit', $row);
		}
	}

	public function _rellenaSelect($query,$seleccionado='')
	{
		foreach($query as $row){
			$id		= $row['ugrp_id'];
			$nombre	= $row['ugrp_name'];
			$selected = (($seleccionado!='' && $seleccionado==$id)? 'selected':'');
			$opts .= '<option value="'.$row[ugrp_id].'" '.$selected.'>'.$nombre.'</option>';
		}

		$html = "<select name='usuarios[nivel]' class='span12' data-rule-required='true'><option value=''>Nivel del usuario</option>$opts</select>";

		return $html;
	}
}
/* End of file listado.php */
/* Location: ./application/controllers/admin/usuarios/listado.php */