<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class User_model extends CI_Model {

    private $table = 'users';

    public function __construct() {
        parent::__construct();
        $this->load->database();
    }

    /**
     * Obter todos os usuários com filtros e paginação
     */
    public function get_all_users($filters = array()) {
        $this->db->select('id, name, email, phone, status, created_at, updated_at');
        
        // Aplicar filtros
        if (isset($filters['status']) && !empty($filters['status'])) {
            $this->db->where('status', $filters['status']);
        }
        
        if (isset($filters['search']) && !empty($filters['search'])) {
            $this->db->group_start();
            $this->db->like('name', $filters['search']);
            $this->db->or_like('email', $filters['search']);
            $this->db->group_end();
        }
        
        // Ordenação
        $order_by = isset($filters['order_by']) ? $filters['order_by'] : 'created_at';
        $order_dir = isset($filters['order_dir']) ? $filters['order_dir'] : 'DESC';
        $this->db->order_by($order_by, $order_dir);
        
        // Paginação
        if (isset($filters['limit']) && $filters['limit'] > 0) {
            $this->db->limit($filters['limit']);
            
            if (isset($filters['offset']) && $filters['offset'] > 0) {
                $this->db->offset($filters['offset']);
            }
        }
        
        $query = $this->db->get($this->table);
        return $query->result_array();
    }

    /**
     * Contar usuários com filtros
     */
    public function count_users($filters = array()) {
        if (isset($filters['status']) && !empty($filters['status'])) {
            $this->db->where('status', $filters['status']);
        }
        
        if (isset($filters['search']) && !empty($filters['search'])) {
            $this->db->group_start();
            $this->db->like('name', $filters['search']);
            $this->db->or_like('email', $filters['search']);
            $this->db->group_end();
        }
        
        return $this->db->count_all_results($this->table);
    }

    /**
     * Obter usuário por ID
     */
    public function get_user_by_id($id) {
        $this->db->select('id, name, email, phone, status, created_at, updated_at');
        $this->db->where('id', $id);
        $query = $this->db->get($this->table);
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
        $query = $this->db->get($this->table);
        return $query->num_rows() > 0;
    }

    /**
     * Criar novo usuário
     */
    public function create_user($data) {
        $user_data = array(
            'name' => trim($data['name']),
            'email' => strtolower(trim($data['email'])),
            'password' => password_hash($data['password'], PASSWORD_DEFAULT),
            'phone' => isset($data['phone']) ? trim($data['phone']) : null,
            'status' => isset($data['status']) ? $data['status'] : 'active',
            'created_at' => date('Y-m-d H:i:s'),
            'updated_at' => date('Y-m-d H:i:s')
        );

        $this->db->trans_start();
        $this->db->insert($this->table, $user_data);
        $insert_id = $this->db->insert_id();
        $this->db->trans_complete();
        
        if ($this->db->trans_status() === FALSE) {
            return false;
        }
        
        return $insert_id;
    }

    /**
     * Atualizar usuário
     */
    public function update_user($id, $data) {
        $user_data = array(
            'name' => trim($data['name']),
            'email' => strtolower(trim($data['email'])),
            'phone' => isset($data['phone']) ? trim($data['phone']) : null,
            'updated_at' => date('Y-m-d H:i:s')
        );

        // Atualizar senha apenas se fornecida
        if (isset($data['password']) && !empty($data['password'])) {
            $user_data['password'] = password_hash($data['password'], PASSWORD_DEFAULT);
        }

        // Atualizar status se fornecido
        if (isset($data['status']) && in_array($data['status'], ['active', 'inactive'])) {
            $user_data['status'] = $data['status'];
        }

        $this->db->trans_start();
        $this->db->where('id', $id);
        $this->db->update($this->table, $user_data);
        $this->db->trans_complete();
        
        if ($this->db->trans_status() === FALSE) {
            return false;
        }
        
        return $this->db->affected_rows() > 0;
    }

    /**
     * Deletar usuário (soft delete)
     */
    public function delete_user($id, $soft_delete = true) {
        $this->db->trans_start();
        
        if ($soft_delete) {
            // Soft delete - apenas marcar como inativo
            $this->db->where('id', $id);
            $this->db->update($this->table, array(
                'status' => 'inactive',
                'updated_at' => date('Y-m-d H:i:s')
            ));
        } else {
            // Hard delete - remover completamente
            $this->db->where('id', $id);
            $this->db->delete($this->table);
        }
        
        $this->db->trans_complete();
        
        if ($this->db->trans_status() === FALSE) {
            return false;
        }
        
        return $this->db->affected_rows() > 0;
    }

    /**
     * Verificar se usuário existe
     */
    public function user_exists($id) {
        $this->db->where('id', $id);
        $query = $this->db->get($this->table);
        return $query->num_rows() > 0;
    }

    /**
     * Obter estatísticas dos usuários
     */
    public function get_user_stats() {
        $stats = array();
        
        // Total de usuários
        $stats['total'] = $this->db->count_all($this->table);
        
        // Usuários ativos
        $this->db->where('status', 'active');
        $stats['active'] = $this->db->count_all_results($this->table);
        
        // Usuários inativos
        $this->db->where('status', 'inactive');
        $stats['inactive'] = $this->db->count_all_results($this->table);
        
        // Usuários criados hoje
        $this->db->where('DATE(created_at)', date('Y-m-d'));
        $stats['created_today'] = $this->db->count_all_results($this->table);
        
        return $stats;
    }
}