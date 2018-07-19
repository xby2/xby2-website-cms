<?php

require_once plugin_dir_path( dirname( __FILE__ ) ) . '/Controllers/FrequentlyAskedQuestionController.php';
require_once plugin_dir_path( dirname( __FILE__ ) ) . '/Models/Xby2BaseModel.php';

class FrequentlyAskedQuestion extends Xby2BaseModel {

    public $id;
    public $question;
    public $answer;

    public function __construct($post)
    {
        $this->id = $post->ID;

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
            'supports'      => array( 'custom-fields' ),
            'post_type'     => 'faq'
        );

        parent::registerType($data);

        $controller = new FrequentlyAskedQuestionController();
        $controller->register_routes();
	}

	public static function registerMeta($post_id){
        add_post_meta($post_id, 'answer',   '', true);
        add_post_meta($post_id, 'question', '', true);
	}
}