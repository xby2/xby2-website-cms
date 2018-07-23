<?php
require_once plugin_dir_path( dirname( __FILE__ ) ) . '/Controllers/ClientStoryController.php';
require_once plugin_dir_path( dirname( __FILE__ ) ) . '/Models/Xby2BaseModel.php';

class ClientStory extends Xby2BaseModel {

    public $id;
    public $imageUrl;
    public $listingImageUrl;
    public $title;
    public $description;
    public $industry;
    public $client;
    public $expertises;
    public $content;
    public $isFeatured;
    public $nextClientStoryId;
    public $nextClientStoryTitle;
    public $nextClientStoryDescription;

    public function __construct($post) {
        // Get the data we need from the post
        $this->id = $post->ID;
        $this->title = $post->post_title;
        $this->content = apply_filters('the_content', $post->post_content);

        // Get the data we need from our custom fields
        foreach ($post->meta as $key=>$value) {
            if ( property_exists ( $this , $key ) ) {
                if ($key == "expertises") {
                    $this->$key = $this->getPointsArray($value[0]);
                // Cast isFeatured as a boolean
                } elseif ($key == "isFeatured") {
                    $this->$key = filter_var($value[0], FILTER_VALIDATE_BOOLEAN);
                } else {
                    $this->$key = $value[0];
                }
            }
        }
    }

    // Register this custom post type, and its associated custom REST endpoints
	public static function registerTypeAndRoutes() {

        $data = array(
            'plural_name'   => 'Client Stories',
            'singular_name' => 'Client Story',
            'dashicon'      => 'dashicons-welcome-widgets-menus',
            'supports'      => array( 'title', 'editor', 'custom-fields' ),
            'post_type'     => 'clientstory'
        );

        parent::registerType($data);

		$controller = new ClientStoryController();
		$controller->register_routes();
	}

	// Add all custom post meta fields when creating a new post of this type
	public static function registerMeta($post_id) {
		add_post_meta($post_id, 'imageUrl',                   '', true);
        add_post_meta($post_id, 'listingImageUrl',            '', true);
		add_post_meta($post_id, 'description',                '', true);
		add_post_meta($post_id, 'industry',                   '', true);
		add_post_meta($post_id, 'client',                     '', true);
		add_post_meta($post_id, 'expertises',                 '', true);
        add_post_meta($post_id, 'isFeatured',                 '', true);
        add_post_meta($post_id, 'nextClientStoryId',          '', true);
        add_post_meta($post_id, 'nextClientStoryTitle',       '', true);
        add_post_meta($post_id, 'nextClientStoryDescription', '', true);
	}
}