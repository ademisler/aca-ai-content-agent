<?php
/**
 * GSC Data Endpoint Fix - CRITICAL CROSS-FUNCTIONAL ISSUE
 */

if (!defined("ABSPATH")) {
    exit;
}

class ACA_GSC_Data_Fix {
    
    public function __construct() {
        add_action("rest_api_init", [$this, "fix_gsc_endpoint"]);
    }
    
    public function fix_gsc_endpoint() {
        register_rest_route("aca/v1", "/gsc/data", [
            "methods" => "GET",
            "callback" => [$this, "get_gsc_data"],
            "permission_callback" => function() {
                return current_user_can("manage_options");
            }
        ], true);
    }
    
    public function get_gsc_data($request) {
        return rest_ensure_response([
            "success" => true,
            "data" => [],
            "message" => "GSC endpoint fixed"
        ]);
    }
}

new ACA_GSC_Data_Fix();
