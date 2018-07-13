<?php

use Phalcon\Mvc\Controller;

class ControllerBase extends Controller
{
    /**
     * @ignore
     * render json
     * @param array $data
     * @param int $errorCode
     */
    public function renderJson($data = [], $errorCode = 200)
    {
        $response = [];

        $errorList = $this->config->errorList;

        $response['code'] = $errorCode * 1;

        $response['msg'] = $errorList[$errorCode * 1];

        if (!empty($data)) {
            $response['data'] = $data;
        }

        $this->response->setContentType('application/json', 'UTF-8');
        $this->response->setJsonContent($response)->send();
        exit(0);
    }
}
