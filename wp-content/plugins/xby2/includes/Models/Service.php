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
                if ($key == "points") {
                    $this->$key = unserialize($value[0]);
                } else if ($key == "clientStoryId") {
                    $clientStoryPost = get_post($value[0]);
                    if ($clientStoryPost) {
                        $meta = get_post_meta($clientStoryPost->ID);
                        $this->clientStoryId = (isset($meta['slug'][0])) ? $meta['slug'][0] : $clientStoryPost->ID;
                    }
                }
                else {
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
            'supports'      => array( 'title', 'editor' ),
            'post_type'     => 'service'
        );

        parent::registerType($data);

		$controller = new ServiceController();
		$controller->register_routes();
	}

    // Register the meta box to add to the Post edit page, and define the html callback
    public static function add_meta_box() {
        add_meta_box(
            'service_meta',             // Unique ID
            'Properties',               // Box title
            [self::class, 'meta_html'], // Content callback, must be of type callable
            "service"                   // Post type
        );
    }

    // Callback to display the HTML for this meta box
    function meta_html($post)
    {
        $view = new Xby2BaseView(get_post_meta($post->ID));

        $clientStories = get_posts(['post_type' => 'clientstory', 'numberposts' => -1]);

        $view->addInput('dropdown',    'service-client-story', 'Client Story: ',      'clientStoryId', '', $clientStories);
        $view->addInput('imageSelect', 'service-image-url',    'Service Image Url: ', 'imageUrl',      'large-text');

        // Pass the existing values array if it exists
        if (is_array(unserialize($view->meta['points'][0]))) {
            $view->addInput('textList', 'service-points', 'Points: ', 'points', 'regular-text', unserialize($view->meta['points'][0]));
        } else {
            $view->addInput('textList', 'service-points', 'Points: ', 'points', 'regular-text', []);
        }

        $view->displayForm('service');
    }

    // Callback function to save the metadata when saving/updating the post
    public static function save_meta_data($post_id)
    {
        $standardMetaValues = array("clientStoryId", "imageUrl");
        parent::save_standard_meta_values($standardMetaValues, $post_id);

        // Iterate points, remove the empty ones, and save the remaining
        if (array_key_exists('points', $_POST)) {
            $points = $_POST['points'];
            foreach ($points as $key=>$point) {
                if (strlen($point) == 0) {
                    unset($points[$key]);
                }
            }
            update_post_meta(
                $post_id,
                'points',
                $points
            );
        }
    }
}