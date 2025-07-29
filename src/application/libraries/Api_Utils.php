<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Biblioteca de utilitários para a API
 */
class Api_Utils {
    
    private $ci;
    
    public function __construct() {
        $this->ci =& get_instance();
    }
    
    /**
     * Obter dados de entrada baseado no método HTTP
     */
    public function get_input_data() {
        $method = $this->ci->input->method(TRUE);
        
        switch($method) {
            case 'POST':
                return $this->get_post_data();
            case 'PUT':
            case 'PATCH':
                return $this->get_put_data();
            case 'DELETE':
                return $this->get_delete_data();
            default:
                return array();
        }
    }
    
    /**
     * Obter dados POST
     */
    private function get_post_data() {
        $input = json_decode($this->ci->input->raw_input_stream, true);
        
        if (!$input) {
            $input = $this->ci->input->post();
        }
        
        return $input ?: array();
    }
    
    /**
     * Obter dados PUT/PATCH
     */
    private function get_put_data() {
        $input = json_decode($this->ci->input->raw_input_stream, true);
        
        if (!$input) {
            parse_str($this->ci->input->raw_input_stream, $input);
        }
        
        return $input ?: array();
    }
    
    /**
     * Obter dados DELETE
     */
    private function get_delete_data() {
        $input = json_decode($this->ci->input->raw_input_stream, true);
        
        if (!$input) {
            parse_str($this->ci->input->raw_input_stream, $input);
        }
        
        return $input ?: array();
    }
    
    /**
     * Validar ID numérico
     */
    public function validate_id($id) {
        return is_numeric($id) && $id > 0;
    }
    
    /**
     * Obter informações do cliente
     */
    public function get_client_info() {
        return array(
            'ip' => $this->ci->input->ip_address(),
            'user_agent' => $this->ci->input->user_agent(),
            'method' => $this->ci->input->method(TRUE),
            'uri' => $this->ci->uri->uri_string(),
            'timestamp' => date('Y-m-d H:i:s')
        );
    }
    
    /**
     * Log de auditoria
     */
    public function log_activity($action, $data = null) {
        $client_info = $this->get_client_info();
        
        $log_data = array_merge($client_info, array(
            'action' => $action,
            'data' => $data
        ));
        
        log_message('info', 'AUDIT: ' . json_encode($log_data));
    }
    
    /**
     * Log de erro
     */
    public function log_error($message, $data = null) {
        $client_info = $this->get_client_info();
        
        $log_data = array_merge($client_info, array(
            'error_message' => $message,
            'error_data' => $data
        ));
        
        log_message('error', 'API_ERROR: ' . json_encode($log_data));
    }
    
    /**
     * Gerar paginação
     */
    public function build_pagination($total, $count, $limit, $offset) {
        return array(
            'total' => (int)$total,
            'count' => (int)$count,
            'limit' => (int)$limit,
            'offset' => (int)$offset,
            'has_more' => ($offset + $limit) < $total,
            'current_page' => floor($offset / $limit) + 1,
            'total_pages' => ceil($total / $limit)
        );
    }
    
    /**
     * Verificar se é ambiente de desenvolvimento
     */
    public function is_development() {
        return ENVIRONMENT === 'development';
    }
    
    /**
     * Verificar se é ambiente de produção
     */
    public function is_production() {
        return ENVIRONMENT === 'production';
    }
}