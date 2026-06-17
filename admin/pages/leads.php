<?php
if (!defined('ABSPATH')) exit;

require_once YANTRAM_CRM_PATH . 'includes/class-leads.php';
$leads = new Yantram_CRM_Leads();

$page = isset($_GET['paged']) ? intval($_GET['paged']) : 1;
$per_page = 10;
all_leads = $leads->get_all(['page' => $page, 'per_page' => $per_page]);
?>

<div class="wrap yantram-crm-leads">
    <h1>Leads Management</h1>
    
    <div class="yantram-lead-controls">
        <button id="add-lead-btn" class="button button-primary">+ Add Lead</button>
        <input type="text" id="lead-search" placeholder="Search leads by name, phone, email..." class="yantram-search-input">
        <button id="apply-filters-btn" class="button">Apply Filters</button>
        <button id="reset-filters-btn" class="button">Reset</button>
    </div>
    
    <div id="add-lead-modal" class="yantram-modal" style="display:none;">
        <div class="modal-content">
            <span class="close">&times;</span>
            <h2>Add New Lead</h2>
            <form id="add-lead-form">
                <input type="text" name="lead_name" placeholder="Lead Name" required>
                <input type="email" name="contact_email" placeholder="Email" required>
                <input type="tel" name="contact_phone" placeholder="Phone" required>
                <select name="status">
                    <option value="New">New</option>
                    <option value="Active">Active</option>
                    <option value="Hold">Hold</option>
                    <option value="A-Old">A-Old</option>
                    <option value="Close">Close</option>
                </select>
                <input type="text" name="work_type" placeholder="Work Type">
                <input type="text" name="source" placeholder="Source">
                <textarea name="comment" placeholder="Comments"></textarea>
                <button type="submit" class="button button-primary">Add Lead</button>
            </form>
        </div>
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
