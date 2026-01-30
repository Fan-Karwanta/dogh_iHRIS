<style>
.hierarchy-container {
    min-height: 600px;
    background: #f8f9fa;
    border-radius: 8px;
    padding: 20px;
    overflow: auto;
}

.tree-canvas {
    min-height: 500px;
    position: relative;
}

.hierarchy-node {
    display: inline-block;
    background: #fff;
    border: 2px solid #1572e8;
    border-radius: 8px;
    padding: 10px 15px;
    margin: 5px;
    cursor: move;
    min-width: 150px;
    text-align: center;
    box-shadow: 0 2px 5px rgba(0,0,0,0.1);
    transition: all 0.3s ease;
    position: relative;
}

.hierarchy-node:hover {
    box-shadow: 0 4px 15px rgba(21, 114, 232, 0.3);
    transform: translateY(-2px);
}

.hierarchy-node.dragging {
    opacity: 0.5;
    border-style: dashed;
}

.hierarchy-node.drag-over {
    border-color: #31ce36;
    background: #e8f5e9;
}

.hierarchy-node .node-name {
    font-weight: 600;
    color: #1a2035;
    font-size: 14px;
}

.hierarchy-node .node-position {
    font-size: 11px;
    color: #6c757d;
    margin-top: 3px;
}

.hierarchy-node .node-actions {
    position: absolute;
    top: -10px;
    right: -10px;
    display: none;
}

.hierarchy-node:hover .node-actions {
    display: block;
}

.hierarchy-node .btn-remove {
    width: 24px;
    height: 24px;
    padding: 0;
    border-radius: 50%;
    font-size: 12px;
    line-height: 24px;
}

.hierarchy-node .node-avatar {
    width: 40px;
    height: 40px;
    border-radius: 50%;
    margin: 0 auto 8px;
    background: #1572e8;
    color: #fff;
    display: flex;
    align-items: center;
    justify-content: center;
    font-weight: 600;
    font-size: 16px;
}

.hierarchy-node .node-avatar img {
    width: 100%;
    height: 100%;
    border-radius: 50%;
    object-fit: cover;
}

.tree-level {
    display: flex;
    justify-content: center;
    flex-wrap: wrap;
    margin-bottom: 30px;
    position: relative;
}

.tree-level::before {
    content: '';
    position: absolute;
    top: -15px;
    left: 50%;
    width: 2px;
    height: 15px;
    background: #dee2e6;
}

.tree-level:first-child::before {
    display: none;
}

.tree-branch {
    display: flex;
    flex-direction: column;
    align-items: center;
    margin: 0 10px;
}

.tree-children {
    display: flex;
    justify-content: center;
    flex-wrap: wrap;
    position: relative;
    padding-top: 20px;
}

.tree-children::before {
    content: '';
    position: absolute;
    top: 0;
    left: 50%;
    width: 2px;
    height: 20px;
    background: #dee2e6;
}

.tree-children .tree-branch {
    position: relative;
}

.tree-children .tree-branch::before {
    content: '';
    position: absolute;
    top: -20px;
    left: 50%;
    width: 2px;
    height: 20px;
    background: #dee2e6;
}

.drop-zone {
    min-height: 80px;
    border: 2px dashed #dee2e6;
    border-radius: 8px;
    display: flex;
    align-items: center;
    justify-content: center;
    color: #6c757d;
    margin: 10px;
    padding: 20px;
    transition: all 0.3s ease;
}

.drop-zone.drag-over {
    border-color: #31ce36;
    background: #e8f5e9;
    color: #31ce36;
}

.personnel-list {
    max-height: 400px;
    overflow-y: auto;
}

.personnel-item {
    display: flex;
    align-items: center;
    padding: 10px;
    border: 1px solid #dee2e6;
    border-radius: 6px;
    margin-bottom: 8px;
    cursor: pointer;
    transition: all 0.2s ease;
}

.personnel-item:hover {
    background: #f8f9fa;
    border-color: #1572e8;
}

.personnel-item .avatar {
    width: 35px;
    height: 35px;
    border-radius: 50%;
    background: #1572e8;
    color: #fff;
    display: flex;
    align-items: center;
    justify-content: center;
    font-weight: 600;
    margin-right: 10px;
    font-size: 14px;
}

.personnel-item .avatar img {
    width: 100%;
    height: 100%;
    border-radius: 50%;
    object-fit: cover;
}

.level-indicator {
    position: absolute;
    left: 10px;
    top: 50%;
    transform: translateY(-50%);
    background: #6c757d;
    color: #fff;
    padding: 5px 10px;
    border-radius: 4px;
    font-size: 11px;
    font-weight: 600;
}

.org-chart {
    display: flex;
    flex-direction: column;
    align-items: center;
}

.org-chart ul {
    padding-top: 20px;
    position: relative;
    transition: all 0.5s;
    display: flex;
    justify-content: center;
}

.org-chart li {
    float: left;
    text-align: center;
    list-style-type: none;
    position: relative;
    padding: 20px 5px 0 5px;
    transition: all 0.5s;
}

.org-chart li::before,
.org-chart li::after {
    content: '';
    position: absolute;
    top: 0;
    right: 50%;
    border-top: 2px solid #ccc;
    width: 50%;
    height: 20px;
}

.org-chart li::after {
    right: auto;
    left: 50%;
    border-left: 2px solid #ccc;
}

.org-chart li:only-child::after,
.org-chart li:only-child::before {
    display: none;
}

.org-chart li:only-child {
    padding-top: 0;
}

.org-chart li:first-child::before,
.org-chart li:last-child::after {
    border: 0 none;
}

.org-chart li:last-child::before {
    border-right: 2px solid #ccc;
    border-radius: 0 5px 0 0;
}

.org-chart li:first-child::after {
    border-radius: 5px 0 0 0;
}

.org-chart ul ul::before {
    content: '';
    position: absolute;
    top: 0;
    left: 50%;
    border-left: 2px solid #ccc;
    width: 0;
    height: 20px;
}

.empty-tree-message {
    text-align: center;
    padding: 60px 20px;
    color: #6c757d;
}

.empty-tree-message i {
    font-size: 64px;
    margin-bottom: 20px;
    opacity: 0.5;
}

.legend-item {
    display: flex;
    align-items: center;
    margin-right: 20px;
    font-size: 12px;
}

.legend-item .dot {
    width: 12px;
    height: 12px;
    border-radius: 50%;
    margin-right: 6px;
}

.legend-item .dot.approver {
    background: #1572e8;
}

.legend-item .dot.approvee {
    background: #31ce36;
}
</style>

<div class="content">
    <div class="page-inner">
        <div class="page-header">
            <h4 class="page-title">Hierarchy Approval</h4>
            <ul class="breadcrumbs">
                <li class="nav-home">
                    <a href="<?= site_url('dashboard') ?>"><i class="flaticon-home"></i></a>
                </li>
                <li class="separator"><i class="flaticon-right-arrow"></i></li>
                <li class="nav-item"><a href="#">Hierarchy Approval</a></li>
            </ul>
        </div>

        <?php if (isset($message) && $message): ?>
            <div class="alert alert-<?= isset($success) && $success ? 'success' : 'danger' ?> alert-dismissible fade show">
                <?= $message ?>
                <button type="button" class="close" data-dismiss="alert">&times;</button>
            </div>
        <?php endif; ?>

        <div class="row">
            <div class="col-md-9">
                <div class="card">
                    <div class="card-header">
                        <div class="d-flex align-items-center justify-content-between">
                            <h4 class="card-title">
                                <i class="fas fa-sitemap text-primary mr-2"></i>
                                Approval Hierarchy Tree
                            </h4>
                            <div class="d-flex align-items-center">
                                <div class="legend-item">
                                    <span class="dot approver"></span>
                                    <span>Approver (Above)</span>
                                </div>
                                <div class="legend-item">
                                    <span class="dot approvee"></span>
                                    <span>Approvee (Below)</span>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="hierarchy-container" id="hierarchyContainer">
                            <div class="org-chart" id="orgChart">
                                <!-- Tree will be rendered here -->
                            </div>
                            <div class="drop-zone root-drop-zone" id="rootDropZone" style="display: none;">
                                <i class="fas fa-plus-circle mr-2"></i>
                                Drop here to add as root node
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-md-3">
                <div class="card">
                    <div class="card-header">
                        <h4 class="card-title">
                            <i class="fas fa-users text-info mr-2"></i>
                            Available Personnel
                        </h4>
                    </div>
                    <div class="card-body">
                        <div class="form-group">
                            <input type="text" class="form-control" id="searchPersonnel" placeholder="Search personnel...">
                        </div>
                        <div class="personnel-list" id="personnelList">
                            <!-- Personnel will be loaded here -->
                        </div>
                        <div class="text-center mt-3" id="noPersonnelMessage" style="display: none;">
                            <p class="text-muted mb-0">All personnel are in the hierarchy</p>
                        </div>
                        <div class="text-center mt-3" id="loadingPersonnel" style="display: none;">
                            <i class="fas fa-spinner fa-spin"></i> Loading personnel...
                        </div>
                        <div class="text-center mt-3" id="noPersonnelInDb" style="display: none;">
                            <p class="text-warning mb-2"><i class="fas fa-exclamation-triangle"></i></p>
                            <p class="text-muted mb-0">No personnel found in database. Please add personnel first.</p>
                            <a href="<?= site_url('admin/personnel') ?>" class="btn btn-sm btn-primary mt-2">
                                <i class="fas fa-users mr-1"></i> Go to Personnel
                            </a>
                        </div>
                    </div>
                </div>

                <div class="card">
                    <div class="card-header">
                        <h4 class="card-title">
                            <i class="fas fa-info-circle text-warning mr-2"></i>
                            Instructions
                        </h4>
                    </div>
                    <div class="card-body">
                        <ul class="pl-3 mb-0" style="font-size: 13px;">
                            <li class="mb-2">Drag personnel from the list to the tree</li>
                            <li class="mb-2">Drop on a node to make them a subordinate</li>
                            <li class="mb-2">Drop on empty area for root position</li>
                            <li class="mb-2">Drag nodes within tree to reorganize</li>
                            <li class="mb-2">Click <i class="fas fa-times text-danger"></i> to remove from hierarchy</li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
// Store data in global scope first, then initialize when jQuery is ready
var hierarchyData = {
    tree: <?= json_encode(isset($tree) ? $tree : []) ?>,
    availablePersonnel: <?= json_encode(isset($available_personnel) ? $available_personnel : []) ?>,
    allPersonnel: <?= json_encode(isset($all_personnel) ? $all_personnel : []) ?>
};

// Wait for jQuery to be available
(function checkJQuery() {
    if (typeof jQuery !== 'undefined') {
        initHierarchyModule();
    } else {
        setTimeout(checkJQuery, 50);
    }
})();

function initHierarchyModule() {
$(document).ready(function() {
    let treeData = hierarchyData.tree;
    let availablePersonnel = hierarchyData.availablePersonnel;
    let allPersonnel = hierarchyData.allPersonnel;
    let draggedElement = null;
    let draggedType = null;
    let draggedData = null;
    
    console.log('Tree Data:', treeData);
    console.log('Available Personnel:', availablePersonnel);
    console.log('All Personnel:', allPersonnel);

    function renderTree() {
        const container = $('#orgChart');
        container.empty();

        if (treeData.length === 0) {
            container.html(`
                <div class="empty-tree-message">
                    <i class="fas fa-sitemap"></i>
                    <h5>No Hierarchy Defined</h5>
                    <p>Drag personnel from the right panel to start building your approval hierarchy</p>
                </div>
            `);
            $('#rootDropZone').show();
        } else {
            const html = buildTreeHtml(treeData);
            container.html('<ul>' + html + '</ul>');
            $('#rootDropZone').hide();
        }

        initDragDrop();
    }

    function buildTreeHtml(nodes) {
        let html = '';
        nodes.forEach(node => {
            const initials = getInitials(node.firstname, node.lastname);
            const fullName = `${node.lastname}, ${node.firstname}`;
            const hasChildren = node.children && node.children.length > 0;
            const profileImg = node.profile_image 
                ? `<img src="<?= base_url('assets/uploads/profile_images/') ?>${node.profile_image}" alt="${fullName}">`
                : initials;

            html += `
                <li>
                    <div class="hierarchy-node" draggable="true" data-node-id="${node.id}" data-personnel-id="${node.personnel_id}">
                        <div class="node-actions">
                            <button class="btn btn-danger btn-remove" onclick="removeNode(${node.id})" title="Remove from hierarchy">
                                <i class="fas fa-times"></i>
                            </button>
                        </div>
                        <div class="node-avatar">${profileImg}</div>
                        <div class="node-name">${fullName}</div>
                        <div class="node-position">${node.position || 'No Position'}</div>
                    </div>
                    ${hasChildren ? '<ul>' + buildTreeHtml(node.children) + '</ul>' : ''}
                </li>
            `;
        });
        return html;
    }

    function renderPersonnelList() {
        const container = $('#personnelList');
        container.empty();
        
        console.log('Available Personnel:', availablePersonnel);
        console.log('Personnel count:', availablePersonnel ? availablePersonnel.length : 0);

        if (!availablePersonnel || availablePersonnel.length === 0) {
            // Check if it's because all are in hierarchy or no personnel exist
            if (treeData && treeData.length > 0) {
                $('#noPersonnelMessage').show();
            } else {
                $('#noPersonnelInDb').show();
            }
            return;
        }

        $('#noPersonnelMessage').hide();
        $('#noPersonnelInDb').hide();

        availablePersonnel.forEach(person => {
            const initials = getInitials(person.firstname, person.lastname);
            const fullName = `${person.lastname}, ${person.firstname}`;
            const profileImg = person.profile_image 
                ? `<img src="<?= base_url('assets/uploads/profile_images/') ?>${person.profile_image}" alt="${fullName}">`
                : initials;

            container.append(`
                <div class="personnel-item" draggable="true" data-personnel-id="${person.id}">
                    <div class="avatar">${profileImg}</div>
                    <div>
                        <div style="font-weight: 600; font-size: 13px;">${fullName}</div>
                        <div style="font-size: 11px; color: #6c757d;">${person.position || 'No Position'}</div>
                    </div>
                </div>
            `);
        });

        initPersonnelDrag();
    }

    function getInitials(firstname, lastname) {
        return ((firstname ? firstname[0] : '') + (lastname ? lastname[0] : '')).toUpperCase();
    }

    function initDragDrop() {
        $('.hierarchy-node').on('dragstart', function(e) {
            draggedElement = this;
            draggedType = 'node';
            draggedData = {
                nodeId: $(this).data('node-id'),
                personnelId: $(this).data('personnel-id')
            };
            $(this).addClass('dragging');
            e.originalEvent.dataTransfer.effectAllowed = 'move';
        });

        $('.hierarchy-node').on('dragend', function(e) {
            $(this).removeClass('dragging');
            $('.hierarchy-node').removeClass('drag-over');
            $('.drop-zone').removeClass('drag-over');
            draggedElement = null;
            draggedType = null;
            draggedData = null;
        });

        $('.hierarchy-node').on('dragover', function(e) {
            e.preventDefault();
            if (draggedElement !== this) {
                $(this).addClass('drag-over');
            }
        });

        $('.hierarchy-node').on('dragleave', function(e) {
            $(this).removeClass('drag-over');
        });

        $('.hierarchy-node').on('drop', function(e) {
            e.preventDefault();
            $(this).removeClass('drag-over');

            const targetNodeId = $(this).data('node-id');

            if (draggedType === 'node') {
                if (draggedData.nodeId !== targetNodeId) {
                    moveNode(draggedData.nodeId, targetNodeId);
                }
            } else if (draggedType === 'personnel') {
                addNode(draggedData.personnelId, targetNodeId);
            }
        });

        $('#rootDropZone, #hierarchyContainer').on('dragover', function(e) {
            e.preventDefault();
            if (e.target.id === 'rootDropZone' || ($(e.target).closest('.hierarchy-node').length === 0 && e.target.id === 'hierarchyContainer')) {
                $('#rootDropZone').addClass('drag-over');
            }
        });

        $('#rootDropZone, #hierarchyContainer').on('dragleave', function(e) {
            $('#rootDropZone').removeClass('drag-over');
        });

        $('#rootDropZone').on('drop', function(e) {
            e.preventDefault();
            $(this).removeClass('drag-over');

            if (draggedType === 'node') {
                moveNode(draggedData.nodeId, null);
            } else if (draggedType === 'personnel') {
                addNode(draggedData.personnelId, null);
            }
        });

        $('#hierarchyContainer').on('drop', function(e) {
            if ($(e.target).closest('.hierarchy-node').length === 0) {
                e.preventDefault();
                $('#rootDropZone').removeClass('drag-over');

                if (draggedType === 'personnel') {
                    addNode(draggedData.personnelId, null);
                }
            }
        });
    }

    function initPersonnelDrag() {
        $('.personnel-item').on('dragstart', function(e) {
            draggedElement = this;
            draggedType = 'personnel';
            draggedData = {
                personnelId: $(this).data('personnel-id')
            };
            $(this).css('opacity', '0.5');
            e.originalEvent.dataTransfer.effectAllowed = 'copy';
            $('#rootDropZone').show();
        });

        $('.personnel-item').on('dragend', function(e) {
            $(this).css('opacity', '1');
            $('.hierarchy-node').removeClass('drag-over');
            $('.drop-zone').removeClass('drag-over');
            if (treeData.length > 0) {
                $('#rootDropZone').hide();
            }
            draggedElement = null;
            draggedType = null;
            draggedData = null;
        });
    }

    function addNode(personnelId, parentId) {
        $.ajax({
            url: '<?= site_url('hierarchyapproval/add_node') ?>',
            type: 'POST',
            data: {
                personnel_id: personnelId,
                parent_id: parentId
            },
            dataType: 'json',
            success: function(response) {
                if (response.success) {
                    showNotification('success', response.message);
                    refreshData();
                } else {
                    showNotification('error', response.message);
                }
            },
            error: function() {
                showNotification('error', 'Failed to add node');
            }
        });
    }

    function moveNode(nodeId, newParentId) {
        $.ajax({
            url: '<?= site_url('hierarchyapproval/move_node') ?>',
            type: 'POST',
            data: {
                node_id: nodeId,
                new_parent_id: newParentId,
                new_position: 0
            },
            dataType: 'json',
            success: function(response) {
                if (response.success) {
                    showNotification('success', response.message);
                    refreshData();
                } else {
                    showNotification('error', response.message);
                }
            },
            error: function() {
                showNotification('error', 'Failed to move node');
            }
        });
    }

    window.removeNode = function(nodeId) {
        if (!confirm('Are you sure you want to remove this person from the hierarchy? Their subordinates will be moved up.')) {
            return;
        }

        $.ajax({
            url: '<?= site_url('hierarchyapproval/delete_node') ?>',
            type: 'POST',
            data: {
                node_id: nodeId
            },
            dataType: 'json',
            success: function(response) {
                if (response.success) {
                    showNotification('success', response.message);
                    refreshData();
                } else {
                    showNotification('error', response.message);
                }
            },
            error: function() {
                showNotification('error', 'Failed to remove node');
            }
        });
    };

    function refreshData() {
        $.ajax({
            url: '<?= site_url('hierarchyapproval/get_tree') ?>',
            type: 'GET',
            dataType: 'json',
            success: function(response) {
                if (response.success) {
                    treeData = response.data;
                    renderTree();
                }
            }
        });

        $.ajax({
            url: '<?= site_url('hierarchyapproval/get_available_personnel') ?>',
            type: 'GET',
            dataType: 'json',
            success: function(response) {
                if (response.success) {
                    availablePersonnel = response.data;
                    renderPersonnelList();
                }
            }
        });
    }

    function showNotification(type, message) {
        const alertClass = type === 'success' ? 'alert-success' : 'alert-danger';
        const icon = type === 'success' ? 'fa-check-circle' : 'fa-exclamation-circle';
        
        const alert = $(`
            <div class="alert ${alertClass} alert-dismissible fade show" style="position: fixed; top: 20px; right: 20px; z-index: 9999; min-width: 300px;">
                <i class="fas ${icon} mr-2"></i>
                ${message}
                <button type="button" class="close" data-dismiss="alert">&times;</button>
            </div>
        `);

        $('body').append(alert);

        setTimeout(function() {
            alert.fadeOut(function() {
                $(this).remove();
            });
        }, 3000);
    }

    $('#searchPersonnel').on('input', function() {
        const query = $(this).val().toLowerCase();
        $('.personnel-item').each(function() {
            const text = $(this).text().toLowerCase();
            $(this).toggle(text.includes(query));
        });
    });

    renderTree();
    renderPersonnelList();
});
} // End initHierarchyModule
</script>
