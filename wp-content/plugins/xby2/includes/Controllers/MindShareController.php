<?php
require_once plugin_dir_path( dirname( __FILE__ ) ) . '/Models/MindShare.php';
require_once 'Xby2BaseController.php';

class MindShareController extends Xby2BaseController {

    public function __construct() {
        $this->base = "mind-shares";
        $this->post_type = "mindshare";
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
        $MindShare = new MindShare($item);
        return $MindShare;
    }

}