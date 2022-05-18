<?php 

/**
 * Creating Admin menu for testing purpose
 */
class TestPage
{
	
	public function __construct()
	{
		add_action( 'admin_menu', array($this, 'add_admin_menu') );
	}

	public function add_admin_menu()
	{
		$id = add_menu_page( "Test page", "Test", "manage_options", "dianacarte", array($this, 'admin_menu_page_content') );
		add_action( 'load-' . $id, array($this, 'process_form_data') );
	}

	public function admin_menu_page_content()
	{
		include('test-page-content.php');
	}

	public function process_form_data()
	{
		//print_r($_POST);
	}
}