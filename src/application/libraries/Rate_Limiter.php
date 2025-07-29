<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Rate_Limiter {
    
    private $ci;
    private $cache_prefix = 'rate_limit_';
    
    public function __construct() {
        $this->ci =& get_instance();
    }
    
    /**
     * Verificar rate limit
     */
    public function check_rate_limit($identifier, $max_requests = 60, $time_window = 3600) {
        $cache_key = $this->cache_prefix . md5($identifier);
        
        // Simular cache com arquivo (em produção, use Redis ou Memcached)
        $cache_file = APPPATH . 'cache/' . $cache_key . '.txt';
        
        $current_time = time();
        $requests = array();
        
        // Ler requests existentes
        if (file_exists($cache_file)) {
            $data = file_get_contents($cache_file);
            $requests = json_decode($data, true) ?: array();
        }
        
        // Remover requests antigos
        $requests = array_filter($requests, function($timestamp) use ($current_time, $time_window) {
            return ($current_time - $timestamp) < $time_window;
        });
        
        // Verificar se excedeu o limite
        if (count($requests) >= $max_requests) {
            return false;
        }
        
        // Adicionar request atual
        $requests[] = $current_time;
        
        // Salvar no cache
        file_put_contents($cache_file, json_encode($requests));
        
        return true;
    }
    
    /**
     * Obter informações do rate limit
     */
    public function get_rate_limit_info($identifier, $max_requests = 60, $time_window = 3600) {
        $cache_key = $this->cache_prefix . md5($identifier);
        $cache_file = APPPATH . 'cache/' . $cache_key . '.txt';
        
        $current_time = time();
        $requests = array();
        
        if (file_exists($cache_file)) {
            $data = file_get_contents($cache_file);
            $requests = json_decode($data, true) ?: array();
        }
        
        // Remover requests antigos
        $requests = array_filter($requests, function($timestamp) use ($current_time, $time_window) {
            return ($current_time - $timestamp) < $time_window;
        });
        
        $remaining = max(0, $max_requests - count($requests));
        $reset_time = count($requests) > 0 ? min($requests) + $time_window : $current_time + $time_window;
        
        return array(
            'limit' => $max_requests,
            'remaining' => $remaining,
            'reset' => $reset_time,
            'reset_in' => max(0, $reset_time - $current_time)
        );
    }
}