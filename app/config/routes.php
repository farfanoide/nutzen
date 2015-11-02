<?php

use App\Core\Router as Router;

// Home Page
Router::get('/', 'ApplicationController#home');
