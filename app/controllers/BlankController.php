<?php
namespace Backend\Controllers;

use GSMS\Models\Services; 
use Phalcon\Http\Client\Exception;
use Phalcon\Paginator\Adapter\Model as Paginator;

class BlankController extends ControllerBase
{
    public function initialize()
    {
        $this->tag->setTitle('服务');
    }

    /**
     * 服务列表
     *
     * @return \Phalcon\Http\ResponseInterface
     */
    public function indexAction()
    {
        if($this->isGetDataOnly()) {

            $data = [];

            $page = $this->request->get('page', 'int');
            $pagesize = $this->request->get('pagesize', 'int');
            $name = $this->request->get('name', 'striptags');

            $conditions[] = '1=1';
            $parameters = [];

            if($name) {
                $conditions[] .= "name LIKE :name:";
                $parameters['name'] = '%'.$name.'%';
            }

            $services = Services::find([
                implode(' AND ', $conditions),
                'bind' => $parameters,
                'order' => 'id DESC'
            ]);

            $paginator = new Paginator(array(
                "data" => $services,
                "limit"=> $pagesize,
                "page" => $page
            ));

            $paginate = $paginator->getPaginate();

            $data['total'] = $paginate->total_items;
            $data['rows'] = [];

            foreach($paginate->items as $key => $item) {

                $data['rows'][$key] = $item->toArray();
            }

            return $this->response->setJsonContent($data);
        }
    }

    /**
     * 添加服务
     *
     * @return \Phalcon\Http\ResponseInterface
     */
    public function createAction()
    {

        if($this->request->isPost()) {
            $service = new Services();

            $service->name = $this->request->getPost('name', 'striptags');
            $service->url = $this->request->getPost('url', 'striptags');
            $service->username = $this->request->getPost('username', 'striptags');
            $service->password = $this->request->getPost('password');
            $service->auth_token = $this->request->getPost('auth_token');

            if($service->create()) {

                $this->flashSession->success('服务添加成功');

                return $this->response->redirect('services');
            }

            foreach ($service->getMessages() as $message) {

                $this->flashSession->error($message);
            }
        }
    }

    public function editAction($id)
    {
        $service = Services::findFirst((int)$id);

        if(!$service) {
            $this->flashSession->error('服务不存在');

            return $this->response->redirect('services');
        }

        if($this->request->isPost()) {
            $service->name = $this->request->getPost('name', 'striptags');
            $service->url = $this->request->getPost('url', 'striptags');
            $service->username = $this->request->getPost('username', 'striptags');
            $service->password = $this->request->getPost('password');
            $service->auth_token = $this->request->getPost('auth_token');

            if($service->update()) {

                $this->flashSession->success('服务修改成功');

                return $this->response->redirect('services');
            }

            foreach ($service->getMessages() as $message) {

                $this->flashSession->error($message);
            }
        }

        $this->view->service = $service;
    }

    public function deleteAction($id)
    {
        $service = Services::findFirst((int)$id);

        if(!$service) {
            $this->flashSession->error('服务不存在');
        } else {

            if($service->delete()) {
                $this->flashSession->success('服务删除成功');
            } else {
                foreach ($service->getMessages() as $message) {

                    $this->flashSession->error($message);
                }
            }
        }

        return $this->response->redirect('services');
    }

    /**
     * 检查服务状态
     */
    public function checkAction()
    {
        $soapChecker = new SoapChecker();

        $result = $soapChecker->checkConnect('http://10.100.113.38:8070/Service.asmx?wsdl');

        return $this->response->setJsonContent([
            'data' => [
                'result' => $result
            ]
        ]);
    }
}

