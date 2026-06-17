<?php

class Yantram_CRM {
    private static $instance = null;
    private $db;

    public static function get_instance() {
        if (null === self::$instance) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    public function __construct() {
        global $wpdb;
        $this->db = $wpdb;
        $this->init();
    }

    private function init() {
        // Load admin pages
        add_action('admin_menu', [$this, 'add_admin_menu']);
        add_action('admin_enqueue_scripts', [$this, 'enqueue_scripts']);
        
        // Register AJAX handlers
        add_action('wp_ajax_get_leads', [$this, 'ajax_get_leads']);
        add_action('wp_ajax_add_lead', [$this, 'ajax_add_lead']);
        add_action('wp_ajax_update_lead', [$this, 'ajax_update_lead']);
        add_action('wp_ajax_delete_lead', [$this, 'ajax_delete_lead']);
        add_action('wp_ajax_search_leads', [$this, 'ajax_search_leads']);
    }

    public static function activate() {
        require_once YANTRAM_CRM_PATH . 'includes/database.php';
        Yantram_CRM_Database::create_tables();
    }

    public static function deactivate() {
        // Cleanup if needed
    }

    public function add_admin_menu() {
        add_menu_page(
            'Yantram CRM',
            'Yantram CRM',
            'manage_options',
            'yantram-crm',
            [$this, 'dashboard_page'],
            'dashicons-businessperson',
            6
        );

        add_submenu_page(
            'yantram-crm',
            'Dashboard',
            'Dashboard',
            'manage_options',
            'yantram-crm',
            [$this, 'dashboard_page']
        );

        add_submenu_page(
            'yantram-crm',
            'Leads',
            'Leads',
            'manage_options',
            'yantram-leads',
            [$this, 'leads_page']
        );

        add_submenu_page(
            'yantram-crm',
            'Pipeline',
            'Pipeline',
            'manage_options',
            'yantram-pipeline',
            [$this, 'pipeline_page']
        );

        add_submenu_page(
            'yantram-crm',
            'Contacts',
            'Contacts',
            'manage_options',
            'yantram-contacts',
            [$this, 'contacts_page']
        );

        add_submenu_page(
            'yantram-crm',
            'Tasks',
            'Tasks',
            'manage_options',
            'yantram-tasks',
            [$this, 'tasks_page']
        );

        add_submenu_page(
            'yantram-crm',
            'Reports',
            'Reports',
            'manage_options',
            'yantram-reports',
            [$this, 'reports_page']
        );

        add_submenu_page(
            'yantram-crm',
            'Settings',
            'Settings',
            'manage_options',
            'yantram-settings',
            [$this, 'settings_page']
        );
    }

    public function enqueue_scripts($hook) {
        if (strpos($hook, 'yantram-crm') === false) {
            return;
        }

        wp_enqueue_style('yantram-crm-style', YANTRAM_CRM_URL . 'assets/css/style.css', [], YANTRAM_CRM_VERSION);
        wp_enqueue_script('yantram-crm-script', YANTRAM_CRM_URL . 'assets/js/script.js', ['jquery'], YANTRAM_CRM_VERSION, true);
        
        wp_localize_script('yantram-crm-script', 'yantramCRM', [
            'ajax_url' => admin_url('admin-ajax.php'),
            'nonce' => wp_create_nonce('yantram_crm_nonce')
        ]);
    }

    public function dashboard_page() {
        require_once YANTRAM_CRM_PATH . 'admin/pages/dashboard.php';
    }

    public function leads_page() {
        require_once YANTRAM_CRM_PATH . 'admin/pages/leads.php';
    }

    public function pipeline_page() {
        require_once YANTRAM_CRM_PATH . 'admin/pages/pipeline.php';
    }

    public function contacts_page() {
        require_once YANTRAM_CRM_PATH . 'admin/pages/contacts.php';
    }

    public function tasks_page() {
        require_once YANTRAM_CRM_PATH . 'admin/pages/tasks.php';
    }

    public function reports_page() {
        require_once YANTRAM_CRM_PATH . 'admin/pages/reports.php';
    }

    public function settings_page() {
        require_once YANTRAM_CRM_PATH . 'admin/pages/settings.php';
    }

    // AJAX Handlers
    public function ajax_get_leads() {
        check_ajax_referer('yantram_crm_nonce', 'nonce');
        require_once YANTRAM_CRM_PATH . 'includes/class-leads.php';
        $leads = new Yantram_CRM_Leads();
        wp_send_json_success($leads->get_all($_POST));
    }

    public function ajax_add_lead() {
        check_ajax_referer('yantram_crm_nonce', 'nonce');
        require_once YANTRAM_CRM_PATH . 'includes/class-leads.php';
        $leads = new Yantram_CRM_Leads();
        wp_send_json_success($leads->add($_POST));
    }

    public function ajax_update_lead() {
        check_ajax_referer('yantram_crm_nonce', 'nonce');
        require_once YANTRAM_CRM_PATH . 'includes/class-leads.php';
        $leads = new Yantram_CRM_Leads();
        wp_send_json_success($leads->update($_POST));
    }

    public function ajax_delete_lead() {
        check_ajax_referer('yantram_crm_nonce', 'nonce');
        require_once YANTRAM_CRM_PATH . 'includes/class-leads.php';
        $leads = new Yantram_CRM_Leads();
        wp_send_json_success($leads->delete($_POST['id']));
    }

    public function ajax_search_leads() {
        check_ajax_referer('yantram_crm_nonce', 'nonce');
        require_once YANTRAM_CRM_PATH . 'includes/class-leads.php';
        $leads = new Yantram_CRM_Leads();
        wp_send_json_success($leads->search($_POST['query']));
    }
}
