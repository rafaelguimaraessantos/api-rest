<?php
defined('BASEPATH') OR exit('No direct script access allowed');

require APPPATH . 'libraries/REST_Controller.php';

class Users extends REST_Controller {

    public function __construct() {
        parent::__construct();
        $this->load->model('User_model');
        $this->load->library('Api_Response', null, 'response');
        $this->load->library('Api_Utils', null, 'utils');
        $this->load->helper('user_validation');
    }

    /**
     * Método principal que roteia baseado no HTTP method
     */
    public function index() {
        $method = $this->input->method(TRUE);
        
        switch($method) {
            case 'GET':
                $this->users_get();
                break;
            case 'POST':
                $this->users_post();
                break;
            default:
                $this->method_not_allowed();
        }
    }

    /**
     * Método para lidar com usuário específico
     */
    public function show($id = null) {
        if ($id === null || !is_numeric($id) || $id <= 0) {
            $this->response(array(
                'status' => 'error',
                'message' => 'ID inválido ou não fornecido'
            ), 400);
            return;
        }

        $method = $this->input->method(TRUE);
        
        switch($method) {
            case 'GET':
                $this->user_get($id);
                break;
            case 'PUT':
                $this->user_put($id);
                break;
            case 'DELETE':
                $this->user_delete($id);
                break;
            default:
                $this->method_not_allowed();
        }
    }

    /**
     * Resposta para método não permitido
     */
    private function method_not_allowed() {
        $this->response->method_not_allowed($this->allowed_methods);
    }

    /**
     * GET /api/users - Listar todos os usuários
     */
    private function users_get() {
        try {
            // Obter e validar filtros
            $input_filters = array(
                'limit' => $this->get('limit'),
                'offset' => $this->get('offset'),
                'status' => $this->get('status'),
                'search' => $this->get('search'),
                'order_by' => $this->get('order_by'),
                'order_dir' => $this->get('order_dir')
            );
            
            $filters = validate_user_filters($input_filters);

            $users = $this->User_model->get_all_users($filters);
            $total = $this->User_model->count_users($filters);

            $pagination = $this->utils->build_pagination($total, count($users), $filters['limit'], $filters['offset']);

            $this->response->success_with_pagination($users, $pagination);

        } catch (Exception $e) {
            $this->utils->log_error('Erro ao listar usuários', $e->getMessage());
            $this->response->internal_error();
        }
    }

    /**
     * GET /api/users/{id} - Obter usuário específico
     */
    private function user_get($id) {
        try {
            if (!$this->utils->validate_id($id)) {
                return $this->response->error('ID inválido', 400);
            }

            $user = $this->User_model->get_user_by_id($id);

            if (!$user) {
                return $this->response->not_found('Usuário não encontrado');
            }

            $this->response->success($user);

        } catch (Exception $e) {
            $this->utils->log_error('Erro ao obter usuário', array('id' => $id, 'error' => $e->getMessage()));
            $this->response->internal_error();
        }
    }

    /**
     * POST /api/users - Criar novo usuário
     */
    private function users_post() {
        try {
            $input = $this->utils->get_input_data();

            if (empty($input)) {
                return $this->response->error('Dados não fornecidos', 400);
            }

            // Validação dos dados
            $validation_errors = validate_user_data($input, true);
            if (!empty($validation_errors)) {
                return $this->response->validation_error($validation_errors);
            }

            // Verificar se email já existe
            if ($this->User_model->email_exists($input['email'])) {
                return $this->response->conflict('Email já está em uso');
            }

            $user_id = $this->User_model->create_user($input);

            if ($user_id) {
                $user = $this->User_model->get_user_by_id($user_id);

                // Log de auditoria
                $this->utils->log_activity('USER_CREATED', array(
                    'user_id' => $user_id,
                    'email' => $input['email']
                ));

                $this->response->success($user, 'Usuário criado com sucesso', 201);
            } else {
                $this->response->internal_error('Erro ao criar usuário');
            }

        } catch (Exception $e) {
            $this->utils->log_error('Erro ao criar usuário', $e->getMessage());
            $this->response->internal_error();
        }
    }    /**

     * PUT /api/users/{id} - Atualizar usuário
     */
    private function user_put($id) {
        try {
            if (!is_numeric($id) || $id <= 0) {
                $this->response(array(
                    'status' => 'error',
                    'message' => 'ID inválido'
                ), 400);
                return;
            }

            // Verificar se usuário existe
            if (!$this->User_model->user_exists($id)) {
                $this->response(array(
                    'status' => 'error',
                    'message' => 'Usuário não encontrado'
                ), 404);
                return;
            }

            $input = json_decode($this->input->raw_input_stream, true);

            if (!$input) {
                $input = $this->put();
            }

            if (empty($input)) {
                $this->response(array(
                    'status' => 'error',
                    'message' => 'Dados não fornecidos'
                ), 400);
                return;
            }

            // Validação dos dados (senha opcional para update)
            $validation_errors = $this->validate_user_data($input, false, $id);
            if (!empty($validation_errors)) {
                $this->response(array(
                    'status' => 'error',
                    'message' => 'Dados inválidos',
                    'errors' => $validation_errors
                ), 400);
                return;
            }

            // Verificar se email já existe (excluindo o usuário atual)
            if ($this->User_model->email_exists($input['email'], $id)) {
                $this->response(array(
                    'status' => 'error',
                    'message' => 'Email já está em uso'
                ), 409);
                return;
            }

            $updated = $this->User_model->update_user($id, $input);

            if ($updated) {
                $user = $this->User_model->get_user_by_id($id);

                // Log de auditoria
                $this->log_activity('USER_UPDATED', array(
                    'user_id' => $id,
                    'updated_fields' => array_keys($input)
                ));

                $this->response(array(
                    'status' => 'success',
                    'message' => 'Usuário atualizado com sucesso',
                    'data' => $user
                ), 200);
            } else {
                $this->response(array(
                    'status' => 'error',
                    'message' => 'Nenhuma alteração foi feita ou erro ao atualizar'
                ), 400);
            }

        } catch (Exception $e) {
            $this->log_error('Erro ao atualizar usuário', array('id' => $id, 'error' => $e->getMessage()));
            $this->response(array(
                'status' => 'error',
                'message' => 'Erro interno do servidor'
            ), 500);
        }
    }

    /**
     * DELETE /api/users/{id} - Deletar usuário
     */
    private function user_delete($id) {
        try {
            if (!is_numeric($id) || $id <= 0) {
                $this->response(array(
                    'status' => 'error',
                    'message' => 'ID inválido'
                ), 400);
                return;
            }

            // Verificar se usuário existe
            if (!$this->User_model->user_exists($id)) {
                $this->response(array(
                    'status' => 'error',
                    'message' => 'Usuário não encontrado'
                ), 404);
                return;
            }

            // Parâmetro para definir se é soft delete ou hard delete
            $hard_delete = $this->get('hard') === 'true';

            $deleted = $this->User_model->delete_user($id, !$hard_delete);

            if ($deleted) {
                $message = $hard_delete ? 'Usuário deletado permanentemente' : 'Usuário desativado com sucesso';
                
                // Log de auditoria
                $this->log_activity($hard_delete ? 'USER_HARD_DELETED' : 'USER_SOFT_DELETED', array(
                    'user_id' => $id,
                    'hard_delete' => $hard_delete
                ));
                
                $this->response(array(
                    'status' => 'success',
                    'message' => $message
                ), 200);
            } else {
                $this->response(array(
                    'status' => 'error',
                    'message' => 'Erro ao deletar usuário'
                ), 500);
            }

        } catch (Exception $e) {
            $this->log_error('Erro ao deletar usuário', array('id' => $id, 'error' => $e->getMessage()));
            $this->response(array(
                'status' => 'error',
                'message' => 'Erro interno do servidor'
            ), 500);
        }
    }

}