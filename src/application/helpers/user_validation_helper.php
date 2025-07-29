<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Helper de Validação de Usuários
 * 
 * Contém todas as regras de validação e sanitização para usuários
 */

if (!function_exists('sanitize_user_data')) {
    /**
     * Sanitizar dados de entrada do usuário
     * 
     * @param array $data Dados do usuário
     * @return array Dados sanitizados
     */
    function sanitize_user_data($data) {
        $sanitized = array();
        
        if (isset($data['name'])) {
            $sanitized['name'] = strip_tags(trim($data['name']));
        }
        
        if (isset($data['email'])) {
            $sanitized['email'] = filter_var(trim($data['email']), FILTER_SANITIZE_EMAIL);
        }
        
        if (isset($data['password'])) {
            $sanitized['password'] = $data['password']; // Não sanitizar senha
        }
        
        if (isset($data['phone'])) {
            $sanitized['phone'] = preg_replace('/[^0-9\s\(\)\-\+]/', '', trim($data['phone']));
        }
        
        if (isset($data['status'])) {
            $sanitized['status'] = in_array($data['status'], ['active', 'inactive']) ? $data['status'] : 'active';
        }
        
        return $sanitized;
    }
}

if (!function_exists('validate_user_data')) {
    /**
     * Validar dados do usuário
     * 
     * @param array $data Dados do usuário
     * @param bool $password_required Se a senha é obrigatória
     * @param int|null $user_id ID do usuário (para updates)
     * @return array Array de erros (vazio se válido)
     */
    function validate_user_data($data, $password_required = true, $user_id = null) {
        $errors = array();

        // Sanitizar dados primeiro
        $data = sanitize_user_data($data);

        // Validar nome
        if (empty($data['name'])) {
            $errors['name'] = 'Nome é obrigatório';
        } elseif (strlen($data['name']) < 2) {
            $errors['name'] = 'Nome deve ter pelo menos 2 caracteres';
        } elseif (strlen($data['name']) > 100) {
            $errors['name'] = 'Nome deve ter no máximo 100 caracteres';
        } elseif (!preg_match('/^[a-zA-ZÀ-ÿ\s]+$/u', $data['name'])) {
            $errors['name'] = 'Nome deve conter apenas letras e espaços';
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
            } elseif (strlen($data['password']) > 255) {
                $errors['password'] = 'Senha deve ter no máximo 255 caracteres';
            }
        } elseif (isset($data['password']) && !empty($data['password'])) {
            if (strlen($data['password']) < 6) {
                $errors['password'] = 'Senha deve ter pelo menos 6 caracteres';
            } elseif (strlen($data['password']) > 255) {
                $errors['password'] = 'Senha deve ter no máximo 255 caracteres';
            }
        }

        // Validar telefone (opcional)
        if (isset($data['phone']) && !empty($data['phone'])) {
            if (strlen($data['phone']) > 20) {
                $errors['phone'] = 'Telefone deve ter no máximo 20 caracteres';
            } elseif (!preg_match('/^[\d\s\(\)\-\+]+$/', $data['phone'])) {
                $errors['phone'] = 'Formato de telefone inválido';
            }
        }

        // Validar status (opcional)
        if (isset($data['status']) && !empty($data['status'])) {
            if (!in_array($data['status'], ['active', 'inactive'])) {
                $errors['status'] = 'Status deve ser "active" ou "inactive"';
            }
        }

        return $errors;
    }
}

if (!function_exists('validate_user_filters')) {
    /**
     * Validar filtros de busca de usuários
     * 
     * @param array $filters Filtros aplicados
     * @return array Filtros validados e sanitizados
     */
    function validate_user_filters($filters) {
        $validated = array();
        
        // Validar limit
        $validated['limit'] = isset($filters['limit']) ? (int)$filters['limit'] : 10;
        if ($validated['limit'] > 100) {
            $validated['limit'] = 100; // Máximo 100 registros por página
        } elseif ($validated['limit'] < 1) {
            $validated['limit'] = 10;
        }
        
        // Validar offset
        $validated['offset'] = isset($filters['offset']) ? (int)$filters['offset'] : 0;
        if ($validated['offset'] < 0) {
            $validated['offset'] = 0;
        }
        
        // Validar status
        if (isset($filters['status']) && in_array($filters['status'], ['active', 'inactive'])) {
            $validated['status'] = $filters['status'];
        }
        
        // Validar search
        if (isset($filters['search']) && !empty($filters['search'])) {
            $validated['search'] = strip_tags(trim($filters['search']));
        }
        
        // Validar order_by
        $allowed_order_fields = ['id', 'name', 'email', 'status', 'created_at', 'updated_at'];
        $validated['order_by'] = isset($filters['order_by']) && in_array($filters['order_by'], $allowed_order_fields) 
            ? $filters['order_by'] 
            : 'created_at';
        
        // Validar order_dir
        $validated['order_dir'] = isset($filters['order_dir']) && in_array(strtoupper($filters['order_dir']), ['ASC', 'DESC']) 
            ? strtoupper($filters['order_dir']) 
            : 'DESC';
        
        return $validated;
    }
}