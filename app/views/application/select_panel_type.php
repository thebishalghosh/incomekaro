<?php view('layouts/partner_header', ['title' => 'Select Panel Type']); ?>

<style>
    :root {
        --primary-color: <?php echo get_primary_color(); ?>;
        --secondary-color: <?php echo get_secondary_color(); ?>;
        --text-dark: #0f172a;
        --text-muted: #64748b;
        --border-radius: 20px;
        --transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
    }

    body {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 25%, #f093fb 50%, #4facfe 75%, #00f2fe 100%);
        background-size: 400% 400%;
        animation: gradientShift 15s ease infinite;
    }

    @keyframes gradientShift {
        0% { background-position: 0% 50%; }
        50% { background-position: 100% 50%; }
        100% { background-position: 0% 50%; }
    }

    @keyframes fadeInUp {
        from { 
            opacity: 0; 
            transform: translateY(30px); 
        }
        to { 
            opacity: 1; 
            transform: translateY(0); 
        }
    }

    .page-header {
        text-align: center;
        margin-bottom: 50px;
        animation: fadeInUp 0.8s ease-out forwards;
    }

    .page-header h1 {
        font-size: 2.5rem;
        font-weight: 800;
        color: var(--text-dark);
        margin-bottom: 12px;
        letter-spacing: -0.5px;
    }

    .page-header .lead {
        font-size: 1.1rem;
        color: #475569;
        font-weight: 500;
    }

    .service-card {
        background: linear-gradient(135deg, rgba(255, 255, 255, 0.95) 0%, rgba(255, 255, 255, 0.9) 100%);
        border-radius: var(--border-radius);
        border: 2px solid rgba(255, 255, 255, 0.5);
        box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1), 0 0 0 1px rgba(255, 255, 255, 0.5);
        transition: var(--transition);
        height: 100%;
        display: flex;
        flex-direction: column;
        padding: 32px 24px;
        position: relative;
        overflow: hidden;
        backdrop-filter: blur(10px);
        cursor: pointer;
        max-width: 17rem;
        margin: 0 auto;
    }

    .service-card::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        opacity: 0;
        transition: opacity 0.3s ease;
    }

    .service-card:nth-child(1)::before {
        background: linear-gradient(135deg, rgba(102, 126, 234, 0.15), rgba(118, 75, 162, 0.15));
    }

    .service-card:nth-child(2)::before {
        background: linear-gradient(135deg, rgba(240, 147, 251, 0.15), rgba(79, 172, 254, 0.15));
    }

    .service-card:nth-child(3)::before {
        background: linear-gradient(135deg, rgba(79, 172, 254, 0.15), rgba(0, 242, 254, 0.15));
    }

    .service-card:nth-child(4)::before {
        background: linear-gradient(135deg, rgba(16, 185, 129, 0.15), rgba(5, 150, 105, 0.15));
    }

    .service-card:nth-child(5)::before {
        background: linear-gradient(135deg, rgba(245, 158, 11, 0.15), rgba(217, 119, 6, 0.15));
    }

    .service-card:nth-child(6)::before {
        background: linear-gradient(135deg, rgba(239, 68, 68, 0.15), rgba(220, 38, 38, 0.15));
    }

    .service-card:hover {
        border-color: rgba(255, 255, 255, 0.8);
        box-shadow: 0 20px 50px rgba(0, 0, 0, 0.2), 0 0 0 1px rgba(255, 255, 255, 0.8);
        transform: translateY(-10px) scale(1.02);
    }

    .service-card:hover::before {
        opacity: 1;
    }

    .service-card .card-body {
        position: relative;
        z-index: 1;
        padding: 0 !important;
    }

    .service-card .icon-box {
        width: 70px;
        height: 70px;
        border-radius: 16px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.75rem;
        transition: var(--transition);
        background: linear-gradient(135deg, var(--primary-color), var(--secondary-color)) !important;
        color: white !important;
        box-shadow: 0 8px 20px rgba(0, 0, 0, 0.1);
        margin: 0 auto 16px auto !important;
    }

    .service-card:hover .icon-box {
        transform: rotate(12deg) scale(1.15);
        box-shadow: 0 12px 30px rgba(0, 0, 0, 0.2);
    }

    .service-card .card-title {
        color: var(--text-dark);
        font-size: 1.1rem;
        letter-spacing: -0.3px;
        margin-bottom: 0 !important;
    }

    .back-btn {
        background: linear-gradient(135deg, rgba(255, 255, 255, 0.95) 0%, rgba(255, 255, 255, 0.9) 100%);
        border: 2px solid rgba(255, 255, 255, 0.5);
        border-radius: var(--border-radius);
        color: var(--text-dark) !important;
        padding: 12px 28px;
        font-weight: 600;
        transition: var(--transition);
        box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
    }

    .back-btn:hover {
        border-color: rgba(255, 255, 255, 0.8);
        box-shadow: 0 20px 50px rgba(0, 0, 0, 0.2);
        transform: translateY(-4px);
    }

    .text-decoration-none {
        text-decoration: none !important;
    }

    .text-decoration-none:hover {
        text-decoration: none !important;
    }

    .animate-in {
        animation: fadeInUp 0.8s ease-out forwards;
        opacity: 0;
    }

    .delay-1 { animation-delay: 0.1s; }
    .delay-2 { animation-delay: 0.2s; }
    .delay-3 { animation-delay: 0.3s; }
    .delay-4 { animation-delay: 0.4s; }
    .delay-5 { animation-delay: 0.5s; }
    .delay-6 { animation-delay: 0.6s; }
    .lead {
        font-size: 1.1rem;
        color: #ffffff !important;
        background-color: rgba(0, 0, 0, 0.1);
        display: inline-block;
        padding: 8px 16px;
        font-weight: 500;
    }
</style>

<div class="container-fluid p-4" style="min-height: 100vh;">
    <div class="dashboard-container">
        <div class="page-header animate-in">
            <h1 class="fw-bold">Select Panel Type</h1>
            <p class="lead">Choose the type of service you are looking for.</p>
        </div>

        <div class="row g-4 justify-content-center">
            <?php if (!empty($panel_types)): ?>
                <?php $delay = 1; ?>
                <?php foreach ($panel_types as $type): ?>
                    <div class="col-lg-4 col-md-6 animate-in delay-<?php echo min($delay, 6); ?>">
                        <a href="<?php echo url('instant_panel/list_by_type/' . base64_encode($type)); ?>" class="text-decoration-none">
                            <div class="card service-card">
                                <div class="card-body d-flex flex-column align-items-center justify-content-center">
                                    <div class="icon-box">
                                        <i class="fas fa-layer-group fa-2x"></i>
                                    </div>
                                    <h5 class="card-title fw-bold"><?php echo htmlspecialchars($type); ?></h5>
                                </div>
                            </div>
                        </a>
                    </div>
                    <?php $delay++; ?>
                <?php endforeach; ?>
            <?php else: ?>
                <div class="col-12">
                    <div class="alert alert-light border-2 py-5 text-center animate-in">
                        <i class="fas fa-folder-open fa-3x text-muted mb-3 opacity-25"></i>
                        <p class="text-muted mb-0">No panel types available at the moment.</p>
                    </div>
                </div>
            <?php endif; ?>
        </div>

        <div class="text-center mt-5">
            <a href="<?php echo url('dashboard/partner'); ?>" class="btn back-btn"><i class="fas fa-arrow-left me-2"></i> Back to Dashboard</a>
        </div>
    </div>
</div>

<?php view('layouts/partner_footer'); ?>
