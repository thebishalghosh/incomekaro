<?php
if ($_SESSION['role_code'] === 'PARTNER_ADMIN') {
    view('layouts/partner_header', ['title' => 'Policies']);
} else {
    view('layouts/header', ['title' => 'Policies']);
}
?>

<style>
    :root {
        --primary-color: <?php echo get_primary_color(); ?>;
        --secondary-color: <?php echo get_secondary_color(); ?>;
        --border-radius: 16px;
        --transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    }

    .policy-card {
        background: white;
        border-radius: var(--border-radius);
        padding: 24px;
        border: 1px solid rgba(0,0,0,0.05);
        box-shadow: 0 2px 4px rgba(0,0,0,0.02);
        transition: var(--transition);
        height: 100%;
        display: flex;
        flex-direction: column;
        align-items: center;
        text-align: center;
        position: relative;
        overflow: hidden;
    }

    .policy-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 12px 24px rgba(0,0,0,0.08);
        border-color: rgba(var(--primary-rgb), 0.2);
    }

    .icon-wrapper {
        width: 64px;
        height: 64px;
        border-radius: 50%;
        background: #fef2f2; /* Light Red for PDF */
        color: #ef4444;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.75rem;
        margin-bottom: 16px;
        transition: var(--transition);
    }

    .policy-card:hover .icon-wrapper {
        background: #ef4444;
        color: white;
        transform: scale(1.1) rotate(-5deg);
    }

    .policy-type {
        font-size: 0.75rem;
        text-transform: uppercase;
        letter-spacing: 1px;
        font-weight: bold;
        color: #94a3b8;
        margin-bottom: 8px;
    }

    .policy-title {
        font-weight: bold;
        color: #1e293b;
        margin-bottom: 20px;
        flex-grow: 1;
    }

    .btn-view {
        background-color: var(--primary-color);
        color: white;
        border: none;
        border-radius: 50px;
        padding: 8px 24px;
        font-weight: 600;
        font-size: 0.9rem;
        transition: var(--transition);
        width: 100%;
        text-decoration: none;
    }

    .btn-view:hover {
        background-color: var(--secondary-color);
        color: white;
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(var(--primary-rgb), 0.3);
    }

    /* Animations */
    @keyframes fadeInUp {
        from { opacity: 0; transform: translateY(20px); }
        to { opacity: 1; transform: translateY(0); }
    }

    .animate-in {
        animation: fadeInUp 0.6s ease-out forwards;
        opacity: 0;
    }
</style>

<div class="container-fluid p-4" style="max-width: 1400px;">
    <div class="text-center mb-5 animate-in">
        <h2 class="fw-bold text-dark">Company Policies</h2>
        <p class="text-muted">Access all important policy documents and guidelines.</p>
    </div>

    <!-- Filter Section -->
    <div class="row justify-content-center mb-5 animate-in" style="animation-delay: 0.1s;">
        <div class="col-md-8">
            <div class="card border-0 shadow-sm p-3 rounded-4">
                <div class="row g-3">
                    <div class="col-md-5">
                        <div class="input-group">
                            <span class="input-group-text bg-white border-end-0"><i class="fas fa-search text-muted"></i></span>
                            <input type="text" class="form-control border-start-0 ps-0" id="policySearch" placeholder="Search policies...">
                        </div>
                    </div>
                    <div class="col-md-4">
                        <select class="form-select" id="typeFilter">
                            <option value="">All Types</option>
                            <option value="Bank">Bank</option>
                            <option value="Credit Card">Credit Card</option>
                            <option value="Account Opening">Account Opening</option>
                            <option value="Insurance">Insurance</option>
                            <option value="Other">Other</option>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <button class="btn btn-outline-secondary w-100" onclick="resetFilters()">
                            <i class="fas fa-undo me-2"></i> Reset
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row g-4" id="policyGrid">
        <?php if (!empty($policies)): ?>
            <?php foreach ($policies as $index => $policy): ?>
                <div class="col-xl-3 col-lg-4 col-md-6 animate-in" style="animation-delay: <?php echo $index * 0.1; ?>s;">
                    <div class="policy-card">
                        <div class="icon-wrapper">
                            <i class="fas fa-file-pdf"></i>
                        </div>
                        <div class="policy-type"><?php echo $policy['type']; ?></div>
                        <h5 class="policy-title"><?php echo $policy['name']; ?></h5>
                        <a href="<?php echo asset($policy['file_url']); ?>" target="_blank" class="btn-view">
                            View Document <i class="fas fa-arrow-right ms-2"></i>
                        </a>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php else: ?>
            <div class="col-12 text-center py-5 animate-in">
                <div class="mb-3">
                    <i class="fas fa-folder-open fa-4x text-muted opacity-25"></i>
                </div>
                <h5 class="text-muted">No policies available at the moment.</h5>
            </div>
        <?php endif; ?>
    </div>
</div>

<script>
    const assetBaseUrl = "<?php echo asset(''); ?>";
    let searchTimeout;

    document.addEventListener('DOMContentLoaded', function() {
        const searchInput = document.getElementById('policySearch');
        const typeSelect = document.getElementById('typeFilter');

        searchInput.addEventListener('input', function() {
            clearTimeout(searchTimeout);
            searchTimeout = setTimeout(loadPolicies, 500);
        });

        typeSelect.addEventListener('change', loadPolicies);
    });

    function resetFilters() {
        document.getElementById('policySearch').value = '';
        document.getElementById('typeFilter').value = '';
        loadPolicies();
    }

    function loadPolicies() {
        const search = document.getElementById('policySearch').value;
        const type = document.getElementById('typeFilter').value;
        const grid = document.getElementById('policyGrid');

        // Show loading state (optional, but good UX)
        grid.style.opacity = '0.5';

        fetch(`<?php echo url('policy/list'); ?>?ajax=1&search=${encodeURIComponent(search)}&type=${encodeURIComponent(type)}`)
            .then(response => response.json())
            .then(data => {
                renderPolicies(data.policies);
                grid.style.opacity = '1';
            })
            .catch(error => {
                console.error('Error:', error);
                grid.innerHTML = '<div class="col-12 text-center text-danger py-5">Error loading policies.</div>';
                grid.style.opacity = '1';
            });
    }

    function renderPolicies(policies) {
        const grid = document.getElementById('policyGrid');
        grid.innerHTML = '';

        if (policies.length === 0) {
            grid.innerHTML = `
                <div class="col-12 text-center py-5 animate-in">
                    <div class="mb-3">
                        <i class="fas fa-search fa-4x text-muted opacity-25"></i>
                    </div>
                    <h5 class="text-muted">No policies found matching your criteria.</h5>
                </div>
            `;
            return;
        }

        policies.forEach((policy, index) => {
            const delay = index * 0.1;
            const card = `
                <div class="col-xl-3 col-lg-4 col-md-6 animate-in" style="animation-delay: ${delay}s;">
                    <div class="policy-card">
                        <div class="icon-wrapper">
                            <i class="fas fa-file-pdf"></i>
                        </div>
                        <div class="policy-type">${policy.type}</div>
                        <h5 class="policy-title">${policy.name}</h5>
                        <a href="${assetBaseUrl}${policy.file_url}" target="_blank" class="btn-view">
                            View Document <i class="fas fa-arrow-right ms-2"></i>
                        </a>
                    </div>
                </div>
            `;
            grid.innerHTML += card;
        });
    }
</script>

<?php
if ($_SESSION['role_code'] === 'PARTNER_ADMIN') {
    view('layouts/partner_footer');
} else {
    view('layouts/footer');
}
?>
