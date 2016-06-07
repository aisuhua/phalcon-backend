<?php
namespace Backend\Controllers;

use Phalcon\Error\Error;

class ErrorController extends ControllerBase
{
    protected $error;

    public function initialize()
    {
        /** @var \Phalcon\Error\Error $error */
        $error = $this->dispatcher->getParam('error');

        if (!$error instanceof Error) {
            $error = new Error([
                'type'        => -1,
                'message'     => 'Something is not quite right',
                'file'        => __FILE__,
                'line'        => __LINE__,
                'exception'   => null,
                'isException' => false,
                'isError'     => true,
            ]);
        }

        $this->error = $error;

        $this->view->setVars([
            'error' => $this->error,
            'debug' => (ENV_DEVELOPMENT === APPLICATION_ENV || ENV_TESTING === APPLICATION_ENV),
        ]);
    }

    public function indexAction()
    {
        switch ($this->error->type()) {
            case 404:
                $this->tag->setTitle('Page not found');
                $code = 404;
                $message = '很抱歉, 你想看的页面已经不存在了!';
                break;
            case 403:
                $this->tag->setTitle('访问被拒绝');
                $code = 403;
                $message = '管理员已禁止对该资源的访问';
                break;
            case 401:
                $this->tag->setTitle('需要授权');
                $code = 401;
                $message = '访问该资源必须先授权';
                break;
            default:
                $this->tag->setTitle('服务器故障');
                $code = 500;
                $message = '很抱歉, 服务器发生故障！';
        }

        if($this->isPjax()) {
            $this->response->resetHeaders()->setStatusCode(200, null);
        } else {
            $this->response->resetHeaders()->setStatusCode($code, null);
        }

        //$this->response->resetHeaders()->setStatusCode($code, null);

        $this->view->setVars([
            'code'    => $code,
            'message' => $message,
        ]);
    }

    public function route404Action()
    {
        $this->tag->setTitle('找不到页面');
        $code = 404;

        $this->view->setVars([
            'code'    => $code,
            'message' => '很抱歉, 你想看的页面已经不存在了!',
        ]);

        if($this->isPjax()) {
            $this->response->resetHeaders()->setStatusCode(200, null);
        } else {
            $this->response->resetHeaders()->setStatusCode($code, null);
        }

        //$this->response->resetHeaders()->setStatusCode($code, 'Not Found');
    }
}
