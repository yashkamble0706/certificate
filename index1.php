<?php
$show_detail = false;
$reference_id = $certificate_number = $name = $designation = $institute = $workshop_orgeniser = $workshop_name = $workshop_date = $avatar = $batch = $unique_id = $verify_url = $qr_code_url = '';
$search_type = 'reference_id'; // Default search type
$search_value = '';
$error_message = '';

// Check if form was submitted with either reference ID or certificate number
if (isset($_GET['rid']) || isset($_GET['cert_num'])) {
    if (isset($_GET['rid']) && !empty($_GET['rid'])) {
        $search_type = 'reference_id';
        $search_value = $_GET['rid'];
        $reference_id = $_GET['rid'];
    } elseif (isset($_GET['cert_num']) && !empty($_GET['cert_num'])) {
        $search_type = 'certificate_number';
        $search_value = $_GET['cert_num'];
        $certificate_number = $_GET['cert_num'];
    }
    
    $result = [];
    
    if (($handle = fopen("users.csv", "r")) !== false) {
        // If the CSV file contains a header, read and ignore it
        $header = fgetcsv($handle, 1000, ",");
        
        while (($data = fgetcsv($handle, 1000, ",")) !== false) {
            // Check if search matches either reference ID (first column) or certificate number (second column)
            if (($search_type == 'reference_id' && $data[0] === $search_value) || 
                ($search_type == 'certificate_number' && $data[1] === $search_value)) {
                $show_detail = true;
                $result = $data;
                break;
            }
        }
        fclose($handle);
        
        if ($show_detail) {
            // Map CSV data to variables based on the provided structure:
            // 0: Reference ID, 1: Certificate Number, 2: Name, 3: Designation, 4: Institute,
            // 5: Workshop Organiser, 6: Workshop Name, 7: Workshop Date, 8: Batch,
            // 9: Unique ID, 10: Avatar URL, 11: Verify URL, 12: QR Code URL
            $reference_id        = isset($result[0]) ? $result[0] : '';
            $certificate_number  = isset($result[1]) ? $result[1] : '';
            $name                = isset($result[2]) ? $result[2] : '';
            $designation         = isset($result[3]) ? $result[3] : '';
            $institute           = isset($result[4]) ? $result[4] : '';
            $workshop_orgeniser  = isset($result[5]) ? $result[5] : '';
            $workshop_name       = isset($result[6]) ? $result[6] : '';
            $workshop_date       = isset($result[7]) ? $result[7] : '';
            $batch               = isset($result[8]) ? $result[8] : '';
            $unique_id           = isset($result[9]) ? $result[9] : '';
            $avatar              = isset($result[10]) && !empty($result[10]) ? $result[10] : "https://i.stack.imgur.com/34AD2.jpg";
            $verify_url          = isset($result[11]) ? $result[11] : '';
            $qr_code_url         = isset($result[12]) ? $result[12] : '';
        } else {
            $error_message = "No certificate found with the provided information. Please check and try again.";
        }
    }
}

// Format the workshop date if needed
$formatted_date = $workshop_date;
?>
<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="IIC SIT X ACM SIT Certificate Verification Portal - Verify the authenticity of your certificate">
    <title>IIC SIT X ACM SIT - Certificate Verification</title>
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <!-- AOS Animation Library -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/aos/2.3.4/aos.css" rel="stylesheet">
    <style>
        :root {
            --primary-color: #1e3a8a;
            --secondary-color: #2563eb;
            --accent-color: #0ea5e9;
            --success-color: #10b981;
            --light-color: #f3f4f6;
            --dark-color: #111827;
            --danger-color: #ef4444;
            --warning-color: #f59e0b;
        }
        
        * { margin: 0; padding: 0; box-sizing: border-box; }
        
        body {
            font-family: 'Poppins', sans-serif;
            background: #f0f2f5;
            color: var(--dark-color);
            line-height: 1.6;
        }
        
        /* Header & Navigation */
        .site-header {
            background: linear-gradient(135deg, var(--primary-color), var(--secondary-color));
            color: white;
            padding: 1rem 0;
            position: relative;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }
        
        .logo-container {
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 1rem 0;
        }
        
        .site-logo {
            max-height: 60px;
            margin-right: 15px;
        }
        
        .site-title {
            font-size: 1.8rem;
            font-weight: 700;
            margin: 0;
            color: white;
        }
        
        .site-tagline {
            font-size: 1rem;
            opacity: 0.8;
            margin: 0;
        }
        
        /* Main Container */
        .main-container {
            max-width: 1000px;
            margin: 30px auto;
            padding: 0 15px;
        }
        
        /* Certificate Card */
        .certificate-card {
            background: white;
            border-radius: 15px;
            overflow: hidden;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.08);
            margin-bottom: 30px;
            position: relative;
        }
        
        .certificate-header {
            background: linear-gradient(45deg, var(--primary-color), var(--secondary-color));
            padding: 1.5rem;
            color: white;
            position: relative;
            text-align: center;
        }
        
        .certificate-badge {
            position: absolute;
            top: -10px;
            right: 20px;
            background-color: var(--success-color);
            color: white;
            border-radius: 30px;
            padding: 10px 20px;
            font-weight: 600;
            font-size: 0.9rem;
            box-shadow: 0 4px 10px rgba(16, 185, 129, 0.3);
            display: flex;
            align-items: center;
            gap: 8px;
            z-index: 10;
        }
        
        .certificate-title {
            font-size: 1.6rem;
            font-weight: 700;
            margin-bottom: 0.5rem;
        }
        
        .certificate-subtitle {
            font-size: 1rem;
            opacity: 0.9;
        }
        
        /* Participant Info */
        .participant-section {
            display: flex;
            flex-direction: column;
            align-items: center;
            padding: 2rem;
            background-color: #f8fafc;
            border-bottom: 1px solid #eaeaea;
            position: relative;
        }
        
        .participant-image {
            width: 180px;
            height: 180px;
            border-radius: 50%;
            object-fit: cover;
            border: 5px solid white;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
            margin-bottom: 1.2rem;
        }
        
        .participant-name {
            font-size: 1.6rem;
            font-weight: 700;
            color: var(--dark-color);
            margin-bottom: 0.3rem;
            text-align: center;
        }
        
        .participant-details {
            text-align: center;
            color: #64748b;
            font-size: 1.1rem;
        }
        
        .unique-id-badge {
            background-color: var(--light-color);
            border-radius: 30px;
            padding: 5px 15px;
            font-size: 0.8rem;
            color: var(--primary-color);
            font-weight: 600;
            margin-top: 10px;
            display: inline-block;
        }
        
        /* Certificate Details */
        .certificate-body {
            padding: 2rem;
        }
        
        .certificate-section-title {
            font-size: 1.3rem;
            font-weight: 600;
            color: var(--primary-color);
            margin-bottom: 1.5rem;
            padding-bottom: 0.5rem;
            border-bottom: 2px solid var(--accent-color);
            display: inline-block;
        }
        
        .certificate-info-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 1.5rem;
            margin-bottom: 2rem;
        }
        
        .info-card {
            background: #f8fafc;
            border-radius: 10px;
            padding: 1.2rem;
            transition: all 0.3s ease;
            border-left: 4px solid var(--accent-color);
        }
        
        .info-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 8px 15px rgba(0, 0, 0, 0.05);
        }
        
        .info-label {
            font-size: 0.85rem;
            color: #64748b;
            margin-bottom: 0.5rem;
            display: flex;
            align-items: center;
        }
        
        .info-label i {
            margin-right: 8px;
            color: var(--accent-color);
        }
        
        .info-value {
            font-size: 1.1rem;
            font-weight: 500;
            color: var(--dark-color);
            word-break: break-word;
        }
        
        .certificate-number-value {
            font-family: 'Courier New', monospace;
            letter-spacing: 1px;
            color: var(--secondary-color);
            font-weight: 600;
        }
        
        /* QR and Validation */
        .verification-section {
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 1.5rem;
            background-color: #f8fafc;
            border-top: 1px solid #eaeaea;
            flex-wrap: wrap;
            gap: 2rem;
        }
        
        .qr-container {
            text-align: center;
        }
        
        .qr-code {
            max-width: 150px;
            height: auto;
            padding: 10px;
            background: white;
            border-radius: 8px;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.05);
        }
        
        .qr-caption {
            margin-top: 10px;
            font-size: 0.9rem;
            color: #64748b;
        }
        
        .security-badge {
            display: flex;
            align-items: center;
            justify-content: center;
            background-color: #f0fdf4;
            padding: 12px 20px;
            border-radius: 8px;
            border-left: 4px solid var(--success-color);
            max-width: 400px;
        }
        
        .security-badge i {
            font-size: 1.5rem;
            color: var(--success-color);
            margin-right: 12px;
        }
        
        .security-text {
            font-size: 0.95rem;
            color: var(--dark-color);
        }
        
        /* Action Buttons */
        .action-buttons {
            display: flex;
            justify-content: center;
            gap: 15px;
            margin-top: 1.5rem;
        }
        
        .action-btn {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            padding: 10px 20px;
            border-radius: 8px;
            font-weight: 500;
            transition: all 0.3s ease;
            cursor: pointer;
            font-size: 0.95rem;
        }
        
        .btn-primary {
            background-color: var(--secondary-color);
            color: white;
            border: none;
        }
        
        .btn-primary:hover {
            background-color: var(--primary-color);
            transform: translateY(-3px);
            box-shadow: 0 5px 15px rgba(37, 99, 235, 0.3);
        }
        
        .btn-outline {
            background-color: white;
            color: var(--secondary-color);
            border: 1px solid var(--secondary-color);
        }
        
        .btn-outline:hover {
            background-color: #f0f9ff;
            transform: translateY(-3px);
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.05);
        }
        
        /* Search Form */
        .verification-form {
            max-width: 550px;
            margin: 2rem auto;
            background: white;
            border-radius: 15px;
            padding: 2rem;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.08);
        }
        
        .form-title {
            font-size: 1.5rem;
            font-weight: 600;
            color: var(--primary-color);
            margin-bottom: 1.5rem;
            text-align: center;
        }
        
        .input-group {
            margin-bottom: 1.5rem;
        }
        
        .form-input {
            width: 100%;
            padding: 12px 15px;
            border: 1px solid #e2e8f0;
            border-radius: 8px;
            font-size: 1rem;
            transition: all 0.3s ease;
        }
        
        .form-input:focus {
            border-color: var(--accent-color);
            box-shadow: 0 0 0 3px rgba(14, 165, 233, 0.15);
            outline: none;
        }
        
        .form-label {
            display: block;
            margin-bottom: 8px;
            font-weight: 500;
            color: #64748b;
        }
        
        .form-hint {
            font-size: 0.85rem;
            color: #94a3b8;
            margin-top: 8px;
        }
        
        .submit-btn {
            width: 100%;
            padding: 12px;
            background-color: var(--secondary-color);
            color: white;
            border: none;
            border-radius: 8px;
            font-size: 1rem;
            font-weight: 500;
            cursor: pointer;
            transition: all 0.3s ease;
        }
        
        .submit-btn:hover {
            background-color: var(--primary-color);
            transform: translateY(-3px);
            box-shadow: 0 5px 15px rgba(37, 99, 235, 0.3);
        }
        
        /* Alert Message */
        .alert {
            padding: 15px 20px;
            border-radius: 10px;
            margin-bottom: 1.5rem;
            font-weight: 500;
        }
        
        .alert-danger {
            background-color: #fef2f2;
            color: #b91c1c;
            border-left: 4px solid #ef4444;
        }
        
        /* Footer */
        .site-footer {
            background-color: #f8fafc;
            border-top: 1px solid #e2e8f0;
            padding: 1.5rem 0;
            text-align: center;
            margin-top: 3rem;
        }
        
        .footer-text {
            font-size: 0.9rem;
            color: #64748b;
        }
        
        .btn-back {
            background-color: #f3f4f6;
            color: #6b7280;
            border: none;
            padding: 8px 16px;
            border-radius: 6px;
            font-size: 0.9rem;
            transition: all 0.2s ease;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 6px;
        }
        
        .btn-back:hover {
            background-color: #e5e7eb;
            color: #4b5563;
        }
        
        /* Tab navigation for search options */
        .search-tabs {
            display: flex;
            margin-bottom: 20px;
            border-bottom: 1px solid #e2e8f0;
        }
        
        .search-tab {
            padding: 10px 20px;
            cursor: pointer;
            font-weight: 500;
            color: #64748b;
            border-bottom: 3px solid transparent;
            transition: all 0.3s ease;
        }
        
        .search-tab.active {
            color: var(--secondary-color);
            border-bottom-color: var(--secondary-color);
        }
        
        .search-tab:hover:not(.active) {
            color: var(--accent-color);
            border-bottom-color: #e2e8f0;
        }
        
        /* Tab content */
        .tab-content {
            display: none;
        }
        
        .tab-content.active {
            display: block;
        }
        
        /* Animation classes */
        .fade-in-up {
            animation: fadeInUp 0.6s ease forwards;
        }
        
        @keyframes fadeInUp {
            from { opacity: 0; transform: translateY(20px); }
            to { opacity: 1; transform: translateY(0); }
        }
        
        /* Responsive Styles */
        @media (max-width: 768px) {
            .certificate-badge {
                position: relative;
                top: 0;
                right: 0;
                margin: 1rem auto 0;
                display: inline-flex;
            }
            
            .certificate-header { padding-bottom: 2rem; }
            
            .participant-image { width: 150px; height: 150px; }
            
            .certificate-info-grid { grid-template-columns: 1fr; }
            
            .verification-section { flex-direction: column; gap: 1.5rem; }
            
            .search-tabs { flex-direction: column; border-bottom: none; }
            
            .search-tab {
                border-bottom: none;
                border-left: 3px solid transparent;
                padding: 10px 15px;
            }
            
            .search-tab.active {
                border-bottom: none;
                border-left-color: var(--secondary-color);
                background-color: #f0f9ff;
            }
        }
        
        /* Print styles */
        @media print {
            body { background-color: white; }
            
            .site-header, .verification-form, .site-footer, .action-buttons { display: none; }
            
            .certificate-card {
                box-shadow: none;
                margin: 0;
                border: 1px solid #e2e8f0;
            }
            
            .participant-image { width: 120px; height: 120px; }
        }
    </style>
</head>
<body>
    <!-- Header -->
    <header class="site-header">
        <div class="container">
            <div class="logo-container">
                <img src="https://res.cloudinary.com/dtw6t2l6t/image/upload/v1744720318/iicxacm_kjkhow.png" alt="IIC SIT X ACM SIT Logo" class="site-logo">
                <div>
                    <h1 class="site-title">IIC SIT X ACM SIT</h1>
                    <p class="site-tagline">Certificate Verification Portal</p>
                </div>
            </div>
        </div>
    </header>
    
    <!-- Main Content -->
    <div class="main-container">
        <?php if ($show_detail): ?>
            <!-- Certificate details -->
            <div class="certificate-card fade-in-up" data-aos="fade-up">
                <div class="certificate-header">
                    <div class="certificate-badge">
                        <i class="fas fa-shield-check"></i> Verified Certificate
                    </div>
                    <h2 class="certificate-title">Certificate Verification</h2>
                    <p class="certificate-subtitle">This certificate has been verified as authentic</p>
                </div>
                
                <div class="participant-section" data-aos="fade-up" data-aos-delay="100">
                    <img src="<?php echo htmlspecialchars($avatar); ?>" alt="<?php echo htmlspecialchars($name); ?>" class="participant-image">
                    <h3 class="participant-name"><?php echo htmlspecialchars($name); ?></h3>
                    <p class="participant-details"><?php echo htmlspecialchars($designation); ?> • <?php echo htmlspecialchars($institute); ?></p>
                    <?php if (!empty($unique_id)): ?>
                        <span class="unique-id-badge">ID: <?php echo htmlspecialchars($unique_id); ?></span>
                    <?php endif; ?>
                </div>
                
                <div class="certificate-body" data-aos="fade-up" data-aos-delay="200">
                    <h4 class="certificate-section-title">Certificate Details</h4>
                    <div class="certificate-info-grid">
                        <div class="info-card certificate">
                            <div class="info-label">
                                <i class="fas fa-certificate"></i> Certificate Number
                            </div>
                            <div class="info-value certificate-number-value">
                                <?php echo htmlspecialchars($certificate_number); ?>
                            </div>
                        </div>
                        
                        <div class="info-card workshop">
                            <div class="info-label">
                                <i class="fas fa-chalkboard-teacher"></i> Workshop Name
                            </div>
                            <div class="info-value">
                                <?php echo htmlspecialchars($workshop_name); ?>
                            </div>
                        </div>
                        
                        <div class="info-card institute">
                            <div class="info-label">
                                <i class="fas fa-users"></i> Organized By
                            </div>
                            <div class="info-value">
                                <?php echo htmlspecialchars($workshop_orgeniser); ?>
                            </div>
                        </div>
                        
                        <div class="info-card date">
                            <div class="info-label">
                                <i class="fas fa-calendar-alt"></i> Workshop Date
                            </div>
                            <div class="info-value">
                                <?php echo htmlspecialchars($formatted_date); ?>
                            </div>
                        </div>
                        
                        <?php if (!empty($batch)): ?>
                        <div class="info-card batch">
                            <div class="info-label">
                                <i class="fas fa-layer-group"></i> Batch
                            </div>
                            <div class="info-value">
                                <?php echo htmlspecialchars($batch); ?>
                            </div>
                        </div>
                        <?php endif; ?>
                    </div>
                    
                    <div class="action-buttons" data-aos="fade-up" data-aos-delay="300">
                        <button class="action-btn btn-primary" onclick="window.print()">
                            <i class="fas fa-print"></i> Print Certificate
                        </button>
                        <a href="<?php echo htmlspecialchars($verify_url); ?>" target="_blank" class="action-btn btn-outline">
                            <i class="fas fa-external-link-alt"></i> View Original
                        </a>
                    </div>
                </div>
                
                <div class="verification-section" data-aos="fade-up" data-aos-delay="400">
                    <div class="qr-container">
                        <?php if (!empty($qr_code_url)): ?>
                            <img src="<?php echo htmlspecialchars($qr_code_url); ?>" alt="Certificate QR Code" class="qr-code">
                        <?php else: ?>
                            <img src="https://api.qrserver.com/v1/create-qr-code/?data=<?php echo urlencode($_SERVER['REQUEST_URI']); ?>&size=150x150" alt="Certificate QR Code" class="qr-code">
                        <?php endif; ?>
                        <p class="qr-caption">Scan to verify this certificate</p>
                    </div>
                    
                    <div class="security-badge">
                        <i class="fas fa-shield-alt"></i>
                        <p class="security-text">This certificate has been digitally verified and is authentic. It includes a unique identification code for additional security.</p>
                    </div>
                </div>
            </div>
            
        <?php else: ?>
            <!-- Verification Form -->
            <div class="verification-form fade-in-up" data-aos="fade-up">
                <h3 class="form-title">Certificate Verification</h3>
                
                <?php if (!empty($error_message)): ?>
                    <div class="alert alert-danger">
                        <i class="fas fa-exclamation-triangle me-2"></i>
                        <strong>Certificate Not Found!</strong>
                        <p class="mb-0 mt-2"><?php echo htmlspecialchars($error_message); ?></p>
                    </div>
                <?php endif; ?>
                
                <!-- Search Tabs -->
                <div class="search-tabs">
                    <div class="search-tab active" id="tab-reference-id">Search by Reference ID</div>
                    <div class="search-tab" id="tab-certificate-number">Search by Certificate Number</div>
                </div>
                
                <!-- Search by Reference ID Form -->
                <div class="tab-content active" id="content-reference-id">
                    <form action="" method="get">
                        <div class="input-group">
                            <label for="rid" class="form-label">Certificate Reference ID</label>
                            <input type="text" id="rid" name="rid" class="form-input" placeholder="Enter your certificate reference ID" required>
                            <p class="form-hint">Enter the reference ID provided on your certificate or in your email.</p>
                        </div>
                        
                        <button type="submit" class="submit-btn">
                            <i class="fas fa-search me-2"></i> Verify Certificate
                        </button>
                    </form>
                </div>
                
                <!-- Search by Certificate Number Form -->
                <div class="tab-content" id="content-certificate-number">
                    <form action="" method="get">
                        <div class="input-group">
                            <label for="cert_num" class="form-label">Certificate Number</label>
                            <input type="text" id="cert_num" name="cert_num" class="form-input" placeholder="Enter your certificate number" required>
                            <p class="form-hint">Enter the certificate number that appears on your certificate document.</p>
                        </div>
                        
                        <button type="submit" class="submit-btn">
                            <i class="fas fa-search me-2"></i> Verify Certificate
                        </button>
                    </form>
                </div>
            </div>
        <?php endif; ?>
    </div>
    
    <!-- Footer -->
    <footer class="site-footer">
        <div class="container">
            <p class="footer-text">&copy; <?php echo date('Y'); ?> IIC SIT X ACM SIT. All rights reserved.</p>
            <p class="mt-2">
                <a href="./" class="btn-back">
                    <i class="fas fa-arrow-left"></i> Back to Home
                </a>
            </p>
        </div>
    </footer>
    
    <!-- JavaScript Libraries -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/aos/2.3.4/aos.js"></script>
    <script>
        // Initialize AOS animations and tab switching functionality
        document.addEventListener('DOMContentLoaded', function() {
            AOS.init({
                duration: 800,
                easing: 'ease-in-out',
                once: true
            });
            const tabs = document.querySelectorAll('.search-tab');
            const tabContents = document.querySelectorAll('.tab-content');
            
            tabs.forEach(tab => {
                tab.addEventListener('click', function() {
                    // Remove active class from all tabs
                    tabs.forEach(t => t.classList.remove('active'));
                    // Remove active class from all tab contents
                    tabContents.forEach(content => content.classList.remove('active'));
                    // Add active class to clicked tab
                    tab.classList.add('active');
                    // Determine target content based on tab id
                    const targetId = tab.id === 'tab-reference-id' ? 'content-reference-id' : 'content-certificate-number';
                    document.getElementById(targetId).classList.add('active');
                });
            });
        });
    </script>
</body>
</html>
