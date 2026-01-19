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

    <?php if (!empty($policies)): ?>
        <div class="row g-4">
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
        </div>
    <?php else: ?>
        <div class="text-center py-5 animate-in">
            <div class="mb-3">
                <i class="fas fa-folder-open fa-4x text-muted opacity-25"></i>
            </div>
            <h5 class="text-muted">No policies available at the moment.</h5>
        </div>
    <?php endif; ?>
</div>

<?php
if ($_SESSION['role_code'] === 'PARTNER_ADMIN') {
    view('layouts/partner_footer');
} else {
    view('layouts/footer');
}
?>
