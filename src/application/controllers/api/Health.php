<?php
defined('BASEPATH') OR exit('No direct script access allowed');

require APPPATH . 'libraries/REST_Controller.php';

class Health extends REST_Controller {

    public function __construct() {
        parent::__construct();
        $this->load->model('User_model');
    }

    /**
     * GET /api/health - Health check da API
     */
    public function index_get() {
        try {
            // Verificar conexão com banco de dados
            $db_status = $this->check_database_connection();
            
            // Obter estatísticas dos usuários
            $user_stats = $this->User_model->get_user_stats();
            
            $response = array(
                'status' => 'success',
                'message' => 'API está funcionando',
                'timestamp' => date('Y-m-d H:i:s'),
                'version' => '1.0.0',
                'environment' => ENVIRONMENT,
                'database' => array(
                    'status' => $db_status ? 'connected' : 'disconnected',
                    'host' => $this->db->hostname,
                    'database' => $this->db->database
                ),
                'statistics' => $user_stats
            );
            
            $http_code = $db_status ? 200 : 503;
            $this->response($response, $http_code);
            
        } catch (Exception $e) {
            $this->response(array(
                'status' => 'error',
                'message' => 'Erro no health check',
                'timestamp' => date('Y-m-d H:i:s')
            ), 503);
        }
    }

    /**
     * Verificar conexão com banco de dados
     */
    private function check_database_connection() {
        try {
            $this->db->query('SELECT 1');
            return true;
        } catch (Exception $e) {
            return false;
        }
    }
}