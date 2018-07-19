<?php

require_once plugin_dir_path( dirname( __FILE__ ) ) . '/Models/RecruitingValue.php';
require_once 'Xby2BaseController.php';

class RecruitingValueController extends Xby2BaseController {

    public function __construct() {
        $this->base = "recruiting-values";
        $this->post_type = "recruitingvalue";
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
        $RecruitingValue = new RecruitingValue($item);
        return $RecruitingValue;
    }
}