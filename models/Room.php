<?php
/*
 * Model for interacting with Room list
 */

class Room
{
	//constructor can take a $post_id
    function __construct( $room = NULL ) 
    {
        if ( !empty( $room ) && is_object($room) )
            $this->getPost( $room );
        if( !empty($room) && is_int($room) ){
            $post = get_post( $room );
            $this->getPost( $post );
        }
    }

    //get the associated post and prepopulate some properties
    private function getPost( $room ) 
    {
        //get post
        $this->post = $room;
        //set some properties for easy access
        if ( !empty( $this->post ) ) {
            $this->id = $this->post->ID;
            $this->name = $this->post->post_title;
            $this->description = $this->post->post_content;
            $this->price = $this->post->dc_room_price;
            $this->type = $this->post->dc_room_type;
            $this->photo = $this->post->dc_room_image;        
        }
        //return post id if found or false if not
        if ( !empty( $this->id ) )
            return $this->id;
        else
            return false;
    }
}