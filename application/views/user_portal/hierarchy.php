<style>
.hierarchy-container {
    min-height: 600px;
    background: #f8f9fa;
    border-radius: 8px;
    padding: 20px;
    overflow: hidden;
    position: relative;
}

.zoom-controls {
    position: absolute;
    top: 10px;
    right: 10px;
    z-index: 100;
    background: white;
    border-radius: 8px;
    padding: 5px;
    box-shadow: 0 2px 10px rgba(0,0,0,0.1);
}

.zoom-controls button {
    width: 36px;
    height: 36px;
    border: none;
    background: #f8f9fa;
    border-radius: 4px;
    margin: 2px;
    cursor: pointer;
    font-size: 16px;
}

.zoom-controls button:hover {
    background: #e9ecef;
}

.zoom-controls .zoom-level {
    display: inline-block;
    min-width: 50px;
    text-align: center;
    font-size: 12px;
    font-weight: 600;
}

.tree-viewport {
    width: 100%;
    height: 550px;
    overflow: auto;
    cursor: grab;
    position: relative;
}

.tree-viewport:active {
    cursor: grabbing;
}

.tree-viewport-inner {
    transform-origin: center center;
    transition: transform 0.1s ease;
    min-width: 2000px;
    min-height: 1500px;
    padding: 100px 500px;
    display: inline-block;
}

.hierarchy-node {
    display: inline-block;
    background: #fff;
    border: 2px solid #1572e8;
    border-radius: 8px;
    padding: 10px 15px;
    margin: 5px;
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

<div class="card-custom">
    <div class="card-header">
        <div class="d-flex align-items-center justify-content-between">
            <h5>
                <i class="fas fa-sitemap text-primary mr-2"></i>
                Organization Hierarchy Chart
            </h5>
            <div class="d-flex align-items-center">
                <span class="badge badge-info mr-2"><i class="fas fa-eye mr-1"></i> View Only</span>
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
    <div class="card-body p-0">
        <div class="hierarchy-container" id="hierarchyContainer">
            <div class="zoom-controls">
                <button onclick="zoomIn()" title="Zoom In"><i class="fas fa-plus"></i></button>
                <span class="zoom-level" id="zoomLevel">100%</span>
                <button onclick="zoomOut()" title="Zoom Out"><i class="fas fa-minus"></i></button>
                <button onclick="resetZoom()" title="Reset"><i class="fas fa-compress-arrows-alt"></i></button>
            </div>
            <div class="tree-viewport" id="treeViewport">
                <div class="tree-viewport-inner" id="treeViewportInner">
                    <div class="org-chart" id="orgChart">
                        <!-- Tree will be rendered here -->
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
var hierarchyTreeData = <?= json_encode(isset($tree) ? $tree : []) ?>;

(function checkJQuery() {
    if (typeof jQuery !== 'undefined') {
        initViewOnlyHierarchy();
    } else {
        setTimeout(checkJQuery, 50);
    }
})();

function initViewOnlyHierarchy() {
$(document).ready(function() {
    function renderTree() {
        const container = $('#orgChart');
        container.empty();

        if (!hierarchyTreeData || hierarchyTreeData.length === 0) {
            container.html(`
                <div class="empty-tree-message">
                    <i class="fas fa-sitemap"></i>
                    <h5>No Hierarchy Defined Yet</h5>
                    <p>The organization hierarchy has not been set up by the administrator yet.</p>
                </div>
            `);
        } else {
            const html = buildTreeHtml(hierarchyTreeData);
            container.html('<ul>' + html + '</ul>');
        }
    }

    function buildTreeHtml(nodes) {
        let html = '';
        nodes.forEach(node => {
            const initials = getInitials(node.firstname, node.lastname);
            const fullName = node.lastname + ', ' + node.firstname;
            const hasChildren = node.children && node.children.length > 0;
            const profileImg = node.profile_image 
                ? '<img src="<?= base_url("assets/uploads/profile_images/") ?>' + node.profile_image + '" alt="' + fullName + '">'
                : initials;

            html += '<li>';
            html += '<div class="hierarchy-node">';
            html += '<div class="node-avatar">' + profileImg + '</div>';
            html += '<div class="node-name">' + fullName + '</div>';
            html += '<div class="node-position">' + (node.position || 'No Position') + '</div>';
            html += '</div>';
            if (hasChildren) {
                html += '<ul>' + buildTreeHtml(node.children) + '</ul>';
            }
            html += '</li>';
        });
        return html;
    }

    function getInitials(firstname, lastname) {
        return ((firstname ? firstname[0] : '') + (lastname ? lastname[0] : '')).toUpperCase();
    }

    renderTree();
    initPanZoom();

    setTimeout(function() {
        var viewport = document.getElementById('treeViewport');
        var inner = document.getElementById('treeViewportInner');
        if (viewport && inner) {
            viewport.scrollLeft = (inner.scrollWidth - viewport.clientWidth) / 2;
            viewport.scrollTop = 50;
        }
    }, 100);
});
}

var currentZoom = 1;
var minZoom = 0.5;
var maxZoom = 2;
var zoomStep = 0.1;

function zoomIn() {
    if (currentZoom < maxZoom) {
        currentZoom = Math.min(currentZoom + zoomStep, maxZoom);
        applyZoom();
    }
}

function zoomOut() {
    if (currentZoom > minZoom) {
        currentZoom = Math.max(currentZoom - zoomStep, minZoom);
        applyZoom();
    }
}

function resetZoom() {
    currentZoom = 1;
    applyZoom();
    var viewport = document.getElementById('treeViewport');
    var inner = document.getElementById('treeViewportInner');
    viewport.scrollLeft = (inner.scrollWidth - viewport.clientWidth) / 2;
    viewport.scrollTop = 50;
}

function applyZoom() {
    var inner = document.getElementById('treeViewportInner');
    inner.style.transform = 'scale(' + currentZoom + ')';
    document.getElementById('zoomLevel').textContent = Math.round(currentZoom * 100) + '%';
}

function initPanZoom() {
    var viewport = document.getElementById('treeViewport');
    var isPanning = false;
    var startX, startY, scrollLeft, scrollTop;

    viewport.addEventListener('mousedown', function(e) {
        if (e.target.closest('button')) return;
        isPanning = true;
        viewport.style.cursor = 'grabbing';
        startX = e.pageX - viewport.offsetLeft;
        startY = e.pageY - viewport.offsetTop;
        scrollLeft = viewport.scrollLeft;
        scrollTop = viewport.scrollTop;
        e.preventDefault();
    });

    viewport.addEventListener('mouseleave', function() {
        isPanning = false;
        viewport.style.cursor = 'grab';
    });

    viewport.addEventListener('mouseup', function() {
        isPanning = false;
        viewport.style.cursor = 'grab';
    });

    viewport.addEventListener('mousemove', function(e) {
        if (!isPanning) return;
        e.preventDefault();
        var x = e.pageX - viewport.offsetLeft;
        var y = e.pageY - viewport.offsetTop;
        var walkX = (x - startX) * 1.5;
        var walkY = (y - startY) * 1.5;
        viewport.scrollLeft = scrollLeft - walkX;
        viewport.scrollTop = scrollTop - walkY;
    });

    viewport.addEventListener('wheel', function(e) {
        if (e.ctrlKey) {
            e.preventDefault();
            if (e.deltaY < 0) {
                zoomIn();
            } else {
                zoomOut();
            }
        }
    }, { passive: false });
}
</script>
