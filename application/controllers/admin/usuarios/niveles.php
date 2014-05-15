<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Niveles extends Admin_Controller 
{
	public function __construct()
	{
		parent::__construct();

		$this->load->model($this->constantData['ruta_modelo'], '', TRUE);
		$this->load->model(
			array(
				'usuarios/secciones_model',
				'usuarios/privilegios_model',
				'usuarios/rel_nivel_privsec_model'
				)
			);

		regiter_script(array('catalogos'));
	}

	public function index()
	{
		$modelo = $this->constantData['modelo'];
		$nivel = 'usuarios_niveles';
		/**************************************************************
			Si quisieramos que una seccion tenga un titulo diferente:
			$data['titulo'] = 'Titulo de la seccion;
			$this->constantData['titulo'] = 'Titulo de la pagina';
		**************************************************************/
		$this->constantData['botones_r']['agregar']['text'] = 'Agregar Nivel';
		$this->viewAdmin('comunes/top', $this->constantData);

		if ( $this->_prueba('Ver') ) {
			$this->constantData['gridd']=$this->$modelo->Grid();
			$content['grid'] = $this->viewAdmin('comunes/grid', $this->constantData, TRUE);
			$content['constantData'] = $this->constantData;
			$this->viewAdmin('comunes/contenido',$content);

		} else {
			$this->load->view('admin/error/403');
		}
		$this->viewAdmin('comunes/bottom');
	
	}

	public function _prueba($privilegios)
	{
		$seccion = 'usuarios_'.$this->router->fetch_class();
		$arraySeccionCat = $this->auth->session_data['privileges'];

		if ( in_array($privilegios, $arraySeccionCat[$seccion] ) ) {
			return true;
		}

		return false;
	}

	public function agregar()
	{
		;
		$modelo = $this->constantData['modelo'];
		$nivel = $this->constantData['privilegos']['insertar'];

		if ( !$this->input->is_ajax_request() || !$this->_prueba('Agregar') ) {
			show_error("No no no, así no se puede", 403);
		}

		$row['constant'] = $this->constantData;
		$secciones = $this->secciones_model->getDatosTabla()->result_array();
		$privilegios = $this->privilegios_model->getDatosTabla()->result_array();
		$row['privilegios'] = $privilegios;
		$row['nivelPrivcb'] = $this->_rellenaCkeckbox($secciones,$privilegios,false,null);

		$this->viewAdmin($this->constantData['ruta'].'/add',$row);
	}

	public function editar($id)
	{
		//$this->prueba('Editar');
		$modelo = $this->constantData['modelo'];
		$nivel = $this->constantData['privilegos']['editar'];

		if ( !$this->input->is_ajax_request() || !$this->_prueba('Editar')  ) {
			show_error("No no no, asi no se puede",403);
		}

		$row['constant'] = $this->constantData;
		$secciones = $this->secciones_model->getDatosTabla()->result_array();
		$privilegios = $this->privilegios_model->getDatosTabla()->result_array();



		$row['privilegios'] = $privilegios;
		$arrayselect = $this->getArrayPrivsec($id);
		$row['nivelPrivcb'] = $this->_rellenaCkeckbox($secciones,$privilegios,true, $arrayselect);
		$row['data'] = $this->$modelo->datosInfo($id);

		if($row) {
			$this->viewAdmin($this->constantData['ruta'].'/edit', $row);
		}
	}

	public function getArrayPrivsec($id)
	{
		$array = array();
		$info = $this->rel_nivel_privsec_model->datosInfo($id);

		foreach ($info as $row) {
			$array[$row['upriv_groups_usect_fk']][] = $row['upriv_groups_upriv_fk'];
		}
		//echo '<pre>'.print_r($array,true).'</pre>';
		return $array;
	}

	public function _rellenaCkeckbox($secciones,$privilegios, $editar = false, $arrayselect)
	{
		foreach ($secciones as $seccion) {
			$secId = $seccion['usec_id'];
			$secName = $seccion['usec_name'];
			$tr = "<tr><td>$secName</td>";
			$checkbox="";

			foreach ($privilegios as $privilegio) {
				$checked = "";
				$privId = $privilegio['upriv_id'];
				$privName = $privilegio['upriv_name'];

				if ( ($editar && in_array($privilegio['upriv_id'] , $arrayselect[$secId]) ) || (!$editar) ) {
					$checked = "checked";
				}
				//echo '<pre>'.print_r( $arrayselect[$secId],true).'</pre>';
				//echo '<pre>'.print_r( $privilegio['upriv_id'],true).'</pre>';
				$checkbox .="<td class='text-center'><input type='checkbox' $checked name='nivel[privilegios][$secId][]' value='$privId'></td>";
			}
			$html .= $tr.$checkbox."</tr>";
		}
		return $html;
	}
}
/* End of file listado.php */
/* Location: ./application/controllers/admin/usuarios/listado.php */