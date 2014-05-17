<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/*
* Name: Flexi_auth_custom
*
* Author: 
* Luis Leal @ BITWIT
*
* Description: A full login authorisation and user management library for CodeIgniter based on Ion Auth (By Ben Edmunds) which itself was based on Redux Auth 2 (Mathew Davies)
*/
// Load the flexi auth Lite library to allow it to be extended.
load_class('Flexi_auth', 'libraries', FALSE);
class Flexi_auth_custom extends Flexi_auth
{
	public function __construct()
	{
		$CI =& get_instance(); 
		$CI->load->model('flexi_auth_custom_model','flexi_auth_model');
		parent::__construct();
	}

	public function is_privileged($privileges = FALSE)
	{
		$CI =& get_instance(); 
		$seccion = 'usuarios_'.$CI->router->fetch_class();
		$arraySeccionCat = $CI->auth->session_data['privileges'];

		if ( in_array($privilegios, $arraySeccionCat[$seccion] ) ) {
			
			return true;
		}

		return false;
	}
}

/* End of file Flexi_auth_custom.php */
/* Location: ./application/libraries/Flexi_auth_custom.php */