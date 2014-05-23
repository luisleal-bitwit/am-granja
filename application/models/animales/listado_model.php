<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Listado_Model extends CI_Model
{
	public $fields;
	public $datos;

	function __construct()
	{
		parent::__construct();
		$this->fields = $this->initFields();
	}

	public function initFields()
	{
		$datos = $this->input->post('animal',TRUE);
		$fields = array(
			'database' => array(
					'table_name'=>'animal_listado',
					'primary_key'=>'id',
					'primary_key_post'=> $datos['id'],
					'name_key'=>'nombre',
					'name_key_post'=>$datos['nombre']
				),
			'crud' => array(
					'nombre'		=>ucfirst($datos['nombre']),
					'priv'			=>$datos['priv'],
					'birth'			=>$datos['birth'],
					'reg'			=>ucfirst($datos['reg']),
					'sexo'			=>$datos['sexo'],
					'raza'			=>ucfirst($datos['raza']),
					'padre'			=>$datos['padre'],
					'madre'			=>$datos['madre'],
					'propio'		=>$datos['propio'],
					'activo'		=>$datos['activo']

				),
			'fieldNames' => array(
					'nombre'		=> 'Nombre',
					'priv'	=> 'Numero privado',
					'birth'	=> 'Fecha de nacimiento',
					'reg'	=> 'Numero de Registro',
					'sexo'	=> 'Sexo',
					'raza'	=> 'Raza',
					'padre'	=> 'padre',
					'madre'	=> 'Madre',
					'propio'=> 'Propio',
					'activo'=> 'Activo',
				)
			);

		$this->datos = $datos;
		return $fields;
	}

	public function Grid()
	{
		$this->load->model('grid_model');
		$grid_gen = array(
				'SelectCommand'=>"SELECT id,
										 nombre,
										 priv,
										 birth,
										 reg,
										 sexo,
										 raza,
										 padre,
										 madre,
										 if(propio = 1,'Propio','No Propio') as propio, 
										 if(activo = 1,'Activo','No Activo') as activo 
								FROM animal_listado",
				'campos'=>array(
							array(
								'DataField' => 'id',
								'HeaderText' => 'ID'
							),
							array(
								'DataField' => 'nombre',
								'HeaderText' => 'Nombre'
							),
							array(
								'DataField' => 'priv',
								'HeaderText' => 'NÚMERO PRIVADO'
							),
							array(
								'DataField' => 'birth',
								'HeaderText' => 'FECHA DE NACIMIENTO'
							),
							array(
								'DataField' => 'reg',
								'HeaderText' => 'NÚMERO DE REGISTRO'
							),
							array(
								'DataField' => 'sexo',
								'HeaderText' => 'SEXO'
							),
							array(
								'DataField' => 'raza',
								'HeaderText' => 'RAZA'
							),
							array(
								'DataField' => 'padre',
								'HeaderText' => 'PADRE'
							),
							array(
								'DataField' => 'madre',
								'HeaderText' => 'MADRE'
							),
							array(
								'DataField' => 'propio',
								'HeaderText' => 'PROPIO'
							),
							array(
								'DataField' => 'activo',
								'HeaderText' => 'ACTIVO'
							)
					)
			);

		return $this->grid_model->init($grid_gen);
	}
	
	public function insertar()
	{
		$fields = $this->fields;
		$inserta = $this->db->insert($fields['database']['table_name'], $fields['crud']);
		
		if($inserta){
			$data['form']=$fields['crud'];
			return $data;
		} else {
			return false;
		}
	}
	
	public function editar()
	{
		$fields = $this->fields;
		$db_data = $this->datosInfo($fields['database']['primary_key_post']);
		
		$this->db->where($fields['database']['primary_key'], $fields['database']['primary_key_post']);
		$actualiza = $this->db->update($fields['database']['table_name'], $fields['crud']);

		if($actualiza){
			$data['db']=$db_data;
			$data['form']=$fields['crud'];
			
			return $data;
		} else {
			return false;
		}
		
	}
	
	public function eliminar($id)
	{
		$fields = $this->fields;
		$db_data = $this->datosInfo($id);

		$this->db->where($fields['database']['primary_key'], $id);
		$elimina = $this->db->delete($fields['database']['table_name']);
		
		if($elimina){
			$data['db']=$db_data;
			return $data;
		} else {
			return false;
		}
	}
	
	public function datosInfo($id)
	{
		$fields = $this->fields;
		
		$this->db->where($fields['database']['primary_key'], $id);
		$query = $this->db->get($fields['database']['table_name']);
		
		if($query) {
			return $query->row_array();
		} else {
			return false;
		}
	}

	public function ancestros($regs)
	{
		$this->db->from('animal_listado');
		$this->db->where_in('reg',$regs);
		$query = $this->db->get();

		return $query->result_array();
	}
	
	public function getDatosTabla()
	{
		$fields = $this->fields;
		$query = $this->db->get($fields['database']['table_name']);
		
		if($query){
			return $query;
		} else {
			return false;
		}
	}

	public function getDatosByParam($nombre_campo,$valor="")
	{
		$fields = $this->fields;
		if($valor != ""){
			$this->db->where($nombre_campo,$valor);
		}
		
		$query = $this->db->get($fields['database']['table_name']);

		if($query){
			return $query;
		} else {
			return false;
		}
	}

	public function messages($args = array())
	{
		$fields = $this->fields;
		$msgs = array();
		$msgs['insertar']['error']	= "Hubo un problema al registrar, intente nuevamente.";
		$msgs['editar']['error']	= "Hubo un problema al actualizar, intente nuevamente.";
		$msgs['eliminar']['error']	= "Hubo un problema al eliminar, intente nuevamente.";
		$msgs['insertar']['exito']	= "El Animal: <strong>".$args['form']['color'].$args['form']['year']."</strong> se registró con exito.";
		$msgs['editar']['exito']	= "El Animal actualizó con exito:<br>".formatResultUpdate($args,$fields['fieldNames']);
		$msgs['eliminar']['exito']	= "El Animal se eliminó con exito:<br>".formatResultDelete($args,$fields['fieldNames']);

		return $msgs;
	}

	public function insertarCsv($data) 
	{
		$this->db->insert_batch('animal_listado', $data);
	}
}