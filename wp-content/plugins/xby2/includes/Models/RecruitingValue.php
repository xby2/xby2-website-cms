<?php

require_once plugin_dir_path( dirname( __FILE__ ) ) . '/Controllers/RecruitingValueController.php';
require_once plugin_dir_path( dirname( __FILE__ ) ) . '/Models/Xby2BaseModel.php';

class RecruitingValue extends Xby2BaseModel {

    public $id;
    public $title;
    public $description;

    public function __construct($post) {

        // Get the data we need from the post
        $this->id = $post->ID;
        $this->title = $post->post_title;
        $this->description = strip_tags(apply_filters('the_content', $post->post_content));
    }

    // Register this custom post type, and its associated custom REST endpoints
    public static function registerTypeAndRoutes() {

        $data = array(
            'plural_name'   => 'Recruiting Values',
            'singular_name' => 'Recruiting Value',
            'dashicon'      => 'dashicons-forms',
            'supports'      => array( 'title', 'editor' ),
            'post_type'     => 'recruitingvalue'
        );

        parent::registerType($data);

        $controller = new RecruitingValueController();
        $controller->register_routes();
    }

    // Add all custom post meta fields when creating a new post of this type
    public static function registerMeta($post) {

    }
}