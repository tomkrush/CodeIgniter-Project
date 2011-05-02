<?php

class Articles_Model extends My_Model 
{	
	function init()
	{
		$this->fields('blog_id', 'title', 'contents');
		$this->validates('title', array('presence' => TRUE, 'uniqueness' => TRUE));
		$this->validates('contents', array('presence' => TRUE));
		
		$this->belongs_to('blog');
	}
}