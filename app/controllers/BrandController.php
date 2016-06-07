<?php
/**
 * Created by PhpStorm.
 * User: huang
 * Date: 2016/5/13
 * Time: 9:18
 */

namespace GSMS\Controllers;
use Phalcon\Paginator\Adapter\NativeArray as pageArr;
use \GSMS\Models\Brand;


class BrandController  extends  ControllerBase
{
    public function initialize(){

        $this->tag->setTitle("品牌");

    }

    public function indexAction(){

        if($this->isGetDataOnly()) {
            $brandM = new Brand();

            $brand = $brandM->getAll();

            $paginator = new pageArr(array(
                "data" => $brand,
                "limit" => 10,
                "page" => 1
            ));

            $paginate = $paginator->getPaginate();

            $data['total'] = $paginate->total_items;
            $data['rows'] = [];

            foreach ($paginate->items as $key => $item) {

                $data['rows'][$key] = $item;
            }

            return $this->response->setJsonContent($data);
        }

    }


    public function isGetDataOnly()
    {
        return (int) $this->request->get('data', 'int') === 1;
    }

}