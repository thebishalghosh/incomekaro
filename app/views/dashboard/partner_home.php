<?php
    view('layouts/partner_header', ['title' => 'Partner Dashboard']);

    // Fetch dynamic company details
    require_once APP_PATH . '/core/helpers.php'; // Ensure helper is loaded
    $company = get_company_details();
    
    // Helper function to convert hex to rgb
    function hexToRgb($hex) {
        $hex = str_replace('#', '', $hex);
        $r = hexdec(substr($hex, 0, 2));
        $g = hexdec(substr($hex, 2, 2));
        $b = hexdec(substr($hex, 4, 2));
        return "$r, $g, $b";
    }
    $primary_rgb = hexToRgb(get_primary_color());

    // Determine RM Name
    $rm_name = 'Not Assigned';
    if (!empty($partner['rm_first'])) {
        $rm_name = $partner['rm_first'] . ' ' . ($partner['rm_last'] ?? '');
    }

    // Determine State
    $partner_state = $partner['address_office']['state'] ?? $partner['address_permanent']['state'] ?? 'N/A';
?>

<link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
<style>
    :root {
        --primary-color: <?php echo get_primary_color(); ?>;
        --secondary-color: <?php echo get_secondary_color(); ?>;
        --bg-color: #f8fafc;
        --card-bg: #ffffff;
        --text-dark: #0f172a;
        --text-muted: #64748b;
        --border-radius: 20px;
        --border-radius-sm: 12px;
        --transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
        --shadow-sm: 0 1px 3px 0 rgba(0, 0, 0, 0.05), 0 1px 2px 0 rgba(0, 0, 0, 0.03);
        --shadow-md: 0 4px 6px -1px rgba(0, 0, 0, 0.08), 0 2px 4px -1px rgba(0, 0, 0, 0.04);
        --shadow-lg: 0 10px 15px -3px rgba(0, 0, 0, 0.1), 0 4px 6px -2px rgba(0, 0, 0, 0.05);
        --shadow-xl: 0 20px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04);
    }

    body {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 25%, #f093fb 50%, #4facfe 75%, #00f2fe 100%);
        background-size: 400% 400%;
        animation: gradientShift 15s ease infinite;
        font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif;
        color: var(--text-dark);
        font-weight: 400;
        line-height: 1.6;
        min-height: 100vh;
    }

    @keyframes gradientShift {
        0% { background-position: 0% 50%; }
        50% { background-position: 100% 50%; }
        100% { background-position: 0% 50%; }
    }

    .dashboard-container {
        max-width: 1400px;
        margin: 0 auto;
        padding: 40px 24px;
    }

    /* Enhanced Animations */
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

    @keyframes slideInRight {
        from {
            opacity: 0;
            transform: translateX(30px);
        }
        to {
            opacity: 1;
            transform: translateX(0);
        }
    }

    @keyframes pulse {
        0%, 100% { transform: scale(1); }
        50% { transform: scale(1.05); }
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

    /* Hero Section - Colorful Premium Design */
    .hero-card {
        background: linear-gradient(135deg, rgba(255, 255, 255, 0.95) 0%, rgba(255, 255, 255, 0.9) 100%);
        border-radius: var(--border-radius);
        padding: 40px;
        position: relative;
        overflow: hidden;
        box-shadow: 0 20px 60px rgba(0, 0, 0, 0.15), 0 0 0 1px rgba(255, 255, 255, 0.5);
        border: 2px solid rgba(255, 255, 255, 0.8);
        margin-bottom: 40px;
        backdrop-filter: blur(20px);
    }

    .hero-card::before {
        content: '';
        position: absolute;
        top: -50%;
        right: -10%;
        width: 500px;
        height: 500px;
        background: radial-gradient(circle, rgba(102, 126, 234, 0.3) 0%, rgba(118, 75, 162, 0.2) 50%, transparent 70%);
        border-radius: 50%;
        pointer-events: none;
        animation: pulse 4s ease-in-out infinite;
    }

    .hero-card::after {
        content: '';
        position: absolute;
        bottom: -30%;
        left: -5%;
        width: 400px;
        height: 400px;
        background: radial-gradient(circle, rgba(79, 172, 254, 0.3) 0%, rgba(0, 242, 254, 0.2) 50%, transparent 70%);
        border-radius: 50%;
        pointer-events: none;
        animation: pulse 5s ease-in-out infinite;
    }

    .profile-avatar {
        width: 110px;
        height: 110px;
        border-radius: 50%;
        object-fit: cover;
        border: 5px solid #ffffff;
        box-shadow: var(--shadow-xl);
        transition: var(--transition);
        position: relative;
        z-index: 1;
    }

    .profile-avatar.bg-light {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 25%, #f093fb 50%, #4facfe 75%, #00f2fe 100%);
        background-size: 200% 200%;
        animation: gradientMove 5s ease infinite;
        color: white;
        font-weight: 700;
        font-size: 2.5rem;
    }

    .hero-card:hover .profile-avatar {
        transform: scale(1.08) rotate(5deg);
        box-shadow: 0 20px 40px rgba(<?php echo $primary_rgb; ?>, 0.3);
    }

    /* Colorful Premium Stat Cards */
    .stat-card {
        background: linear-gradient(135deg, rgba(255, 255, 255, 0.95) 0%, rgba(255, 255, 255, 0.9) 100%);
        border-radius: var(--border-radius);
        padding: 32px;
        height: 100%;
        border: 2px solid rgba(255, 255, 255, 0.5);
        box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1), 0 0 0 1px rgba(255, 255, 255, 0.5);
        transition: var(--transition);
        position: relative;
        overflow: hidden;
        backdrop-filter: blur(10px);
    }

    .stat-card::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        height: 5px;
        background: linear-gradient(90deg, #667eea, #764ba2, #f093fb, #4facfe, #00f2fe);
        background-size: 200% 100%;
        transform: scaleX(0);
        transform-origin: left;
        transition: transform 0.4s ease;
        animation: gradientMove 3s ease infinite;
    }

    .stat-card:hover {
        transform: translateY(-10px) scale(1.02);
        box-shadow: 0 20px 50px rgba(0, 0, 0, 0.2), 0 0 0 1px rgba(255, 255, 255, 0.8);
    }

    .stat-card:hover::before {
        transform: scaleX(1);
    }

    @keyframes gradientMove {
        0% { background-position: 0% 50%; }
        50% { background-position: 100% 50%; }
        100% { background-position: 0% 50%; }
    }

    .stat-card::after {
        content: '';
        position: absolute;
        top: -50%;
        right: -20%;
        width: 200px;
        height: 200px;
        background: radial-gradient(circle, rgba(102, 126, 234, 0.2) 0%, transparent 70%);
        border-radius: 50%;
        opacity: 0;
        transition: opacity 0.4s ease;
    }

    .stat-card:hover::after {
        opacity: 1;
    }

    /* Colorful stat icons with unique gradients */
    .stat-card.stat-wallet .stat-icon {
        background: linear-gradient(135deg, #10b981 0%, #059669 100%) !important;
        color: white !important;
        box-shadow: 0 8px 20px rgba(16, 185, 129, 0.4);
    }

    .stat-card.stat-plan .stat-icon {
        background: linear-gradient(135deg, #8b5cf6 0%, #7c3aed 100%) !important;
        color: white !important;
        box-shadow: 0 8px 20px rgba(139, 92, 246, 0.4);
    }

    .stat-card.stat-applications .stat-icon {
        background: linear-gradient(135deg, #3b82f6 0%, #2563eb 100%) !important;
        color: white !important;
        box-shadow: 0 8px 20px rgba(59, 130, 246, 0.4);
    }

    .stat-card.stat-approved .stat-icon {
        background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%) !important;
        color: white !important;
        box-shadow: 0 8px 20px rgba(245, 158, 11, 0.4);
    }

    .stat-icon {
        width: 64px;
        height: 64px;
        border-radius: 18px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.75rem;
        transition: var(--transition);
        position: relative;
        z-index: 1;
    }

    .stat-card:hover .stat-icon {
        transform: rotate(15deg) scale(1.2);
        box-shadow: 0 12px 30px rgba(0, 0, 0, 0.3) !important;
    }

    /* Colorful Enhanced Action Cards */
    .action-card {
        background: linear-gradient(135deg, rgba(255, 255, 255, 0.95) 0%, rgba(255, 255, 255, 0.9) 100%);
        border-radius: var(--border-radius);
        padding: 32px 24px;
        text-align: center;
        border: 2px solid rgba(255, 255, 255, 0.5);
        box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1), 0 0 0 1px rgba(255, 255, 255, 0.5);
        transition: var(--transition);
        height: 100%;
        cursor: pointer;
        position: relative;
        overflow: hidden;
        backdrop-filter: blur(10px);
    }

    .action-card::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        opacity: 0;
        transition: opacity 0.4s ease;
    }

    .action-card:nth-child(1)::before {
        background: linear-gradient(135deg, rgba(102, 126, 234, 0.1), rgba(118, 75, 162, 0.1));
    }

    .action-card:nth-child(2)::before {
        background: linear-gradient(135deg, rgba(240, 147, 251, 0.1), rgba(79, 172, 254, 0.1));
    }

    .action-card:nth-child(3)::before {
        background: linear-gradient(135deg, rgba(79, 172, 254, 0.1), rgba(0, 242, 254, 0.1));
    }

    .action-card:hover {
        transform: translateY(-10px) scale(1.03);
        box-shadow: 0 20px 50px rgba(0, 0, 0, 0.2), 0 0 0 1px rgba(255, 255, 255, 0.8);
    }

    .action-card:hover::before {
        opacity: 1;
    }

    .action-card:hover h6,
    .action-card:hover p {
        color: var(--text-dark);
        position: relative;
        z-index: 1;
    }

    .action-icon-circle {
        width: 80px;
        height: 80px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        margin: 0 auto 20px;
        font-size: 2rem;
        transition: var(--transition);
        position: relative;
        z-index: 1;
        box-shadow: 0 8px 20px rgba(0, 0, 0, 0.15);
    }

    .action-card:nth-child(1) .action-icon-circle {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
    }

    .action-card:nth-child(2) .action-icon-circle {
        background: linear-gradient(135deg, #f093fb 0%, #4facfe 100%);
        color: white;
    }

    .action-card:nth-child(3) .action-icon-circle {
        background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%);
        color: white;
    }

    .action-card:hover .action-icon-circle {
        box-shadow: 0 15px 35px rgba(0, 0, 0, 0.3);
        transform: scale(1.15) rotate(10deg);
    }

    /* Colorful Premium Service Cards */
    .service-card {
        background: linear-gradient(135deg, rgba(255, 255, 255, 0.95) 0%, rgba(255, 255, 255, 0.9) 100%);
        border-radius: var(--border-radius-sm);
        padding: 0;
        border: 2px solid rgba(255, 255, 255, 0.5);
        box-shadow: 0 8px 25px rgba(0, 0, 0, 0.1), 0 0 0 1px rgba(255, 255, 255, 0.5);
        transition: var(--transition);
        height: 100%;
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: flex-start;
        text-align: center;
        position: relative;
        overflow: hidden;
        backdrop-filter: blur(10px);
    }

    .service-img-wrapper {
        width: 100%;
        height: 200px;
        display: flex;
        align-items: center;
        justify-content: center;
        margin-bottom: 0;
        transition: var(--transition);
        position: relative;
        z-index: 1;
        padding: 0;
        border-radius: 0;
        background: linear-gradient(135deg, rgba(255, 255, 255, 0.5), rgba(255, 255, 255, 0.3));
        overflow: hidden;
        flex-shrink: 0;
    }

    .service-card:hover .service-img-wrapper {
        transform: scale(1.05);
        background: linear-gradient(135deg, rgba(255, 255, 255, 0.8), rgba(255, 255, 255, 0.6));
    }

    .service-img-wrapper img {
        width: 100%;
        height: 100%;
        object-fit: contain;
        object-position: center;
        filter: drop-shadow(0 4px 12px rgba(0, 0, 0, 0.2));
        transition: var(--transition);
    }

    .service-card:hover .service-img-wrapper img {
        filter: drop-shadow(0 8px 20px rgba(0, 0, 0, 0.4));
        transform: scale(1.08);
    }

    .service-img-wrapper i {
        font-size: 2.5rem;
        background: linear-gradient(135deg, #667eea, #764ba2, #f093fb, #4facfe);
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
        background-clip: text;
    }

    /* Service Card Text Content */
    .service-card h6 {
        padding: 16px 12px 4px 12px;
        margin: 0;
        flex-grow: 1;
        display: flex;
        align-items: flex-start;
        justify-content: center;
    }

    .service-card span {
        padding: 0 12px 12px 12px;
        display: block;
        margin: 0;
    }

    .service-card:hover {
        border-color: rgba(255, 255, 255, 0.8);
        box-shadow: 0 15px 40px rgba(0, 0, 0, 0.2), 0 0 0 1px rgba(255, 255, 255, 0.8);
        transform: translateY(-8px);
    }

    .service-card:hover .service-img-wrapper {
        background: linear-gradient(135deg, rgba(255, 255, 255, 0.9), rgba(255, 255, 255, 0.7));
    }

    /* Colorful Premium Chart Cards */
    .chart-card {
        background: linear-gradient(135deg, rgba(255, 255, 255, 0.95) 0%, rgba(255, 255, 255, 0.9) 100%);
        border-radius: var(--border-radius);
        padding: 32px;
        border: 2px solid rgba(255, 255, 255, 0.5);
        box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1), 0 0 0 1px rgba(255, 255, 255, 0.5);
        height: 100%;
        transition: var(--transition);
        backdrop-filter: blur(10px);
        position: relative;
        overflow: hidden;
    }

    .chart-card::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        height: 4px;
        background: linear-gradient(90deg, #667eea, #764ba2, #f093fb, #4facfe, #00f2fe);
        background-size: 200% 100%;
        animation: gradientMove 3s ease infinite;
    }

    .chart-card:hover {
        box-shadow: 0 20px 50px rgba(0, 0, 0, 0.2), 0 0 0 1px rgba(255, 255, 255, 0.8);
        transform: translateY(-6px);
    }

    .chart-card h6 {
        font-weight: 700;
        letter-spacing: -0.5px;
        color: var(--text-dark);
    }

    .chart-card h6 i {
        background: linear-gradient(135deg, #667eea, #764ba2, #f093fb, #4facfe);
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
        background-clip: text;
    }

    /* Colorful Enhanced Alert */
    .alert {
        border-radius: var(--border-radius-sm);
        backdrop-filter: blur(10px);
        border: none;
        box-shadow: 0 10px 30px rgba(220, 38, 38, 0.2), 0 0 0 1px rgba(255, 255, 255, 0.5);
        border-left: 5px solid #dc2626 !important;
    }

    /* Colorful Enhanced Badge */
    .badge {
        font-weight: 600;
        letter-spacing: 0.3px;
        padding: 8px 16px;
        border-radius: 12px;
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
    }

    .badge.bg-success-subtle {
        background: linear-gradient(135deg, #d1fae5 0%, #a7f3d0 100%) !important;
        color: #065f46 !important;
        border: 1px solid rgba(16, 185, 129, 0.3);
    }

    .badge.bg-warning-subtle {
        background: linear-gradient(135deg, #fef3c7 0%, #fde68a 100%) !important;
        color: #92400e !important;
        border: 1px solid rgba(245, 158, 11, 0.3);
    }

    .badge.bg-danger-subtle {
        background: linear-gradient(135deg, #fee2e2 0%, #fecaca 100%) !important;
        color: #991b1b !important;
        border: 1px solid rgba(239, 68, 68, 0.3);
    }

    /* Typography Enhancements */
    h2 {
        font-weight: 800;
        letter-spacing: -1px;
        color: var(--text-dark);
    }

    h5, h6 {
        font-weight: 700;
        letter-spacing: -0.5px;
    }

    .text-muted {
        color: var(--text-muted) !important;
        font-weight: 500;
    }

    /* Section Headers */
    .section-header {
        display: flex;
        align-items: center;
        justify-content: space-between;
        margin-bottom: 24px;
    }

    .section-header h5 {
        font-weight: 800;
        letter-spacing: -0.5px;
        color: var(--text-dark);
        margin: 0;
    }

    .services-heading {
        font-size: 1.5rem;
        font-weight: 800;
        letter-spacing: -0.5px;
        color: #0f172a !important;
        position: relative;
        display: inline-block;
    }

    /* Gradient text effect - only if supported */
    @supports (-webkit-background-clip: text) {
        .services-heading {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 25%, #f093fb 50%, #4facfe 75%, #00f2fe 100%);
            background-size: 200% 200%;
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
            animation: gradientMove 3s ease infinite;
            color: transparent;
        }
    }

    /* Empty State */
    .empty-state {
        padding: 60px 20px;
        text-align: center;
    }

    .empty-state i {
        opacity: 0.3;
        margin-bottom: 16px;
    }

    /* Responsive Improvements */
    @media (max-width: 768px) {
        .dashboard-container {
            padding: 24px 16px;
        }

        .hero-card {
            padding: 24px;
        }

        .stat-card,
        .action-card {
            padding: 24px;
        }
    }

    /* Utility Classes */
    .text-primary-soft { 
        color: var(--primary-color); 
        opacity: 0.85; 
    }
    
    .bg-primary-soft { 
        background-color: rgba(<?php echo $primary_rgb; ?>, 0.1); 
    }

    /* Smooth Scroll */
    html {
        scroll-behavior: smooth;
    }
</style>

<div class="dashboard-container">

    <!-- Hero Section -->
    <div class="hero-card animate-in">
        <div class="d-flex align-items-center flex-wrap position-relative" style="z-index: 1;">
            <div class="me-4 mb-3 mb-md-0">
                <?php if (!empty($partner['profile']['profile_image'])): ?>
                    <img src="<?php echo asset($partner['profile']['profile_image']); ?>" alt="Profile" class="profile-avatar">
                <?php else: ?>
                    <div class="profile-avatar bg-light d-flex align-items-center justify-content-center text-white fw-bold">
                        <?php echo strtoupper(substr($partner['profile']['full_name'], 0, 1)); ?>
                    </div>
                <?php endif; ?>
            </div>
            <div class="flex-grow-1">
                <h6 class="text-muted text-uppercase small fw-bold mb-2" style="letter-spacing: 1.5px; font-size: 0.75rem;">Welcome Back</h6>
                <h2 class="fw-bold mb-2 text-dark" style="font-size: 2rem; line-height: 1.2;"><?php echo htmlspecialchars($partner['profile']['full_name']); ?></h2>
                <div class="d-flex align-items-center gap-3 text-muted small flex-wrap">
                    <span class="d-flex align-items-center"><i class="fas fa-id-badge me-2"></i> <span class="fw-semibold">ID:</span> <?php echo htmlspecialchars($partner['id']); ?></span>
                    <span class="d-none d-sm-inline text-muted">•</span>
                    <span class="d-flex align-items-center"><i class="fas fa-envelope me-2"></i> <?php echo htmlspecialchars($partner['profile']['email']); ?></span>
                </div>
                <div class="mt-2 text-muted small">
                    <i class="fas fa-user-tie me-2"></i> Assigned RM: <span class="fw-bold text-dark"><?php echo htmlspecialchars($rm_name); ?></span>
                    <span class="mx-2">|</span>
                    <i class="fas fa-map-marker-alt me-2"></i> State: <span class="fw-bold text-dark"><?php echo htmlspecialchars($partner_state); ?></span>
                </div>
            </div>
            <div class="text-end mt-3 mt-md-0">
                <?php
                    $kyc_status = $partner['kyc_status'] ?? 'PENDING';
                    $kyc_class = 'bg-warning-subtle text-warning';
                    $kyc_icon = 'fa-clock';
                    if ($kyc_status == 'VERIFIED') { $kyc_class = 'bg-success-subtle text-success'; $kyc_icon = 'fa-check-circle'; }
                    elseif ($kyc_status == 'REJECTED') { $kyc_class = 'bg-danger-subtle text-danger'; $kyc_icon = 'fa-times-circle'; }
                ?>
                <div class="badge <?php echo $kyc_class; ?> px-3 py-2 d-inline-flex align-items-center mb-2" style="font-size: 0.75rem;">
                    <i class="fas <?php echo $kyc_icon; ?> me-2"></i> KYC <?php echo $kyc_status; ?>
                </div>
                <div class="text-muted small" style="font-size: 0.8rem;"><i class="fas fa-calendar-alt me-1"></i> Joined <?php echo date('d M Y', strtotime($partner['created_at'])); ?></div>
            </div>
        </div>
    </div>

    <!-- Payment Alert -->
    <?php if (!empty($partner['subscription']['due_amount']) && $partner['subscription']['due_amount'] > 0): ?>
        <div class="alert alert-danger border-0 d-flex align-items-center mb-4 animate-in delay-1" role="alert" style="background: linear-gradient(135deg, #fee2e2 0%, #fecaca 100%); border-left: 4px solid #dc2626 !important;">
            <div class="me-3 text-danger"><i class="fas fa-exclamation-circle fa-2x"></i></div>
            <div class="flex-grow-1">
                <h6 class="alert-heading fw-bold mb-1" style="color: #991b1b;">Payment Due</h6>
                <p class="mb-0 small" style="color: #7f1d1d;">Pending Amount: <strong class="fw-bold">₹<?php echo number_format($partner['subscription']['due_amount'], 2); ?></strong>. Please clear your dues.</p>
            </div>
        </div>
    <?php endif; ?>

    <!-- Stats Grid -->
    <div class="row g-4 mb-5">
        <div class="col-xl-3 col-md-6 animate-in delay-1">
            <div class="stat-card stat-wallet">
                <div class="d-flex justify-content-between align-items-start">
                    <div class="flex-grow-1">
                        <p class="text-muted small fw-bold text-uppercase mb-2" style="letter-spacing: 0.5px; font-size: 0.7rem;">Wallet Balance</p>
                        <h3 class="fw-bold text-dark mb-0" style="font-size: 2rem; line-height: 1.2;">₹<?php echo number_format($user['wallet_balance'] ?? 0, 2); ?></h3>
                    </div>
                    <div class="stat-icon">
                        <i class="fas fa-wallet"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-md-6 animate-in delay-2">
            <div class="stat-card stat-plan">
                <div class="d-flex justify-content-between align-items-start">
                    <div class="flex-grow-1">
                        <p class="text-muted small fw-bold text-uppercase mb-2" style="letter-spacing: 0.5px; font-size: 0.7rem;">Current Plan</p>
                        <h4 class="fw-bold text-dark mb-0" style="font-size: 1.5rem; line-height: 1.3;"><?php echo htmlspecialchars($partner['subscription']['plan_name'] ?? 'None'); ?></h4>
                    </div>
                    <div class="stat-icon">
                        <i class="fas fa-crown"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-md-6 animate-in delay-3">
            <div class="stat-card stat-applications">
                <div class="d-flex justify-content-between align-items-start">
                    <div class="flex-grow-1">
                        <p class="text-muted small fw-bold text-uppercase mb-2" style="letter-spacing: 0.5px; font-size: 0.7rem;">Total Applications</p>
                        <h3 class="fw-bold text-dark mb-0" style="font-size: 2rem; line-height: 1.2;"><?php echo number_format($stats['total_applications'] ?? 0); ?></h3>
                    </div>
                    <div class="stat-icon">
                        <i class="fas fa-file-alt"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-md-6 animate-in delay-4">
            <div class="stat-card stat-approved">
                <div class="d-flex justify-content-between align-items-start">
                    <div class="flex-grow-1">
                        <p class="text-muted small fw-bold text-uppercase mb-2" style="letter-spacing: 0.5px; font-size: 0.7rem;">Approved</p>
                        <h3 class="fw-bold text-dark mb-0" style="font-size: 2rem; line-height: 1.2;"><?php echo number_format($stats['total_approved'] ?? 0); ?></h3>
                    </div>
                    <div class="stat-icon">
                        <i class="fas fa-check-circle"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Quick Actions -->
    <div class="row g-4 mb-5">
        <div class="col-md-4 animate-in delay-2">
            <div class="action-card" onclick="window.open('<?php echo url('certificate/download'); ?>', '_blank')">
                <div class="action-icon-circle">
                    <i class="fas fa-certificate"></i>
                </div>
                <h6 class="fw-bold text-dark mb-2" style="font-size: 1.1rem;">Certificate</h6>
                <p class="text-muted small mb-0" style="font-size: 0.85rem;">Download Partnership Certificate</p>
            </div>
        </div>
        <div class="col-md-4 animate-in delay-3">
            <div class="action-card" onclick="window.open('<?php echo url('authorization/download'); ?>', '_blank')">
                <div class="action-icon-circle">
                    <i class="fas fa-user-check"></i>
                </div>
                <h6 class="fw-bold text-dark mb-2" style="font-size: 1.1rem;">Authorization</h6>
                <p class="text-muted small mb-0" style="font-size: 0.85rem;">Download Authorization Letter</p>
            </div>
        </div>
        <div class="col-md-4 animate-in delay-4">
            <div class="action-card" onclick="window.open('<?php echo url('agreement/download'); ?>', '_blank')">
                <div class="action-icon-circle">
                    <i class="fas fa-file-contract"></i>
                </div>
                <h6 class="fw-bold text-dark mb-2" style="font-size: 1.1rem;">Agreement</h6>
                <p class="text-muted small mb-0" style="font-size: 0.85rem;">View Signed Agreement</p>
            </div>
        </div>
    </div>

    <!-- Services -->
    <div class="section-header animate-in">
        <h5 class="fw-bold mb-0 services-heading">My Services</h5>
        <span class="badge fw-semibold" style="font-size: 0.85rem; padding: 8px 16px; background: linear-gradient(135deg, #667eea, #764ba2, #f093fb, #4facfe) !important; color: white !important; border: none !important;"><?php echo count($partner['subscription']['services'] ?? []); ?> Active</span>
    </div>

    <?php if (!empty($partner['subscription']['services'])): ?>
        <div class="row g-4 mb-5">
            <?php
                $parent_services = array_filter($partner['subscription']['services'], function($svc) {
                    return empty($svc['parent_id']);
                });
            ?>
            <?php foreach ($parent_services as $index => $svc): ?>
                <?php
                    $link = '#';
                    $target = '';
                    if ($svc['service_type'] === 'EXTERNAL_REDIRECT' && !empty($svc['url'])) {
                        $link = $svc['url'];
                        $target = '_blank';
                    } elseif ($svc['service_type'] === 'INTERNAL_FORM') {
                        $link = url('application/select/' . $svc['id']);
                    }
                    $delay = ($index % 5) + 1;
                ?>
                <div class="col-xl-2 col-lg-3 col-md-4 col-sm-6 animate-in delay-<?php echo $delay; ?>">
                    <a href="<?php echo $link; ?>" target="<?php echo $target; ?>" class="text-decoration-none">
                        <div class="service-card">
                            <div class="service-img-wrapper">
                                <?php if (!empty($svc['image_url'])): ?>
                                    <img src="<?php echo asset($svc['image_url']); ?>" alt="<?php echo htmlspecialchars($svc['name']); ?>" onerror="this.style.display='none'; this.nextElementSibling.style.display='block';">
                                    <i class="fas fa-box-open fa-2x text-primary" style="display: none;"></i>
                                <?php else: ?>
                                    <i class="fas fa-box-open fa-2x text-primary"></i>
                                <?php endif; ?>
                            </div>
                            <h6 class="fw-bold text-dark mb-1" style="font-size: 0.9rem; line-height: 1.3;"><?php echo htmlspecialchars($svc['name']); ?></h6>
                            <span class="text-muted" style="font-size: 0.75rem; font-weight: 500;"><?php echo htmlspecialchars($svc['category']); ?></span>
                        </div>
                    </a>
                </div>
            <?php endforeach; ?>
        </div>
    <?php else: ?>
        <div class="empty-state animate-in">
            <i class="fas fa-box-open fa-3x text-muted mb-3"></i>
            <p class="text-muted fw-semibold">No services active.</p>
        </div>
    <?php endif; ?>

    <!-- Charts -->
    <div class="row g-4 animate-in delay-2">
        <div class="col-lg-8">
            <div class="chart-card">
                <h6 class="fw-bold text-dark mb-4" style="font-size: 1.1rem; letter-spacing: -0.3px;">
                    <i class="fas fa-chart-bar me-2 text-primary"></i>Applications Overview
                </h6>
                <div id="serviceChart" style="width: 100%; height: 300px;"></div>
            </div>
        </div>
        <div class="col-lg-4">
            <div class="chart-card">
                <h6 class="fw-bold text-dark mb-4" style="font-size: 1.1rem; letter-spacing: -0.3px;">
                    <i class="fas fa-chart-pie me-2 text-primary"></i>Status Distribution
                </h6>
                <div id="statusChart" style="width: 100%; height: 300px;"></div>
            </div>
        </div>
    </div>

</div>

<!-- Google Charts -->
<script type="text/javascript" src="https://www.gstatic.com/charts/loader.js"></script>
<script type="text/javascript">
    google.charts.load('current', {'packages':['corechart']});
    google.charts.setOnLoadCallback(drawCharts);

    function drawCharts() {
        // 1. Status Chart
        var statusData = google.visualization.arrayToDataTable([
            ['Status', 'Count'],
            <?php
                if (!empty($stats['by_status'])) {
                    foreach ($stats['by_status'] as $status => $count) {
                        echo "['" . str_replace('_', ' ', $status) . "', " . $count . "],";
                    }
                } else {
                    echo "['No Data', 1]";
                }
            ?>
        ]);

        var statusOptions = {
            pieHole: 0.6,
            colors: ['#667eea', '#764ba2', '#f093fb', '#4facfe', '#00f2fe', '#10b981', '#f59e0b', '#ef4444'],
            chartArea: {width: '90%', height: '80%'},
            legend: {position: 'bottom', textStyle: {color: '#0f172a', fontSize: 12, bold: true}},
            pieSliceText: 'none',
            backgroundColor: 'transparent',
            tooltip: {textStyle: {color: '#0f172a', fontSize: 12}}
        };

        var statusChart = new google.visualization.PieChart(document.getElementById('statusChart'));
        statusChart.draw(statusData, statusOptions);

        // 2. Service Chart
        var serviceData = google.visualization.arrayToDataTable([
            ['Service', 'Applications', { role: 'style' }],
            <?php
                if (!empty($stats['by_root_service'])) {
                    $colors = ['#667eea', '#764ba2', '#f093fb', '#4facfe', '#00f2fe', '#10b981', '#f59e0b', '#ef4444', '#8b5cf6', '#ec4899'];
                    $i = 0;
                    foreach ($stats['by_root_service'] as $name => $count) {
                        $color = $colors[$i % count($colors)];
                        echo "['" . $name . "', " . $count . ", '$color'],";
                        $i++;
                    }
                } else {
                    echo "['No Data', 0, '#ccc']";
                }
            ?>
        ]);

        var serviceOptions = {
            legend: { position: "none" },
            chartArea: {width: '85%', height: '70%'},
            vAxis: { 
                minValue: 0, 
                format: '0', 
                gridlines: { color: 'rgba(0, 0, 0, 0.05)' },
                textStyle: { color: '#64748b', fontSize: 11, bold: true }
            },
            hAxis: { 
                textStyle: { color: '#0f172a', fontSize: 11, bold: true } 
            },
            backgroundColor: 'transparent',
            bar: { groupWidth: '50%' },
            tooltip: {textStyle: {color: '#0f172a', fontSize: 12, bold: true}}
        };

        var serviceChart = new google.visualization.ColumnChart(document.getElementById('serviceChart'));
        serviceChart.draw(serviceData, serviceOptions);
    }

    window.addEventListener('resize', drawCharts);
</script>

<?php view('layouts/partner_footer'); ?>
