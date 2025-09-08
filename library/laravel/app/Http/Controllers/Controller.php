<?php

namespace App\Http\Controllers;

//require_once LARAVEL_SRC. '/Illuminate/Routing/Controller.php';
require_once '/home/bitnami/apps/wordpress/htdocs/wp-content/plugins/contract-tools/library/laravel/vendor/laravel/framework/src/Illuminate/Routing/Controller.php';
require_once '/home/bitnami/apps/wordpress/htdocs/wp-content/plugins/contract-tools/library/laravel/vendor/laravel/framework/src/Illuminate/Foundation/Auth/Access/AuthorizesRequests.php';
require_once '/home/bitnami/apps/wordpress/htdocs/wp-content/plugins/contract-tools/library/laravel/vendor/laravel/framework/src/Illuminate/Foundation/Bus/DispatchesJobs.php';
require_once '/home/bitnami/apps/wordpress/htdocs/wp-content/plugins/contract-tools/library/laravel/vendor/laravel/framework/src/Illuminate/Foundation/Validation/ValidatesRequests.php';

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;

class Controller extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;
}
