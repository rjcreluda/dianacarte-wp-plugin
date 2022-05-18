<?php
/**
 * Class Wrapper for Listing Custom Post Type
 * Used for easy access to a listing object properties
 * instead of using standard wordrpress post properties
 **/

class Listing 
{
    //constructor can take a $post_id
    function __construct( $post_id = NULL ) 
    {
        if ( !empty( $post_id ) )
            $this->getPost( $post_id );
    }


    /** 
     * Populate Listing object with post properties
     * @param int $id: the post id
     * @return null
     */
    private function getPost( $post_id ) 
    {
        //get post
        $this->post = get_post( $post_id );
        //set some properties for easy access
        if ( !empty( $this->post ) ) {
            $this->id = $this->post->ID;
            //$this->post_id = $this->post->ID;
            $this->name = $this->post->post_title;
            //$this->user = $this->post->post_author;
            $this->content = $this->post->post_content;
            $thubmnail = get_the_post_thumbnail_url(  $this->id, 'post-thumbnail' );
            if( $thubmnail )
                $this->thumbnail_url = $thubmnail;
            else
                $this->thumbnail_url = DC_URL . 'assets/img/640x360.png';
            $this->features = Feature::get_name($this->id);
            $this->price = array(
                'min' => $this->post->_cth_price_from,
                'max' => $this->post->_cth_price_to
            );
            $this->contact = array(
                'address' => $this->post->_cth_contact_infos_address,
                'phone' => $this->post->_cth_contact_infos_phone,
                'email' => $this->post->_cth_contact_infos_email
            );
            $this->location = array(
                'latitude' => $this->post->_cth_contact_infos_latitude,
                'longitude' => $this->post->_cth_contact_infos_longitude
            );
            $this->showed = false;
            $cats = get_the_terms( $this->post->ID, 'listing_cat' );
            if ( $cats && ! is_wp_error( $cats ) ){
                $this->category = $cats[0]->name;
            }
            $this->rating = array(
                'average' => (float) $this->post->_cth_rating_average,
                'count' => (float) $this->post->_cth_rating_count
            );
            $this->link = get_permalink( $this->post, false );
			$place_cat = get_the_terms( $this->post->ID, 'listing_location' );
            if ( $place_cat && ! is_wp_error( $place_cat ) ){
                $this->place = array( 
                    'id' =>  $place_cat[0]->term_id,
                    'name' => $place_cat[0]->name,
                );
            }
            else{
                $this->place = null;
            }
        }
        //return post id if found or false if not
        if ( !empty( $this->id ) )
            return $this->id;
        else
            return false;
    }


    /* 
     * Get listings list by category id
     * @params category id: int
     * @return array of listing object
     */
    public static function category($id){
        $args = array(
        'post_type' => 'listing',
        'posts_per_page' => -1,
        'tax_query' => array(
            array(
            'taxonomy' => 'listing_cat',
            'field' => 'term_id',
            'terms' => $id
             )
          )
        );
        $data = new WP_Query( $args );
        if( count($data->posts) > 0 ){
            foreach($data->posts as $listing){
                $listings[] = new Listing($listing->ID);
            }
        }
        return $listings;
    }


    /* 
     * Get listings list by category
     * @params category term slug
     * @return array of listing object
     */
    public static function category_slug($slug){
        $args = array(
        'post_type' => 'listing',
        'posts_per_page' => -1,
        'tax_query' => array(
            array(
            'taxonomy' => 'listing_cat',
            'field' => 'slug',
            'terms' => $slug
             )
          )
        );
        $listings = array();
        $data = new WP_Query( $args );
        if( count($data->posts) > 0 ){
            foreach($data->posts as $listing){
                $listings[] = new Listing($listing->ID);
            }
        }
        return $listings;
    }


    /* 
     * Get all listings
     * @return array of listing object
     */
    public static function all(){
        $args = array(
        'post_type' => 'listing',
        'posts_per_page' => -1,
        );
        $data = new WP_Query( $args );
        $listings = array();
        if( count($data->posts) > 0 ){
            foreach($data->posts as $listing){
                $listings[] = new Listing($listing->ID);
            }
        }
        return $listings;
    }


    /* 
     * Get services CPT lists
     * @params null
     * @return array of Service object
     */
    public function services()
    {
        $query_args = array(
            'post_type'   => 'dc_service',
            'meta_query'  => array(
              array(
                'key'   => 'listing_id',
                'value' => $this->id
              )
            )
        );
        $query = new WP_Query( $query_args );
        $services = array();
        if( count($query->posts) > 0 ){
            foreach( $query->posts as $post ){
                array_push($services, new Service($post->ID));
            }
        }
        return $services;
    }

    /* 
     * Get available service types
     * @params null
     * @return array of Service object
     */
    public function availableServiceType( $services )
    {
        $availableServicesType = array();
        $list = array();
        foreach( $services as $service ){
            $serviceType = get_term( $service->type, 'service_cat' );
            if( ! in_array($serviceType->name, $list) ){
                array_push($availableServicesType, $serviceType);
                array_push($list, $serviceType->name);
            }
        }
        return $availableServicesType;
    }

    /** 
     * Get Listing Location Taxonomy
     * @param null
     * @return Object: array
     */
    public static function locationList()
    {
        $locations = array();
        $taxonomy = 'listing_location';
        $terms = get_terms( array($taxonomy), [
            'hide_empty' => false
        ] );
        foreach( $terms as $term ){
            array_push($locations, (object)[
                'id' => $term->term_id,
                'name' => $term->name
            ]);
        }
        return $locations;
    }
}
  