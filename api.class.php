<?php

class Api {
    public $req;

    public function __construct() {
        $this->req = json_decode(file_get_contents('php://input'), true);
    }

    public function check($field) {
        if (!isset($this->req[$field])) {
            $this->send('Erro: campo ['.$field.'] não informado!', 400);
        }
    }

    public function post($route) {
        if($_SERVER['REQUEST_METHOD'] == 'POST') {
            if(isset($this->req['action']) && $this->req['action'] == $route) {
                if(!isset($this->req['token']) || $this->req['token'] != getenv('ACCESS_TOKEN')) {
                    $this->send('Erro: token inválido!', 403);
                } else {
                    return TRUE;
                }
            } else {
                return FALSE;
            }
        }
    }

    public function send($response, $httpStatus = 200) {
        $res = array();
        http_response_code($httpStatus);
        $res['status'] = $httpStatus;
        $res['data'] = $response;
        echo json_encode($res);
        exit();
    }
}

?>