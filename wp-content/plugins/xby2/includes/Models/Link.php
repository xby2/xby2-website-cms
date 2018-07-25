<?php

require_once plugin_dir_path( dirname( __FILE__ ) ) . '/Controllers/LinkController.php';
require_once plugin_dir_path( dirname( __FILE__ ) ) . '/Models/Xby2BaseModel.php';

class Link extends Xby2BaseModel {

    public $id;
    public $label;
    public $link;
    public $priority;

    public function __construct($post)
    {
        // Get the data we need from the post
        $this->id = $post->ID;

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
            'plural_name'   => 'Links',
            'singular_name' => 'Link',
            'dashicon'      => 'dashicons-paperclip',
            'supports'      => array( 'title', 'custom-fields' ),
            'post_type'     => 'link'
        );

        parent::registerType($data);

		$controller = new LinkController();
		$controller->register_routes();
	}

    // Add all custom post meta fields when creating a new post of this type
	public static function registerMeta($post_id) {
		add_post_meta($post_id, 'label', '', true);
		add_post_meta($post_id, 'link',  '', true);
        add_post_meta($post_id, 'priority',  '', true);
	}
}