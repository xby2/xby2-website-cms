<?php

require_once plugin_dir_path( dirname( __FILE__ ) ) . '/Models/FrequentlyAskedQuestion.php';
require_once 'Xby2BaseController.php';

class FrequentlyAskedQuestionController extends Xby2BaseController {

    public function __construct() {
        $this->base = "frequently-asked-questions";
        $this->post_type = "faq";
    }

    /**
     * Register the routes for the objects of the controller.
     */
    public function register_routes() {
        $this->registerBaseRoutes();
    }

    /**
     * Prepare the item for the REST response
     *
     * @param mixed $item WordPress representation of the item.
     * @param WP_REST_Request $request Request object.
     * @return mixed
     */
    public function prepare_item_for_response( $item, $request ) {
        $meta = get_post_meta($item->ID);
        $item->meta = $meta;
        $FrequentlyAskedQuestion = new FrequentlyAskedQuestion($item);
        return $FrequentlyAskedQuestion;
    }
}