<?php

require_once plugin_dir_path( dirname( __FILE__ ) ) . '/Controllers/CompanyValueController.php';
require_once plugin_dir_path( dirname( __FILE__ ) ) . '/Models/Xby2BaseModel.php';

class CompanyValue extends Xby2BaseModel {

    public $id;
    public $iconUrl;
    public $title;
    public $description;

    public function __construct($post)
    {
        // Get the data we need from the post
        $this->id = $post->ID;
        $this->title = $post->post_title;
        $this->description = strip_tags(apply_filters('the_content', $post->post_content));

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
            'plural_name'   => 'Company Values',
            'singular_name' => 'Company Value',
            'dashicon'      => 'dashicons-slides',
            'supports'      => array( 'title', 'editor', 'custom-fields' ),
            'post_type'     => 'companyvalue'
        );

        parent::registerType($data);

        $controller = new CompanyValueController();
        $controller->register_routes();
    }

    // Add all custom post meta fields when creating a new post of this type
    public static function registerMeta($post_id) {
        add_post_meta($post_id, 'iconUrl', '', true);
    }
}