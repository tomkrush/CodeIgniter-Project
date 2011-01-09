<?php

class Pages_Model extends My_Model 
{	
	function init()
	{
		$this->fields('section_id', 'is_public', 'name', 'slug', 'title', 'keywords', 'description', 'content');
		$this->validates('name', array('presence' => TRUE, 'uniqueness' => array('scope'=>'section_id'), 'length' => array('minimum' => 6, 'maximum' => 8)));
		$this->validates('slug', array('presence' => TRUE, 'uniqueness' => array('scope' => 'section_id')));
		
		// $this->before_create('test');
	}
	
	function test($conditions)
	{
		$conditions['slug'] = strtoupper($conditions['slug']);
		
		return $conditions;
	}
}