<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class User_model extends CI_Model {

    public function __construct() {
        parent::__construct();
        $this->load->database();
    }

    /**
     * Obter todos os usuários
     */
    public function get_all_users($limit = null, $offset = null) {
        if ($limit !== null) {
            $this->db->limit($limit, $offset);
        }
        
        $query = $this->db->get('users');
        return $query->result_array();
    }

    /**
     * Obter usuário por ID
     */
    public function get_user_by_id($id) {
        $this->db->where('id', $id);
        $query = $this->db->get('users');
        return $query->row_array();
    }

    /**
     * Verificar se email já existe
     */
    public function email_exists($email, $exclude_id = null) {
        $this->db->where('email', $email);
        if ($exclude_id !== null) {
            $this->db->where('id !=', $exclude_id);
        }
        $query = $this->db->get('users');
        return $query->num_rows() > 0;
    }

    /**
     * Criar novo usuário
     */
    public function create_user($data) {
        $user_data = array(
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => password_hash($data['password'], PASSWORD_DEFAULT),
            'phone' => isset($data['phone']) ? $data['phone'] : null,
            'created_at' => date('Y-m-d H:i:s'),
            'updated_at' => date('Y-m-d H:i:s')
        );

        $this->db->insert('users', $user_data);
        
        if ($this->db->affected_rows() > 0) {
            return $this->db->insert_id();
        }
        return false;
    }

    /**
     * Atualizar usuário
     */
    public function update_user($id, $data) {
        $user_data = array(
            'name' => $data['name'],
            'email' => $data['email'],
            'phone' => isset($data['phone']) ? $data['phone'] : null,
            'updated_at' => date('Y-m-d H:i:s')
        );

        // Atualizar senha apenas se fornecida
        if (isset($data['password']) && !empty($data['password'])) {
            $user_data['password'] = password_hash($data['password'], PASSWORD_DEFAULT);
        }

        $this->db->where('id', $id);
        $this->db->update('users', $user_data);
        
        return $this->db->affected_rows() > 0;
    }

    /**
     * Deletar usuário
     */
    public function delete_user($id) {
        $this->db->where('id', $id);
        $this->db->delete('users');
        
        return $this->db->affected_rows() > 0;
    }

    /**
     * Contar total de usuários
     */
    public function count_users() {
        return $this->db->count_all('users');
    }
}