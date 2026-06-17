<?php

class Yantram_CRM_Database {
    public static function create_tables() {
        global $wpdb;
        require_once(ABSPATH . 'wp-admin/includes/upgrade.php');

        $charset_collate = $wpdb->get_charset_collate();

        // Leads table
        $leads_table = $wpdb->prefix . 'yantram_leads';
        $leads_sql = "CREATE TABLE IF NOT EXISTS $leads_table (
            id BIGINT(20) UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
            lead_name VARCHAR(255) NOT NULL,
            status VARCHAR(50) NOT NULL,
            work_type VARCHAR(100),
            contact_phone VARCHAR(20),
            contact_email VARCHAR(255),
            client_responsible VARCHAR(255),
            email_action VARCHAR(255),
            wa_action VARCHAR(255),
            source VARCHAR(100),
            comment LONGTEXT,
            date_added DATETIME DEFAULT CURRENT_TIMESTAMP,
            date_modified DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            created_by BIGINT(20),
            INDEX idx_status (status),
            INDEX idx_date_added (date_added)
        ) $charset_collate;";
        dbDelta($leads_sql);

        // Contacts table
        $contacts_table = $wpdb->prefix . 'yantram_contacts';
        $contacts_sql = "CREATE TABLE IF NOT EXISTS $contacts_table (
            id BIGINT(20) UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
            first_name VARCHAR(255) NOT NULL,
            last_name VARCHAR(255),
            email VARCHAR(255) UNIQUE,
            phone VARCHAR(20),
            company VARCHAR(255),
            address TEXT,
            city VARCHAR(100),
            state VARCHAR(100),
            country VARCHAR(100),
            postal_code VARCHAR(20),
            notes LONGTEXT,
            status VARCHAR(50),
            date_added DATETIME DEFAULT CURRENT_TIMESTAMP,
            date_modified DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            created_by BIGINT(20),
            INDEX idx_email (email),
            INDEX idx_status (status)
        ) $charset_collate;";
        dbDelta($contacts_sql);

        // Tasks table
        $tasks_table = $wpdb->prefix . 'yantram_tasks';
        $tasks_sql = "CREATE TABLE IF NOT EXISTS $tasks_table (
            id BIGINT(20) UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
            task_title VARCHAR(255) NOT NULL,
            description LONGTEXT,
            lead_id BIGINT(20) UNSIGNED,
            assigned_to BIGINT(20),
            status VARCHAR(50),
            priority VARCHAR(20),
            due_date DATETIME,
            date_added DATETIME DEFAULT CURRENT_TIMESTAMP,
            date_modified DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            INDEX idx_lead_id (lead_id),
            INDEX idx_assigned_to (assigned_to),
            INDEX idx_status (status),
            FOREIGN KEY (lead_id) REFERENCES {$wpdb->prefix}yantram_leads(id) ON DELETE CASCADE
        ) $charset_collate;";
        dbDelta($tasks_sql);

        // Activities table
        $activities_table = $wpdb->prefix . 'yantram_activities';
        $activities_sql = "CREATE TABLE IF NOT EXISTS $activities_table (
            id BIGINT(20) UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
            lead_id BIGINT(20) UNSIGNED,
            activity_type VARCHAR(100),
            activity_details LONGTEXT,
            date_added DATETIME DEFAULT CURRENT_TIMESTAMP,
            created_by BIGINT(20),
            INDEX idx_lead_id (lead_id),
            INDEX idx_activity_type (activity_type),
            FOREIGN KEY (lead_id) REFERENCES {$wpdb->prefix}yantram_leads(id) ON DELETE CASCADE
        ) $charset_collate;";
        dbDelta($activities_sql);

        // Pipeline Stages table
        $stages_table = $wpdb->prefix . 'yantram_pipeline_stages';
        $stages_sql = "CREATE TABLE IF NOT EXISTS $stages_table (
            id BIGINT(20) UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
            stage_name VARCHAR(255) NOT NULL UNIQUE,
            stage_order INT,
            color_code VARCHAR(7),
            description TEXT,
            date_added DATETIME DEFAULT CURRENT_TIMESTAMP
        ) $charset_collate;";
        dbDelta($stages_sql);

        // Insert default pipeline stages
        self::insert_default_stages();
    }

    private static function insert_default_stages() {
        global $wpdb;
        $table = $wpdb->prefix . 'yantram_pipeline_stages';
        
        $stages = [
            ['New', 1, '#003366', 'New leads'],
            ['Active', 2, '#006600', 'Active prospects'],
            ['Hold', 3, '#CC9900', 'On hold'],
            ['A-Old', 4, '#666666', 'Aged leads'],
            ['Close', 5, '#009900', 'Closed deals']
        ];

        foreach ($stages as $stage) {
            $wpdb->query($wpdb->prepare(
                "INSERT IGNORE INTO $table (stage_name, stage_order, color_code, description) VALUES (%s, %d, %s, %s)",
                $stage[0], $stage[1], $stage[2], $stage[3]
            ));
        }
    }
}
