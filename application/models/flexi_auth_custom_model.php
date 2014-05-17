<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/*
* Name: flexi_auth_custom_model
*
* Author: 
* Luis Leal @ BITWIT
*
* TO-DO:
* Cambiar las funciones de privilegios para "usuarios".
* 
* Description: A full login authorisation and user management library for CodeIgniter based on Ion Auth (By Ben Edmunds) which itself was based on Redux Auth 2 (Mathew Davies)
* Requirements: PHP5 or above and Codeigniter 2.0+
*/

class Flexi_auth_custom_model extends Flexi_auth_model
{
	public function __construct(){}

	/**
	 * Se cambio la linea 36 y 38, en funcion original.
	 * @param  [type] $group_id   [description]
	 * @param  [type] $group_data [description]
	 * @return [type]             [description]
	 */
	public function update_group($group_id, $group_data)
	{
		if (!is_numeric($group_id) || !is_array($group_data)) {
			return FALSE;
		}
		
		$sql_update = array();		
		foreach ($this->auth->database_config['user_group']['columns'] as $key => $column) {
			if (isset($group_data[$column])) {
				$sql_update[$this->auth->tbl_col_user_group[$key]] = $group_data[$column];
				unset($group_data[$column]);
			}
		}

		$update = $this->db->update($this->auth->tbl_user_group, $sql_update, $sql_where);
		
		return ($update ? true:false);
	}

	/**
	 * Se agreagó manualmente la data de secciones de usuario al JOIN
	 * @param  [type] $sql_select [description]
	 * @param  [type] $sql_where  [description]
	 * @return [type]             [description]
	 */
	public function get_user_group_privileges($sql_select, $sql_where)
	{
		// Set any custom defined SQL statements.
		$this->flexi_auth_lite_model->set_custom_sql_to_db($sql_select, $sql_where);
		
		return $this->db->from($this->auth->tbl_user_privilege)
			->join($this->auth->tbl_user_privilege_groups, $this->auth->tbl_col_user_privilege['id'].' = '.$this->auth->tbl_col_user_privilege_groups['privilege_id'])
			->join('user_sections','upriv_groups_usect_fk = usec_id')
			->get();
	}

	/**
	 * Se cambió a partir de la linea 131 en la función original, se cambió lo referente a los priveçilegios por grupos.
	 * @param [type]  $user                   [description]
	 * @param boolean $logged_in_via_password [description]
	 */
	private function set_login_sessions($user, $logged_in_via_password = FALSE)
	{
		if (!$user)
		{
			return FALSE;
		}
		
		$user_id = $user->{$this->auth->database_config['user_acc']['columns']['id']};
		
		// Regenerate CI session_id on successful login.
		$this->regenerate_ci_session_id();
		
		// Update users last login date.
		$this->update_last_login($user_id);
		
		// Set database and login session token if defined by config file.
		if ($this->auth->auth_security['validate_login_onload'] && ! $this->insert_database_login_session($user_id))
		{
			return FALSE;
		}
		
		// Set verified login session if user logged in via Password rather than 'Remember me'.
		$this->auth->session_data[$this->auth->session_name['logged_in_via_password']] = $logged_in_via_password;
		
		// Set user id and identifier data to session.
		$this->auth->session_data[$this->auth->session_name['user_id']] = $user_id;
		$this->auth->session_data[$this->auth->session_name['user_identifier']] = $user->{$this->auth->db_settings['primary_identity_col']};

		// Get group data.
		$sql_where[$this->auth->tbl_col_user_group['id']] = $user->{$this->auth->database_config['user_acc']['columns']['group_id']};
		
		$group = $this->get_groups(FALSE, $sql_where)->row();
		
		// Set admin status to session.
		$this->auth->session_data[$this->auth->session_name['is_admin']] = ($group->{$this->auth->database_config['user_group']['columns']['admin']} == 1);
		
		$this->auth->session_data[$this->auth->session_name['group']] = 
			array($group->{$this->auth->database_config['user_group']['columns']['id']} => $group->{$this->auth->database_config['user_group']['columns']['name']});
		
		###+++++++++++++++++++++++++++++++++###

		$privilege_sources = $this->auth->auth_settings['privilege_sources'];
		$privileges = array();

		// If 'user' privileges have been defined within the config 'privilege_sources'.
        if (in_array('user', $privilege_sources))
        {
            // Get user privileges.
            $sql_select = array(
                $this->auth->tbl_col_user_privilege['id'],
                $this->auth->tbl_col_user_privilege['name']
            );

            $sql_where = array($this->auth->tbl_col_user_privilege_users['user_id'] => $user_id);

            $query = $this->get_user_privileges($sql_select, $sql_where);

            // Create an array of user privileges.
            if ($query->num_rows() > 0)
            {
                foreach($query->result_array() as $data)
                {
                    $privileges[$data[$this->auth->database_config['user_privileges']['columns']['id']]] = $data[$this->auth->database_config['user_privileges']['columns']['name']];
                }
            }
        }
        
		// If 'group' privileges have been defined within the config 'privilege_sources'.
        if (in_array('group', $privilege_sources))
        {
            // Get group privileges.
            $sql_select = array(
                $this->auth->tbl_col_user_privilege['id'],
                $this->auth->tbl_col_user_privilege['name'],
                'usec_name'
            );

            $sql_where = array($this->auth->tbl_col_user_privilege_groups['group_id'] => $user->{$this->auth->database_config['user_acc']['columns']['group_id']});

			$query = $this->get_user_group_privileges($sql_select, $sql_where);

			// Extend array of user privileges by group privileges.
			if ($query->num_rows() > 0)
			{
				foreach($query->result_array() as $data)
				{
					$privileges[$data[$this->auth->database_config['user_privileges']['columns']['id']]] = $data[$this->auth->database_config['user_privileges']['columns']['name']];
				}
			}
		}

		// Set user privileges to session.
		$this->auth->session_data[$this->auth->session_name['privileges']] = $privileges;
		
		###+++++++++++++++++++++++++++++++++###

		$this->session->set_userdata(array($this->auth->session_name['name'] => $this->auth->session_data));

		return TRUE;
	}
}

/* End of file flexi_auth_custom_model.php */
/* Location: ./application/models/flexi_auth_custom_model.php */