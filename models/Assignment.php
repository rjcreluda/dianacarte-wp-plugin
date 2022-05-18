<?php 
class Assignment
{
	private $post_type;
	private $template_parser;
	public function __construct() 
	{
		global $wpwa_template_loader;
		$this->template_parser = $wpwa_template_loader;
		// Initialization code goes here
		$this->post_type = "assignment";
    	add_action('init', array($this, 'create_post_type'));
	}
	public function create_post_type()
	{
		global $wpwa_custom_post_types_manager;
		$params = array();
		$params['post_type'] = $this->post_type;
		$params['singular_post_name'] = __('Assignment','wpwa');
		$params['plural_post_name'] = __('Assignments','wpwa');
		$params['description'] = __('Assignments','wpwa');
		$params['supported_fields'] = array('title', 'editor');
		$wpwa_custom_post_types_manager->create_post_type($params);
	}
}