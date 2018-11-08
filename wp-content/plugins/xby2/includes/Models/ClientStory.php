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

    public function __construct($post, $findNextClientStory = true) {
        // Get the data we need from the post
        $this->id = (isset($post->meta['slug'][0])) ? $post->meta['slug'][0] : $post->ID;
        $this->title = $post->post_title;
        $this->content = apply_filters('the_content', $post->post_content);

        // Get the data we need from our custom fields
        foreach ($post->meta as $key=>$value) {
            if ( property_exists ( $this , $key ) ) {
                if ($key == "expertises") {
                    $this->$key = $this->getServicesArray(unserialize($value[0]));
                // Cast isFeatured as a boolean
                } else if ($key == "isFeatured") {
                    $this->$key = filter_var($value[0], FILTER_VALIDATE_BOOLEAN);
                // Populate the "nextClientStory______" fields based on the given post ID
                } else if ($key == "nextClientStoryId" && $findNextClientStory) {
                    $clientStoryPost = get_post($value[0]);
                    if ($clientStoryPost) {
                        $meta = get_post_meta($clientStoryPost->ID);
                        $clientStoryPost->meta = $meta;
                        $clientStory = new ClientStory($clientStoryPost, false);
                        $this->nextClientStoryId = $clientStory->id;
                        $this->nextClientStoryDescription = $clientStory->description;
                        $this->nextClientStoryTitle = $clientStory->title;
                    }
                } else if ($key == "industry") {
                    $industryPost = get_post($value[0]);
                    if ($industryPost) {
                        $this->industry = $industryPost->post_title;
                    }
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
            'supports'      => array( 'title', 'editor' ),
            'post_type'     => 'clientstory'
        );

        parent::registerType($data);

		$controller = new ClientStoryController();
		$controller->register_routes();
	}

    // Register the meta box to add to the Post edit page, and define the html callback
    public static function add_meta_box() {
        add_meta_box(
            'client_story_meta',        // Unique ID
            'Properties',               // Box title
            [self::class, 'meta_html'], // Content callback, must be of type callable
            "clientstory"               // Post type
        );
    }

    // Callback to display the HTML for this meta box
    function meta_html($post)
    {
        $view = new Xby2BaseView(get_post_meta($post->ID));

        $industries    = get_posts(['post_type' => 'industry', 'numberposts' => -1]);
        $services      = get_posts(['post_type' => 'service', 'numberposts' => -1]);
        $clientStories = get_posts(['post_type' => 'clientstory', 'numberposts' => -1]);

        $view->addInput('text',         'client-story-client',            'Client: ',            'client',            'regular-text');
        $view->addInput('text',         'client-story-description',       'Description: ',       'description',       'large-text', [], true);
        $view->addInput('imageSelect',  'client-story-listing-image-url', 'Listing Image Url: ', 'listingImageUrl',   'large-text');
        $view->addInput('imageSelect',  'client-story-image-url',         'Image Url: ',         'imageUrl',          'large-text');
        $view->addInput('dropdown',     'client-story-industry',          'Industry: ',          'industry',          'regular-text', $industries);
        $view->addInput('dropdown',     'client-story-next-client-story', 'Next Client Story: ', 'nextClientStoryId', 'regular-text', $clientStories);
        $view->addInput('checkboxList', 'client-story-services',          'Services: ',          'expertises',        'regular-text', $services);
        $view->addInput('checkbox',     'client-story-featured',          'Featured (Y/N): ',    'isFeatured',        '');
        $view->addInput('text',         'client-story-slug',              'URL Slug: ',          'slug',              'regular-text');
        $view->displayForm();

    }

    // Callback function to save the metadata when saving/updating the post
    public static function save_meta_data($post_id)
    {
        $standardMetaValues  = array("client", "description", "expertises", "imageUrl", "industry", "listingImageUrl", "nextClientStoryId", "slug" );
        parent::save_standard_meta_values($standardMetaValues, $post_id);

        // Update Checkbox value
        update_post_meta(
            $post_id,
            'isFeatured',
            (array_key_exists('isFeatured', $_POST)) ? TRUE : FALSE
        );
    }

	// Iterate Service Id's, store all their names in array
	public function getServicesArray ($idArray) {
        $titles = [];
        foreach($idArray as $serviceId) {
            $servicePost = get_post($serviceId);
            if ($servicePost) {
                $titles[] = $servicePost->post_title;
            }
        }
        return $titles;
    }
}