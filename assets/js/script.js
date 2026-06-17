jQuery(document).ready(function($) {
    // Add Lead Modal
    const modal = $('#add-lead-modal');
    const addBtn = $('#add-lead-btn');
    const closeBtn = $('.close');

    addBtn.on('click', function() {
        modal.show();
    });

    closeBtn.on('click', function() {
        modal.hide();
    });

    $(window).on('click', function(event) {
        if (event.target == modal[0]) {
            modal.hide();
        }
    });

    // Add Lead Form Submission
    $('#add-lead-form').on('submit', function(e) {
        e.preventDefault();
        
        const formData = $(this).serializeArray();
        formData.push({name: 'action', value: 'add_lead'});
        formData.push({name: 'nonce', value: yantramCRM.nonce});

        $.ajax({
            url: yantramCRM.ajax_url,
            type: 'POST',
            data: formData,
            success: function(response) {
                if (response.success) {
                    alert('Lead added successfully!');
                    modal.hide();
                    location.reload();
                } else {
                    alert('Error adding lead');
                }
            }
        });
    });

    // Edit Lead
    $(document).on('click', '.edit-lead', function() {
        const leadId = $(this).data('id');
        // TODO: Implement edit functionality
        alert('Edit functionality - Lead ID: ' + leadId);
    });

    // Delete Lead
    $(document).on('click', '.delete-lead', function() {
        const leadId = $(this).data('id');
        if (confirm('Are you sure you want to delete this lead?')) {
            $.ajax({
                url: yantramCRM.ajax_url,
                type: 'POST',
                data: {
                    action: 'delete_lead',
                    id: leadId,
                    nonce: yantramCRM.nonce
                },
                success: function(response) {
                    if (response.success) {
                        alert('Lead deleted successfully!');
                        location.reload();
                    }
                }
            });
        }
    });

    // Search Leads
    $('#lead-search').on('keyup', function() {
        const query = $(this).val();
        if (query.length > 2) {
            $.ajax({
                url: yantramCRM.ajax_url,
                type: 'POST',
                data: {
                    action: 'search_leads',
                    query: query,
                    nonce: yantramCRM.nonce
                },
                success: function(response) {
                    if (response.success) {
                        // TODO: Update table with search results
                        console.log(response.data);
                    }
                }
            });
        }
    });
});
