<?php

class Yantram_CRM_Shortcodes {
    public static function init() {
        add_shortcode('yantram_dashboard', [__CLASS__, 'dashboard_shortcode']);
        add_shortcode('yantram_leads', [__CLASS__, 'leads_shortcode']);
        add_shortcode('yantram_pipeline', [__CLASS__, 'pipeline_shortcode']);
    }

    /**
     * Dashboard Shortcode: [yantram_dashboard]
     */
    public static function dashboard_shortcode() {
        require_once YANTRAM_CRM_PATH . 'includes/class-leads.php';
        $leads = new Yantram_CRM_Leads();
        $stats = $leads->get_stats();
        $total_leads = array_sum($stats);

        wp_enqueue_style('yantram-crm-style', YANTRAM_CRM_URL . 'assets/css/style.css');
        ?>
        <div class="yantram-dashboard-shortcode">
            <h2>Lead Pipeline</h2>
            <p>Track, manage and convert your leads efficiently.</p>
            
            <div class="yantram-stats-container">
                <div class="stat-box stat-new">
                    <h3>New</h3>
                    <p class="count"><?php echo $stats['New'] ?? 0; ?></p>
                    <p class="percentage"><?php echo $total_leads > 0 ? round(($stats['New'] / $total_leads) * 100, 1) : 0; ?>%</p>
                </div>
                
                <div class="stat-box stat-active">
                    <h3>Active</h3>
                    <p class="count"><?php echo $stats['Active'] ?? 0; ?></p>
                    <p class="percentage"><?php echo $total_leads > 0 ? round(($stats['Active'] / $total_leads) * 100, 1) : 0; ?>%</p>
                </div>
                
                <div class="stat-box stat-hold">
                    <h3>Hold</h3>
                    <p class="count"><?php echo $stats['Hold'] ?? 0; ?></p>
                    <p class="percentage"><?php echo $total_leads > 0 ? round(($stats['Hold'] / $total_leads) * 100, 1) : 0; ?>%</p>
                </div>
                
                <div class="stat-box stat-aold">
                    <h3>A-Old</h3>
                    <p class="count"><?php echo $stats['A-Old'] ?? 0; ?></p>
                    <p class="percentage"><?php echo $total_leads > 0 ? round(($stats['A-Old'] / $total_leads) * 100, 1) : 0; ?>%</p>
                </div>
                
                <div class="stat-box stat-close">
                    <h3>Close</h3>
                    <p class="count"><?php echo $stats['Close'] ?? 0; ?></p>
                    <p class="percentage"><?php echo $total_leads > 0 ? round(($stats['Close'] / $total_leads) * 100, 1) : 0; ?>%</p>
                </div>
            </div>
            
            <div class="yantram-total-leads">
                <h2>Total Leads</h2>
                <p class="total-count"><?php echo $total_leads; ?></p>
                <p class="description">This is your overall lead count.</p>
            </div>
        </div>
        <?php
        return ob_get_clean();
    }

    /**
     * Leads Shortcode: [yantram_leads]
     */
    public static function leads_shortcode() {
        require_once YANTRAM_CRM_PATH . 'includes/class-leads.php';
        $leads = new Yantram_CRM_Leads();
        $page = isset($_GET['paged']) ? intval($_GET['paged']) : 1;
        $per_page = 10;
        $all_leads = $leads->get_all(['page' => $page, 'per_page' => $per_page]);

        wp_enqueue_style('yantram-crm-style', YANTRAM_CRM_URL . 'assets/css/style.css');
        wp_enqueue_script('yantram-crm-script', YANTRAM_CRM_URL . 'assets/js/script.js', ['jquery']);
        wp_localize_script('yantram-crm-script', 'yantramCRM', [
            'ajax_url' => admin_url('admin-ajax.php'),
            'nonce' => wp_create_nonce('yantram_crm_nonce')
        ]);
        
        ob_start();
        ?>
        <div class="yantram-leads-shortcode">
            <h2>Leads Management</h2>
            
            <div class="yantram-lead-controls">
                <button id="add-lead-btn" class="button button-primary">+ Add Lead</button>
                <input type="text" id="lead-search" placeholder="Search leads..." class="yantram-search-input">
                <button id="apply-filters-btn" class="button">Apply Filters</button>
            </div>
            
            <table class="wp-list-table widefat striped">
                <thead>
                    <tr>
                        <th>Lead Name</th>
                        <th>Email</th>
                        <th>Phone</th>
                        <th>Status</th>
                        <th>Work Type</th>
                        <th>Source</th>
                        <th>Date Added</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($all_leads as $lead): ?>
                    <tr>
                        <td><?php echo esc_html($lead->lead_name); ?></td>
                        <td><?php echo esc_html($lead->contact_email); ?></td>
                        <td><?php echo esc_html($lead->contact_phone); ?></td>
                        <td><span class="status-badge status-<?php echo esc_attr($lead->status); ?>"><?php echo esc_html($lead->status); ?></span></td>
                        <td><?php echo esc_html($lead->work_type); ?></td>
                        <td><?php echo esc_html($lead->source); ?></td>
                        <td><?php echo esc_html(date('Y-m-d', strtotime($lead->date_added))); ?></td>
                        <td>
                            <button class="edit-lead" data-id="<?php echo $lead->id; ?>">Edit</button>
                            <button class="delete-lead" data-id="<?php echo $lead->id; ?>">Delete</button>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
        <?php
        return ob_get_clean();
    }

    /**
     * Pipeline Shortcode: [yantram_pipeline]
     */
    public static function pipeline_shortcode() {
        require_once YANTRAM_CRM_PATH . 'includes/class-leads.php';
        $leads = new Yantram_CRM_Leads();
        $stats = $leads->get_stats();

        wp_enqueue_style('yantram-crm-style', YANTRAM_CRM_URL . 'assets/css/style.css');
        
        ob_start();
        ?>
        <div class="yantram-pipeline-shortcode">
            <h2>Lead Pipeline</h2>
            <div class="pipeline-columns">
                <?php foreach (['New', 'Active', 'Hold', 'A-Old', 'Close'] as $stage): ?>
                <div class="pipeline-column pipeline-<?php echo strtolower($stage); ?>">
                    <h3><?php echo $stage; ?></h3>
                    <p class="stage-count"><?php echo $stats[$stage] ?? 0; ?></p>
                </div>
                <?php endforeach; ?>
            </div>
        </div>
        <?php
        return ob_get_clean();
    }
}

// Initialize shortcodes when WordPress loads
add_action('init', ['Yantram_CRM_Shortcodes', 'init']);
