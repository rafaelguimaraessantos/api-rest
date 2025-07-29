<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class REST_Controller extends CI_Controller {

    protected $request_method;
    protected $allowed_methods = array('GET', 'POST', 'PUT', 'DELETE', 'OPTIONS', 'HEAD');

    public function __construct() {
        parent::__construct();
        
        $this->request_method = $this->input->method(TRUE);
        
        // Configurar headers CORS
        $this->set_cors_headers();
        
        // Responder a requisições OPTIONS (preflight)
        if ($this->request_method === 'OPTIONS') {
            $this->handle_options_request();
            return;
        }
        
        // Verificar rate limiting (apenas em produção)
        if (ENVIRONMENT === 'production') {
            $this->check_rate_limiting();
        }
        
        // Verificar se o método é permitido
        if (!in_array($this->request_method, $this->allowed_methods)) {
            $this->response(array(
                'status' => 'error',
                'message' => 'Método não permitido'
            ), 405);
            return;
        }
        
        // Configurar content type para JSON
        $this->output->set_content_type('application/json');
    }

    /**
     * Configurar headers CORS de forma segura
     */
    private function set_cors_headers() {
        // Em produção, substitua por domínios específicos
        $allowed_origins = array(
            'http://localhost:3000',
            'http://localhost:8080',
            'http://localhost:8081',
            'https://seudominio.com'
        );
        
        $origin = isset($_SERVER['HTTP_ORIGIN']) ? $_SERVER['HTTP_ORIGIN'] : '';
        
        if (ENVIRONMENT === 'development') {
            // Em desenvolvimento, permite qualquer origem
            header('Access-Control-Allow-Origin: *');
        } elseif (in_array($origin, $allowed_origins)) {
            // Em produção, apenas origens permitidas
            header('Access-Control-Allow-Origin: ' . $origin);
        }
        
        header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS');
        header('Access-Control-Allow-Headers: Content-Type, Authorization, X-Requested-With');
        header('Access-Control-Allow-Credentials: true');
        header('Access-Control-Max-Age: 86400');
    }

    /**
     * Enviar resposta JSON
     */
    protected function response($data, $http_code = 200) {
        $this->output
            ->set_status_header($http_code)
            ->set_output(json_encode($data, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT));
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
     * Obter dados PUT/PATCH
     */
    protected function put($key = null) {
        $input = json_decode($this->input->raw_input_stream, true);
        
        if (!$input) {
            parse_str($this->input->raw_input_stream, $input);
        }
        
        if ($key === null) {
            return $input;
        }
        return isset($input[$key]) ? $input[$key] : null;
    }

    /**
     * Obter dados DELETE
     */
    protected function delete($key = null) {
        $input = json_decode($this->input->raw_input_stream, true);
        
        if (!$input) {
            parse_str($this->input->raw_input_stream, $input);
        }
        
        if ($key === null) {
            return $input;
        }
        return isset($input[$key]) ? $input[$key] : null;
    }

    /**
     * Tratar requisições OPTIONS (CORS preflight)
     */
    protected function handle_options_request() {
        $this->response(array(
            'status' => 'success',
            'message' => 'CORS preflight request handled',
            'allowed_methods' => $this->allowed_methods
        ), 200);
    }

    /**
     * Log de erro
     */
    protected function log_error($message, $data = null) {
        $log_data = array(
            'timestamp' => date('Y-m-d H:i:s'),
            'method' => $this->request_method,
            'uri' => $this->uri->uri_string(),
            'message' => $message,
            'data' => $data,
            'ip' => $this->input->ip_address(),
            'user_agent' => $this->input->user_agent()
        );
        
        log_message('error', json_encode($log_data));
    }

    /**
     * Log de atividade (para auditoria)
     */
    protected function log_activity($action, $data = null) {
        $log_data = array(
            'timestamp' => date('Y-m-d H:i:s'),
            'method' => $this->request_method,
            'uri' => $this->uri->uri_string(),
            'action' => $action,
            'data' => $data,
            'ip' => $this->input->ip_address(),
            'user_agent' => $this->input->user_agent()
        );
        
        log_message('info', json_encode($log_data));
    }

    /**
     * Verificar rate limiting
     */
    private function check_rate_limiting() {
        $this->load->library('rate_limiter');
        
        $identifier = $this->input->ip_address();
        $max_requests = 100; // 100 requests por hora
        $time_window = 3600; // 1 hora
        
        if (!$this->rate_limiter->check_rate_limit($identifier, $max_requests, $time_window)) {
            $rate_info = $this->rate_limiter->get_rate_limit_info($identifier, $max_requests, $time_window);
            
            // Adicionar headers de rate limit
            header('X-RateLimit-Limit: ' . $rate_info['limit']);
            header('X-RateLimit-Remaining: ' . $rate_info['remaining']);
            header('X-RateLimit-Reset: ' . $rate_info['reset']);
            
            $this->response(array(
                'status' => 'error',
                'message' => 'Rate limit excedido. Tente novamente em ' . $rate_info['reset_in'] . ' segundos.',
                'rate_limit' => $rate_info
            ), 429);
            return;
        }
        
        // Adicionar headers de rate limit para requests válidos
        $rate_info = $this->rate_limiter->get_rate_limit_info($identifier, $max_requests, $time_window);
        header('X-RateLimit-Limit: ' . $rate_info['limit']);
        header('X-RateLimit-Remaining: ' . $rate_info['remaining']);
        header('X-RateLimit-Reset: ' . $rate_info['reset']);
    }
}