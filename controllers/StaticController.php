<?php

namespace Controller;

class StaticController extends BaseController {
    public function nosotros(): void {
        $this->render('static/nosotros');
    }

    public function dudas(): void {
        $this->render('static/dudas');
    }
}
