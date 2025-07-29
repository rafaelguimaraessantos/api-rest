<?php
defined('BASEPATH') OR exit('No direct script access allowed');

require APPPATH . 'libraries/REST_Controller.php';

class Users extends REST_Controller {

    public function __construct() {
        parent::__construct();
        $this->load->model('User_model');
        $this->load->library('form_validation');
        
        // Configurar headers CORS
        header('Access-Control-Allow-Origin: *');
        header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS');
        header('Access-Control-Allow-Headers: Content-Type, Authorization');
        
        // Responder a requisições OPTIONS (preflight)
        if ($this->input->method() === 'options') {
            $this->response(null, 200);
        }
    }

    /**
     * GET /api/users - Listar todos os usuários
     */
    public function index_get() {
        try {
            $limit = $this->get('limit') ? (int)$this->get('limit') : null;
            $offset = $this->get('offset') ? (int)$this->get('offset') : null;
            
            $users = $this->User_model->get_all_users($limit, $offset);
            
            // Remover senhas dos dados retornados
            foreach ($users as &$user) {
                unset($user['password']);
            }
            
            $total = $this->User_model->count_users();
            
            $response = array(
                'status' => 'success',
                'data' => $users,
                'total' => $total,
                'count' => count($users)
            );
            
            $this->response($response, 200);
            
        } catch (Exception $e) {
            $this->response(array(
                'status' => 'error',
                'message' => 'Erro interno do servidor'
            ), 500);
        }
    }

    /**
     * GET /api/users/{id} - Obter usuário específico
     */
    public function show_get($id) {
        try {
            if (!is_numeric($id)) {
                $this->response(array(
                    'status' => 'error',
                    'message' => 'ID inválido'
                ), 400);
                return;
            }
            
            $user = $this->User_model->get_user_by_id($id);
            
            if (!$user) {
                $this->response(array(
                    'status' => 'error',
                    'message' => 'Usuário não encontrado'
                ), 404);
                return;
            }
            
            // Remover senha dos dados retornados
            unset($user['password']);
            
            $this->response(array(
                'status' => 'success',
                'data' => $user
            ), 200);
            
        } catch (Exception $e) {
            $this->response(array(
                'status' => 'error',
                'message' => 'Erro interno do servidor'
            ), 500);
        }
    }

    /**
     * POST /api/users - Criar novo usuário
     */
    public function create_post() {
        try {
            $input = json_decode($this->input->raw_input_stream, true);
            
            if (!$input) {
                $input = $this->post();
            }
            
            // Validação dos dados
            $validation_errors = $this->validate_user_data($input);
            if (!empty($validation_errors)) {
                $this->response(array(
                    'status' => 'error',
                    'message' => 'Dados inválidos',
                    'errors' => $validation_errors
                ), 400);
                return;
            }
            
            // Verificar se email já existe
            if ($this->User_model->email_exists($input['email'])) {
                $this->response(array(
                    'status' => 'error',
                    'message' => 'Email já está em uso'
                ), 409);
                return;
            }
            
            $user_id = $this->User_model->create_user($input);
            
            if ($user_id) {
                $user = $this->User_model->get_user_by_id($user_id);
                unset($user['password']);
                
                $this->response(array(
                    'status' => 'success',
                    'message' => 'Usuário criado com sucesso',
                    'data' => $user
                ), 201);
            } else {
                $this->response(array(
                    'status' => 'error',
                    'message' => 'Erro ao criar usuário'
                ), 500);
            }
            
        } catch (Exception $e) {
            $this->response(array(
                'status' => 'error',
                'message' => 'Erro interno do servidor'
            ), 500);
        }
    }    /**

     * PUT /api/users/{id} - Atualizar usuário
     */
    public function update_put($id) {
        try {
            if (!is_numeric($id)) {
                $this->response(array(
                    'status' => 'error',
                    'message' => 'ID inválido'
                ), 400);
                return;
            }
            
            // Verificar se usuário existe
            $existing_user = $this->User_model->get_user_by_id($id);
            if (!$existing_user) {
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
                unset($user['password']);
                
                $this->response(array(
                    'status' => 'success',
                    'message' => 'Usuário atualizado com sucesso',
                    'data' => $user
                ), 200);
            } else {
                $this->response(array(
                    'status' => 'error',
                    'message' => 'Nenhuma alteração foi feita'
                ), 400);
            }
            
        } catch (Exception $e) {
            $this->response(array(
                'status' => 'error',
                'message' => 'Erro interno do servidor'
            ), 500);
        }
    }

    /**
     * DELETE /api/users/{id} - Deletar usuário
     */
    public function delete_delete($id) {
        try {
            if (!is_numeric($id)) {
                $this->response(array(
                    'status' => 'error',
                    'message' => 'ID inválido'
                ), 400);
                return;
            }
            
            // Verificar se usuário existe
            $user = $this->User_model->get_user_by_id($id);
            if (!$user) {
                $this->response(array(
                    'status' => 'error',
                    'message' => 'Usuário não encontrado'
                ), 404);
                return;
            }
            
            $deleted = $this->User_model->delete_user($id);
            
            if ($deleted) {
                $this->response(array(
                    'status' => 'success',
                    'message' => 'Usuário deletado com sucesso'
                ), 200);
            } else {
                $this->response(array(
                    'status' => 'error',
                    'message' => 'Erro ao deletar usuário'
                ), 500);
            }
            
        } catch (Exception $e) {
            $this->response(array(
                'status' => 'error',
                'message' => 'Erro interno do servidor'
            ), 500);
        }
    }

    /**
     * Validar dados do usuário
     */
    private function validate_user_data($data, $password_required = true, $user_id = null) {
        $errors = array();
        
        // Validar nome
        if (empty($data['name'])) {
            $errors['name'] = 'Nome é obrigatório';
        } elseif (strlen($data['name']) < 2) {
            $errors['name'] = 'Nome deve ter pelo menos 2 caracteres';
        } elseif (strlen($data['name']) > 100) {
            $errors['name'] = 'Nome deve ter no máximo 100 caracteres';
        }
        
        // Validar email
        if (empty($data['email'])) {
            $errors['email'] = 'Email é obrigatório';
        } elseif (!filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
            $errors['email'] = 'Email inválido';
        } elseif (strlen($data['email']) > 150) {
            $errors['email'] = 'Email deve ter no máximo 150 caracteres';
        }
        
        // Validar senha
        if ($password_required) {
            if (empty($data['password'])) {
                $errors['password'] = 'Senha é obrigatória';
            } elseif (strlen($data['password']) < 6) {
                $errors['password'] = 'Senha deve ter pelo menos 6 caracteres';
            }
        } elseif (isset($data['password']) && !empty($data['password'])) {
            if (strlen($data['password']) < 6) {
                $errors['password'] = 'Senha deve ter pelo menos 6 caracteres';
            }
        }
        
        // Validar telefone (opcional)
        if (isset($data['phone']) && !empty($data['phone'])) {
            if (strlen($data['phone']) > 20) {
                $errors['phone'] = 'Telefone deve ter no máximo 20 caracteres';
            }
        }
        
        return $errors;
    }
}