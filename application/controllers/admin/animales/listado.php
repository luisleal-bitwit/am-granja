<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Listado extends Admin_Controller
{
	public function __construct()
	{
		parent::__construct();
		
		$this->load->model($this->constantData['ruta_modelo'],'',TRUE);
		regiter_css(array('uploadifive','select2','select2-bootstrap'));
		regiter_script(array('jquery.uploadifive','bootbox.min','select2.min','catalogos'));
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
		$this->constantData['titulo'] = 'Listado de Productos';
		$this->constantData['botones_r']['agregar']['text'] = 'Agregar Listado';
		$this->viewAdmin('comunes/top', $this->constantData);
		//if($this->flexi_auth->is_privileged($nivel)){

		$this->constantData['gridd']=$this->$modelo->Grid();
		$content['grid']= $this->viewAdmin('comunes/grid', $this->constantData, TRUE);
		$content['constantData'] = $this->constantData;
		$this->viewAdmin('comunes/contenido',$content);

		/*} else {
			$this->load->view('error/403');
		}*/
		$this->viewAdmin('comunes/bottom', $this->constantData);
	}

	public function agregar()
	{
		$modelo = $this->constantData['modelo'];
		$nivel = $this->constantData['privilegos']['insertar'];

		if ( !$this->input->is_ajax_request() /*|| !$this->flexi_auth->is_privileged($nivel) */) {
			show_error("No no no, así no se puede",403);
    	}

		$row['constant']=$this->constantData;
		//$row['categoria']['list']=$this->categorias_model->getDatos(true);
		$row['getPadreListado'] = $this->cambioDeVista('m');
		$row['getMadreListado'] = $this->cambioDeVista('h');

		$this->viewAdmin($this->constantData['ruta'].'/add',$row);
	}

	public function editar($id)
	{
		$modelo = $this->constantData['modelo'];
		$nivel = $this->constantData['privilegos']['editar'];
		
		if ( !$this->input->is_ajax_request() /*|| !$this->flexi_auth->is_privileged($nivel) */ ) {
			show_error("No no no, asi no se puede",403);
    	}
    	$row['data'] = $this->$modelo->datosInfo($id);
    	$row['getPadreListado'] = $this->cambioDeVista('papa',$row['data']['id']);
		$row['getMadreListado'] = $this->cambioDeVista('mama',$row['data']['id']);

		$row['constant']=$this->constantData;
		//$row['images'] = $this->images_model->getDatosByRel($id);
		//$row['categoria']['list']=$this->categorias_model->getDatos(true);
		//$row['categoria']['select']=returnFieldByCommas($this->prod_mod->getDatosByRelExtended($id),'categoria_nombre');
		if($row){
			$this->viewAdmin($this->constantData['ruta'].'/edit', $row);
		}
	}

	private function _getPadres($query)
	{
		$ancestros = array();
		foreach ($query as $animal) {
			$ancestros[] = $animal['padre'];
			$ancestros[] = $animal['madre'];
		}

		return $ancestros;
	}

	public function ancestros($id=0)
	{
		//$this->output->enable_profiler(TRUE);
		$regs = array();
		$modelo = $this->constantData['modelo'];
		//if ( !$this->input->is_ajax_request() ) {
			//show_error("No no no, asi no se puede",403);
		//}
		$animal = $this->$modelo->datosInfo($id);

		$papa = $this->$modelo->ancestros($animal['padre']);
		$mama = $this->$modelo->ancestros($animal['madre']);

		$idabuelospadre = $this->_getPadres( $this->$modelo->ancestros($animal['padre']) );
		$idabuelosmadre = $this->_getPadres( $this->$modelo->ancestros($animal['madre']) );

		if($idabuelospadre != null || $idabuelosmadre != null) {

			$padredelPadre = $this->$modelo->ancestros($idabuelospadre[0]);
			$madredelPadre = $this->$modelo->ancestros($idabuelospadre[1]);
			$padredelaMadre = $this->$modelo->ancestros($idabuelosmadre[0]);
			$madredelaMadre = $this->$modelo->ancestros($idabuelosmadre[1]);
			$row['thumb'] = "thumbnail bg-gris";
		}

		$row['animal'] = $animal;
		$row['papa'] = $papa;
		$row['mama'] = $mama;
		$row['abuelopaterno'] = $padredelPadre;
		$row['abuelapaterna'] = $madredelPadre;
		$row['abuelomaterno'] = $padredelaMadre;
		$row['abuelamaterna'] = $madredelaMadre;
		$row['constant'] = $this->constantData;

		if($row) {
			$this->viewAdmin($this->constantData['ruta'].'/getancestros', $row);
		}

		return $regs;
	}

	private function _get_JS_CatMar()
	{
		$modelo = $this->constantData['modelo'];
		$query = $this->$modelo->getCategoriasMarcas();
		$json = array();
		foreach($query as $row) {
			$r['id'] = $row['marca_id'];
			$r['nombre'] = $row['marca_nombre'];
			$json[ $row['categoria_nombre'] ][] = $r;
		}

		return base64_encode(json_encode($json));
	}
}