<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Forgot Password - ITSO Equipment Management</title>
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
        .forgot-card {
            max-width: 500px;
            width: 100%;
            background: white;
            border-radius: 1rem;
            box-shadow: 0 20px 60px rgba(0,0,0,0.3);
            overflow: hidden;
        }
        .forgot-header {
            background: linear-gradient(135deg, #1e3a8a 0%, #1e40af 100%);
            color: white;
            padding: 2rem;
            text-align: center;
        }
        .forgot-header i {
            font-size: 2.5rem;
            margin-bottom: 0.5rem;
        }
        .forgot-body {
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
        <div class="forgot-card mx-auto">
            <div class="forgot-header">
                <i class="bi bi-key-fill"></i>
                <h3 class="mb-0">Forgot Password</h3>
                <p class="mb-0 mt-2">Reset your password</p>
            </div>
            <div class="forgot-body">
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
                
                <div class="alert alert-info">
                    <i class="bi bi-info-circle me-2"></i>
                    Enter your email address and we'll send you a link to reset your password.
                </div>
                
                <form action="<?= base_url('auth/forgot-password') ?>" method="post">
                    <?= csrf_field() ?>
                    
                    <div class="mb-3">
                        <label class="form-label">Email Address</label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="bi bi-envelope"></i></span>
                            <input type="email" name="email" class="form-control" required 
                                   value="<?= old('email') ?>" placeholder="Enter your email address">
                        </div>
                    </div>
                    
                    <div class="d-grid mb-3">
                        <button type="submit" class="btn btn-primary btn-reset">
                            <i class="bi bi-send me-2"></i>Send Reset Link
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