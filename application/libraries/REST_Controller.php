<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class REST_Controller extends CI_Controller {

    protected $request_method;
    protected $allowed_methods = array('GET', 'POST', 'PUT', 'DELETE', 'OPTIONS');

    public function __construct() {
        parent::__construct();
        
        $this->request_method = $this->input->method(TRUE);
        
        // Verificar se o método é permitido
        if (!in_array($this->request_method, $this->allowed_methods)) {
            $this->response(array(
                'status' => 'error',
                'message' => 'Método não permitido'
            ), 405);
        }
        
        // Configurar content type para JSON
        $this->output->set_content_type('application/json');
    }

    /**
     * Enviar resposta JSON
     */
    protected function response($data, $http_code = 200) {
        $this->output
            ->set_status_header($http_code)
            ->set_output(json_encode($data, JSON_UNESCAPED_UNICODE));
    }

    /**
     * Obter dados GET
     */
    protected function get($key = null) {
        if ($key === null) {
            return $this->input->get();
        }
        return $this->input->get($key);
    }

    /**
     * Obter dados POST
     */
    protected function post($key = null) {
        if ($key === null) {
            return $this->input->post();
        }
        return $this->input->post($key);
    }

    /**
     * Obter dados PUT
     */
    protected function put($key = null) {
        parse_str($this->input->raw_input_stream, $put_data);
        
        if ($key === null) {
            return $put_data;
        }
        return isset($put_data[$key]) ? $put_data[$key] : null;
    }

    /**
     * Obter dados DELETE
     */
    protected function delete($key = null) {
        parse_str($this->input->raw_input_stream, $delete_data);
        
        if ($key === null) {
            return $delete_data;
        }
        return isset($delete_data[$key]) ? $delete_data[$key] : null;
    }
}