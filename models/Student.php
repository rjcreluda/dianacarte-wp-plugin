<?php 
class Student extends WP_User
{
	// method to get assignments for this Student
	function getAssignments()
	{
		if( !isset($this->data->assignments) ){
			$this->data->assignments = get_posts( array(
				'post_type' => 'assignment',// assignments
                'numberposts' => -1,        // all posts
                'author' => $this->ID       // user ID for this Student
			) );
		}
		return $this->data->assignments;
	}

	// magic method to detect $student->assignments
    function __get( $key ) {
        if ( $key == 'assignments' )
        {
            return $this->getAssignments();
        }
        else
        {
            // fallback to default WP_User magic method
            return parent::__get( $key );
        }
    }
} 