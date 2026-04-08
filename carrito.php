<?php
require 'includes/app.php';

use Controller\CartController;

(new CartController())->index();

