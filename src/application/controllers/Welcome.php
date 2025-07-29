<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Welcome extends CI_Controller {

    public function index() {
        $data = array(
            'message' => 'API RESTful de Usuários está funcionando!',
            'version' => '1.0.0',
            'endpoints' => array(
                'GET /api/health' => 'Health check da API',
                'GET /api/users' => 'Listar usuários',
                'GET /api/users/{id}' => 'Obter usuário específico',
                'POST /api/users' => 'Criar usuário',
                'PUT /api/users/{id}' => 'Atualizar usuário',
                'DELETE /api/users/{id}' => 'Deletar usuário'
            )
        );
        
        $this->output
            ->set_content_type('application/json')
            ->set_output(json_encode($data, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT));
    }
}