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
            'supports'      => array( 'title' ),
            'post_type'     => 'location'
        );

        parent::registerType($data);

		$controller = new LocationController();
		$controller->register_routes();
	}

    // Register the meta box to add to the Post edit page, and define the html callback
    public static function add_meta_box() {
        add_meta_box(
            'location_meta',            // Unique ID
            'Properties',               // Box title
            [self::class, 'meta_html'], // Content callback, must be of type callable
            "location"                  // Post type
        );
    }

    // Callback to display the HTML for this meta box
    function meta_html($post)
    {
        $view = new Xby2BaseView(get_post_meta($post->ID));

        $view->addInput('text', 'location-address',   'Address: ',         'address',   'large-text');
        $view->addInput('text', 'location-address-2', 'Address 2: ',       'address2',  'large-text');
        $view->addInput('text', 'location-city',      'City: ',            'city',      'regular-text');
        $view->addInput('text', 'location-latitude',  'Latitude: ',        'latitude',  'regular-text');
        $view->addInput('text', 'location-longitude', 'Longitude: ',       'longitude', 'regular-text');
        $view->addInput('text', 'location-phone',     'Phone #: ',         'phone',     'regular-text');
        $view->addInput('text', 'location-state',     'State/Province: ',  'state',     'regular-text');
        $view->addInput('text', 'location-zip',       'ZIP/Postal Code: ', 'zip',       'regular-text');

        $view->displayForm();
    }

    // Callback function to save the metadata when saving/updating the post
    public static function save_meta_data($post_id) {
        $standardMetaValues = array("address", "address2", "city", "latitude", "longitude", "phone", "state", "zip");
        parent::save_standard_meta_values($standardMetaValues, $post_id);
    }
}