<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reset Password - ITSO Equipment Management</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css">
    <style>
        body {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
        .reset-card {
            max-width: 500px;
            width: 100%;
            background: white;
            border-radius: 1rem;
            box-shadow: 0 20px 60px rgba(0,0,0,0.3);
            overflow: hidden;
        }
        .reset-header {
            background: linear-gradient(135deg, #1e3a8a 0%, #1e40af 100%);
            color: white;
            padding: 2rem;
            text-align: center;
        }
        .reset-header i {
            font-size: 2.5rem;
            margin-bottom: 0.5rem;
        }
        .reset-body {
            padding: 2rem;
        }
        .form-control:focus {
            border-color: #1e40af;
            box-shadow: 0 0 0 0.2rem rgba(30, 64, 175, 0.25);
        }
        .btn-reset {
            background: linear-gradient(135deg, #1e3a8a 0%, #1e40af 100%);
            border: none;
            padding: 0.75rem;
            font-weight: 600;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="reset-card mx-auto">
            <div class="reset-header">
                <i class="bi bi-shield-lock-fill"></i>
                <h3 class="mb-0">Reset Password</h3>
                <p class="mb-0 mt-2">Enter your new password</p>
            </div>
            <div class="reset-body">
                <?php if (session()->getFlashdata('success')): ?>
                    <div class="alert alert-success alert-dismissible fade show">
                        <?= session()->getFlashdata('success') ?>
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                <?php endif; ?>
                
                <?php if (session()->getFlashdata('error')): ?>
                    <div class="alert alert-danger alert-dismissible fade show">
                        <?= session()->getFlashdata('error') ?>
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                <?php endif; ?>
                
                <?php if (session()->getFlashdata('errors')): ?>
                    <div class="alert alert-danger alert-dismissible fade show">
                        <ul class="mb-0">
                            <?php foreach (session()->getFlashdata('errors') as $error): ?>
                                <li><?= esc($error) ?></li>
                            <?php endforeach; ?>
                        </ul>
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                <?php endif; ?>
                
                <form action="<?= base_url('auth/reset-password') ?>" method="post">
                    <?= csrf_field() ?>
                    <input type="hidden" name="token" value="<?= esc($token) ?>">
                    
                    <div class="mb-3">
                        <label class="form-label">New Password <span class="text-danger">*</span></label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="bi bi-lock"></i></span>
                            <input type="password" name="password" class="form-control" required 
                                   placeholder="Enter new password">
                        </div>
                        <small class="text-muted">Min 8 characters, at least one uppercase letter and one number</small>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label">Confirm Password <span class="text-danger">*</span></label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="bi bi-lock-fill"></i></span>
                            <input type="password" name="password_confirm" class="form-control" required 
                                   placeholder="Confirm new password">
                        </div>
                    </div>
                    
                    <div class="d-grid mb-3">
                        <button type="submit" class="btn btn-primary btn-reset">
                            <i class="bi bi-check-circle me-2"></i>Reset Password
                        </button>
                    </div>
                </form>
                
                <div class="text-center">
                    <a href="<?= base_url('auth/login') ?>" class="text-decoration-none">
                        <i class="bi bi-arrow-left me-1"></i>Back to Login
                    </a>
                </div>
            </div>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>