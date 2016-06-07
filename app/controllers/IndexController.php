<?php
namespace Backend\Controllers;

use Phalcon\Http\Client\Exception;

class IndexController extends ControllerBase
{

    public function indexAction()
    {
        $this->tag->setTitle('首页');
    }
}
