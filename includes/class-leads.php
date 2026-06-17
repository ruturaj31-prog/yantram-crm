<?php

class Yantram_CRM_Leads {
    private $db;
    private $table;

    public function __construct() {
        global $wpdb;
        $this->db = $wpdb;
        $this->table = $wpdb->prefix . 'yantram_leads';
    }

    public function get_all($params = []) {
        $query = "SELECT * FROM {$this->table} WHERE 1=1";
        
        // Filter by status
        if (!empty($params['status'])) {
            $query .= $this->db->prepare(" AND status = %s", $params['status']);
        }

        // Filter by work type
        if (!empty($params['work_type'])) {
            $query .= $this->db->prepare(" AND work_type = %s", $params['work_type']);
        }

        // Filter by source
        if (!empty($params['source'])) {
            $query .= $this->db->prepare(" AND source = %s", $params['source']);
        }

        // Search
        if (!empty($params['search'])) {
            $search = '%' . $this->db->esc_like($params['search']) . '%';
            $query .= $this->db->prepare(
                " AND (lead_name LIKE %s OR contact_phone LIKE %s OR contact_email LIKE %s)",
                $search, $search, $search
            );
        }

        // Sorting
        $order_by = !empty($params['order_by']) ? $params['order_by'] : 'date_added';
        $order = !empty($params['order']) ? $params['order'] : 'DESC';
        $query .= $this->db->prepare(" ORDER BY %i %i", $order_by, $order);

        // Pagination
        $per_page = !empty($params['per_page']) ? intval($params['per_page']) : 10;
        $page = !empty($params['page']) ? intval($params['page']) : 1;
        $offset = ($page - 1) * $per_page;
        $query .= " LIMIT $offset, $per_page";

        return $this->db->get_results($query);
    }

    public function get_by_id($id) {
        return $this->db->get_row($this->db->prepare(
            "SELECT * FROM {$this->table} WHERE id = %d",
            $id
        ));
    }

    public function add($data) {
        $insert_data = [
            'lead_name' => sanitize_text_field($data['lead_name'] ?? ''),
            'status' => sanitize_text_field($data['status'] ?? 'New'),
            'work_type' => sanitize_text_field($data['work_type'] ?? ''),
            'contact_phone' => sanitize_text_field($data['contact_phone'] ?? ''),
            'contact_email' => sanitize_email($data['contact_email'] ?? ''),
            'client_responsible' => sanitize_text_field($data['client_responsible'] ?? ''),
            'email_action' => sanitize_text_field($data['email_action'] ?? ''),
            'wa_action' => sanitize_text_field($data['wa_action'] ?? ''),
            'source' => sanitize_text_field($data['source'] ?? ''),
            'comment' => sanitize_textarea_field($data['comment'] ?? ''),
            'created_by' => get_current_user_id()
        ];

        $result = $this->db->insert($this->table, $insert_data);
        return $result ? $this->db->insert_id : false;
    }

    public function update($data) {
        $id = intval($data['id'] ?? 0);
        if (!$id) return false;

        $update_data = [
            'lead_name' => sanitize_text_field($data['lead_name'] ?? ''),
            'status' => sanitize_text_field($data['status'] ?? ''),
            'work_type' => sanitize_text_field($data['work_type'] ?? ''),
            'contact_phone' => sanitize_text_field($data['contact_phone'] ?? ''),
            'contact_email' => sanitize_email($data['contact_email'] ?? ''),
            'client_responsible' => sanitize_text_field($data['client_responsible'] ?? ''),
            'email_action' => sanitize_text_field($data['email_action'] ?? ''),
            'wa_action' => sanitize_text_field($data['wa_action'] ?? ''),
            'source' => sanitize_text_field($data['source'] ?? ''),
            'comment' => sanitize_textarea_field($data['comment'] ?? '')
        ];

        return $this->db->update(
            $this->table,
            $update_data,
            ['id' => $id]
        );
    }

    public function delete($id) {
        return $this->db->delete($this->table, ['id' => intval($id)]);
    }

    public function search($query) {
        $search = '%' . $this->db->esc_like($query) . '%';
        return $this->db->get_results($this->db->prepare(
            "SELECT * FROM {$this->table} WHERE lead_name LIKE %s OR contact_email LIKE %s OR contact_phone LIKE %s",
            $search, $search, $search
        ));
    }

    public function get_stats() {
        $stats = [];
        $statuses = ['New', 'Active', 'Hold', 'A-Old', 'Close'];
        
        foreach ($statuses as $status) {
            $count = $this->db->get_var($this->db->prepare(
                "SELECT COUNT(*) FROM {$this->table} WHERE status = %s",
                $status
            ));
            $stats[$status] = $count;
        }
        
        return $stats;
    }
}
