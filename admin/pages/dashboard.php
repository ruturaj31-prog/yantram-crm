<?php
if (!defined('ABSPATH')) exit;

require_once YANTRAM_CRM_PATH . 'includes/class-leads.php';
$leads = new Yantram_CRM_Leads();
$stats = $leads->get_stats();
$total_leads = array_sum($stats);
?>

<div class="wrap yantram-crm-dashboard">
    <h1>Yantram CRM - Dashboard</h1>
    
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
