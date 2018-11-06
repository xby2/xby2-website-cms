<?php

require_once plugin_dir_path( dirname( __FILE__ ) ) . '/Controllers/FrequentlyAskedQuestionController.php';
require_once plugin_dir_path( dirname( __FILE__ ) ) . '/Models/Xby2BaseModel.php';

class FrequentlyAskedQuestion extends Xby2BaseModel {

    public $id;
    public $question;
    public $answer;

    public function __construct($post)
    {
        $this->id = 'faq_' . $post->ID;

        // Get the data we need from our custom fields
        foreach ($post->meta as $key=>$value){
            if ( property_exists ( $this , $key ) ){
                $this->$key = $value[0];
            }
        }
    }

    public static function registerTypeAndRoutes(){

        $data = array(
            'plural_name'   => 'FAQs',
            'singular_name' => 'FAQ',
            'dashicon'      => 'dashicons-format-status',
            'supports'      => array( 'title' ),
            'post_type'     => 'faq'
        );

        parent::registerType($data);

        $controller = new FrequentlyAskedQuestionController();
        $controller->register_routes();
	}

    // Register the meta box to add to the Post edit page, and define the html callback
    public static function add_meta_box() {
        add_meta_box(
            'faq_meta',                 // Unique ID
            'Properties',               // Box title
            [self::class, 'meta_html'], // Content callback, must be of type callable
            "faq"                       // Post type
        );
    }

    // Callback to display the HTML for this meta box
    function meta_html($post)
    {
        $view = new Xby2BaseView(get_post_meta($post->ID));
        $view->addInput('text', 'faq-question', 'Question: ', 'question', 'large-text');
        $view->addInput('text', 'faq-answer',   'Answer: ',   'answer',   'large-text');
        $view->displayForm();
    }

    // Callback function to save the metadata when saving/updating the post
    public static function save_meta_data($post_id)
    {
        $standardMetaValues = array("question", "answer");
        parent::save_standard_meta_values($standardMetaValues, $post_id);
    }
}