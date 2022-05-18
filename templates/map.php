<?php 
/*$args = array(
    'post_type' => 'listing',
    'posts_per_page' => -1,
    );
$data = new WP_Query( $args );
$lists = array();
foreach($data->posts as $listing){
	$lists[] = new Listing($listing->ID);
}*/

$args = array(
        'post_type' => 'listing',
        'posts_per_page' => 2,
        /*'paged' => 2*/
        );
        //$query = new WP_Query( $args );
        /*max_num_pages, found_posts, query_vars.paged=0*/
        /*echo '<pre>';
        print_r($query);
        echo '</pre>';*/
?>

<div id="app">
	Hello Map
</div>