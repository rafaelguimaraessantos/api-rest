<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Biblioteca para padronizar respostas da API
 */
class Api_Response {
    
    private $ci;
    
    public function __construct() {
        $this->ci =& get_instance();
    }
    
    /**
     * Resposta de sucesso
     */
    public function success($data = null, $message = null, $http_code = 200) {
        $response = array(
            'status' => 'success'
        );
        
        if ($message !== null) {
            $response['message'] = $message;
        }
        
        if ($data !== null) {
            $response['data'] = $data;
        }
        
        $response['timestamp'] = date('Y-m-d H:i:s');
        
        return $this->send_response($response, $http_code);
    }
    
    /**
     * Resposta de sucesso com paginação
     */
    public function success_with_pagination($data, $pagination, $message = null) {
        $response = array(
            'status' => 'success',
            'data' => $data,
            'pagination' => $pagination,
            'timestamp' => date('Y-m-d H:i:s')
        );
        
        if ($message !== null) {
            $response['message'] = $message;
        }
        
        return $this->send_response($response, 200);
    }
    
    /**
     * Resposta de erro
     */
    public function error($message, $http_code = 400, $errors = null) {
        $response = array(
            'status' => 'error',
            'message' => $message,
            'timestamp' => date('Y-m-d H:i:s')
        );
        
        if ($errors !== null) {
            $response['errors'] = $errors;
        }
        
        return $this->send_response($response, $http_code);
    }
    
    /**
     * Resposta de erro de validação
     */
    public function validation_error($errors, $message = 'Dados inválidos') {
        return $this->error($message, 400, $errors);
    }
    
    /**
     * Resposta de não encontrado
     */
    public function not_found($message = 'Recurso não encontrado') {
        return $this->error($message, 404);
    }
    
    /**
     * Resposta de não autorizado
     */
    public function unauthorized($message = 'Não autorizado') {
        return $this->error($message, 401);
    }
    
    /**
     * Resposta de conflito
     */
    public function conflict($message = 'Conflito de dados') {
        return $this->error($message, 409);
    }
    
    /**
     * Resposta de erro interno
     */
    public function internal_error($message = 'Erro interno do servidor') {
        return $this->error($message, 500);
    }
    
    /**
     * Resposta de método não permitido
     */
    public function method_not_allowed($allowed_methods = []) {
        $response = array(
            'status' => 'error',
            'message' => 'Método HTTP não permitido',
            'timestamp' => date('Y-m-d H:i:s')
        );
        
        if (!empty($allowed_methods)) {
            $response['allowed_methods'] = $allowed_methods;
        }
        
        return $this->send_response($response, 405);
    }
    
    /**
     * Resposta de rate limit excedido
     */
    public function rate_limit_exceeded($rate_info) {
        $response = array(
            'status' => 'error',
            'message' => 'Rate limit excedido. Tente novamente em ' . $rate_info['reset_in'] . ' segundos.',
            'rate_limit' => $rate_info,
            'timestamp' => date('Y-m-d H:i:s')
        );
        
        return $this->send_response($response, 429);
    }
    
    /**
     * Enviar resposta JSON
     */
    private function send_response($data, $http_code) {
        $this->ci->output
            ->set_status_header($http_code)
            ->set_content_type('application/json')
            ->set_output(json_encode($data, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT));
    }
}