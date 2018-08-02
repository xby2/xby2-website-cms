<?php

require_once plugin_dir_path( dirname( __FILE__ ) ) . '/Controllers/AuthorController.php';
require_once plugin_dir_path( dirname( __FILE__ ) ) . '/Models/Xby2BaseModel.php';

class Author extends Xby2BaseModel {

    public $id;
    public $name;
    public $imageUrl;
    public $title;

    public function __construct($post)
    {
        // Get the data we need from the post
        $this->id = $post->ID;
        $this->name = $post->post_title;

        // Get the data we need from our custom fields
        foreach ($post->meta as $key=>$value) {
            if ( property_exists ( $this , $key ) ) {
                $this->$key = $value[0];
            }
        }
    }

    // Register this custom post type, and its associated custom REST endpoints
    public static function registerTypeAndRoutes() {

        $data = array(
            'plural_name'   => 'Authors',
            'singular_name' => 'Author',
            'dashicon'      => 'dashicons-businessman',
            'supports'      => array( 'title', 'custom-fields' ),
            'post_type'     => 'author'
        );

        parent::registerType($data);

		$controller = new AuthorController();
		$controller->register_routes();
	}

    // Add all custom post meta fields when creating a new post of this type
	public static function registerMeta($post_id) {
		add_post_meta($post_id, 'imageUrl', '', true);
        add_post_meta($post_id, 'title',    '', true);
	}
}