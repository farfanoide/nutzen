<?php

require_once __ROOT__ . '/app/core/router.php';

// Home Page
Router::get('/home', 'ApplicationController#home');
Router::get('/with/:named_parameter', 'ApplicationController#home');
