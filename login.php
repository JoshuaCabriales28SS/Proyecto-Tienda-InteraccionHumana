<?php
require 'includes/app.php';

use Controller\AuthController;

(new AuthController())->login();

