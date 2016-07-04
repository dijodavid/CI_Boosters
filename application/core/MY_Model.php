<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
 * Extending core model
 * @author Dijo David
 * 
 */

class MY_Model extends CI_Model {
	
 	protected $table = '';
 	protected $primary_key = 'id';

    function __construct()
    {
        parent::__construct();
    }
	
	/**
	 * Insert record to table
	 */
	function add( $data )
	{
		if( $data )
		{
			return ($this->db->insert($this->table, $data)) ? $this->db->insert_id() : FALSE;
		}
		return FALSE;
	}
	
	/**
	 * Insert multiple records to table
	 */
	function add_batch( $data )
	{
		if( $data )
		{
			return $this->db->insert_batch($this->table, $data);
		}
		return FALSE;
	}
	
	/**
	 * Update a record based on ID passed
	 */
	function update($data = array(), $id = false)
	{
		if( $data )
		{
			//collect id and unset it from the array to update
			$id = ($id) ? $id : $data[$this->primary_key];
			unset( $data[$this->primary_key] );
			
			return $this->db->update($this->table, $data, array($this->primary_key => $id));
		}
		return FALSE;
	}
	
	/**
	 * Update a record based on column passed
	 */
	function update_by_column($data = array(), $col = array())
	{
		if( $data && $col )
		{
			$col_name = key($col);
			$col_value = $col[$col_name];

			$this->db->where($col_name, $col_value);
			return $this->db->update($this->table, $data);
		}
		return FALSE;
	}

	/**
	 * Delete a record from table
	 */
	function delete( $id )
	{
		if( $id )
		{
			return $this->db->delete($this->table, array($this->primary_key => $id));
		}
		return FALSE;
	}
	
	/**
	 * Get all records from the table
	 */
	function get_all($order_col=array())
	{
		if($order_col) {
			foreach ( $order_col as $key => $value ) {
				$this->db->order_by($key, $value);
			}
		}
		$this->db->order_by($this->primary_key,'desc');
		
		return $this->db->get($this->table);
	}
        
	/**
	 * Get a record by ID
	 */
	function get_by_id( $id )
	{
		return ($id) ? $this->db->get_where($this->table, array($this->primary_key => $id)) : FALSE;
	}
    
	/**
	 * Get result based on table columns
	 */
	function get_by_column( $cols = array(), $order_col=array(), $limit = 0, $start = 0 )
	{
		if($order_col) {
			foreach ( $order_col as $key => $value ) {
				$this->db->order_by($key, $value);
			}
		}
		$this->db->order_by($this->primary_key,'desc');

		if($limit)
		{
			$this->db->limit($limit, $start);
		}
		if($cols)
		{
			$this->db->where($cols);
		}
		
		$query = $this->db->get($this->table);
		return $query;
	}
	
	/**
	 * Total record count
	 */
	function record_count() 
	{
    	return $this->db->count_all($this->table);
    }
	
	/**
	 * Get a result based on limit and start for pagination
	 * 
	 * @param $limit - result limit
	 * @param $start - start record
	 * @return object - query object
	 */
	function get_result_set($limit, $start, $where = array()) 
	{
        $this->db->limit($limit, $start);

        if($where) {
			$this->db->where($where);
		}
		
        $query = $this->db->get($this->table);
        return $query;
   	}
	
	/**
	 * Get the result count
	 * 
	 * @param $cols optional - where conditions
	 * @return count int
	 */
	function get_count($cols = array())
	{
		if($cols)
		{
			$this->db->where($cols);
		}
		return $this->db->count_all_results($this->table);
	}


	function _encrypt($str) {
        
        $this->load->library('encrypt');

        $base64 = $this->encrypt->encode($str);
        $urisafe = strtr($base64, '+/=', '-_,');
        return $urisafe;
    }

    function _decrypt($str) {

        $this->load->library('encrypt');

        $base64 = strtr($str, '-_,', '+/=');
        return $this->encrypt->decode($base64);
    }

	//get config values easily
	function get_config($key) {
		return $this->config->item($key);
	}

	function get_ipaddress() {
		return $this->input->ip_address();
	}

	//get datetime to insert to db
	function get_datetime($date = NULL) {
		return ($date) ? date("Y-m-d H:i:s", strtotime($date)) : date("Y-m-d H:i:s");
	}


	//experimental chaining method

	function get($cols = array()) 
	{
		if($cols) {
			$this->db->select(implode(',',$cols));
		}
       	return $this;
   	}

   	function filter($conditions = array()) 
   	{
   		if (isset($conditions['like'])) {
        	foreach ($conditions as $condition) {
        	    $this->db->like($condition);
    	    }    
        }

        if (isset($conditions['or_like'])) {
        	foreach ($conditions as $condition) {
        	    $this->db->or_like($condition);
    	    }    
        }

   		if(isset($conditions['where'])) {
			foreach ($conditions as $condition) {
        	    $this->db->or_like($condition);
    	    }
		}

		return $this;
   	}

   	function sort($order = array()) {
		if($order) {
			foreach ($order as $col => $dir) {
				$this->db->order_by($col, $dir);
			}
		}
		return $this;
	}

	function group($limit, $start) {
		$this->db->limit($limit, $start);
		return $this;
	}

	function data()
	{
		return $this->db->get($this->table);
	}

	function count()
	{
		return $this->db->count_all_results($this->table);
	}

}