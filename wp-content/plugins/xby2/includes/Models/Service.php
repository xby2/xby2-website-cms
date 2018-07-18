<?php

require_once plugin_dir_path( dirname( __FILE__ ) ) . '/Controllers/ServiceController.php';
require_once plugin_dir_path( dirname( __FILE__ ) ) . '/Models/Xby2BaseModel.php';

class Service extends Xby2BaseModel {

    public $id;
    public $title;
    public $text;
    public $points;
    public $clientStoryId;
    public $imageUrl;

    public function __construct($post) {

        // Get the data we need from the post
        $this->id = $post->ID;
        $this->title = $post->post_title;
        $this->text = strip_tags(apply_filters('the_content', $post->post_content));

        // Get the data we need from our custom fields
        foreach ($post->meta as $key=>$value) {
            if ( property_exists ( $this , $key ) ) {
                if ($key == "points"){
                    $this->$key = $this->getPointsArray($value[0]);
                } else {
                    $this->$key = $value[0];
                }
            }
        }
    }

    // Register this custom post type, and its associated custom REST endpoints
	public static function registerTypeAndRoutes() {

        $data = array(
            'plural_name'   => 'Services',
            'singular_name' => 'Service',
            'dashicon'      => 'dashicons-chart-area',
            'supports'      => array( 'title', 'editor', 'custom-fields' ),
            'post_type'     => 'service'
        );

        parent::registerType($data);

		$controller = new ServiceController();
		$controller->register_routes();
	}

    // Add all custom post meta fields when creating a new post of this type
	public static function registerMeta($post_id) {
		add_post_meta($post_id, 'points',        '', true);
		add_post_meta($post_id, 'clientStoryId', '', true);
        add_post_meta($post_id, 'imageUrl', '', true);
	}
}