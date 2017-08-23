<?php

require_once __ROOT__ . '/app/core/router.php';

Router::get('/',      'ApplicationController#home');
Router::get('/about', 'ApplicationController#about');
