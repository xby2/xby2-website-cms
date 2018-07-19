<?php

require_once plugin_dir_path( dirname( __FILE__ ) ) . '/Controllers/IndustryController.php';
require_once plugin_dir_path( dirname( __FILE__ ) ) . '/Models/Xby2BaseModel.php';

class Industry extends Xby2BaseModel {

    public $id;
    public $label;

    public function __construct($post) {
        // Get the data we need from the post
        $this->id = strtolower($post->post_title);
        $this->label = ucfirst($post->post_title);
    }

    // Register this custom post type, and its associated custom REST endpoints
    public static function registerTypeAndRoutes() {

        $data = array(
            'plural_name'   => 'Industries',
            'singular_name' => 'Industry',
            'dashicon'      => 'dashicons-building',
            'supports'      => array( 'title' ),
            'post_type'     => 'industry'
        );

        parent::registerType($data);

        $controller = new IndustryController();
        $controller->register_routes();
    }

    // Add all custom post meta fields when creating a new post of this type
    public static function registerMeta($post_id) {

    }
}