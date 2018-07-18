<?php

require_once plugin_dir_path( dirname( __FILE__ ) ) . '/Controllers/LocationController.php';
require_once plugin_dir_path( dirname( __FILE__ ) ) . '/Models/Xby2BaseModel.php';

class Location extends Xby2BaseModel {

    public $id;
    public $name;
    public $address;
    public $address2;
    public $city;
    public $state;
    public $zip;
    public $phone;
    public $latitude;
    public $longitude;

    public function __construct($post)
    {
        // Get the data we need from the post
        $this->id = $post->ID;
        $this->name = $post->post_title;

        // Get the data we need from our custom fields
        foreach ($post->meta as $key=>$value) {
            if ( property_exists ( $this , $key ) ) {
                // parse the lat and lng to float values instead of strings
                if ($key == "latitude" || $key == "longitude") {
                    $value[0] = floatval($value[0]);
                }
                $this->$key = $value[0];
            }
        }
    }

    // Register this custom post type, and its associated custom REST endpoints
	public static function registerTypeAndRoutes() {

        $data = array(
            'plural_name'   => 'Locations',
            'singular_name' => 'Location',
            'dashicon'      => 'dashicons-location',
            'supports'      => array( 'title', 'custom-fields' ),
            'post_type'     => 'location'
        );

        parent::registerType($data);

		$controller = new LocationController();
		$controller->register_routes();
	}

    // Add all custom post meta fields when creating a new post of this type
	public static function registerMeta($post_id) {
		add_post_meta($post_id, 'address', '', true);
        add_post_meta($post_id, 'address2', '', true);
        add_post_meta($post_id, 'city', '', true);
        add_post_meta($post_id, 'state', '', true);
        add_post_meta($post_id, 'zip', '', true);
        add_post_meta($post_id, 'phone', '', true);
        add_post_meta($post_id, 'latitude', '', true);
        add_post_meta($post_id, 'longitude', '', true);
	}
}