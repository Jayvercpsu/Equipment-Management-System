<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register - ITSO Equipment Management</title>
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
            padding: 2rem 0;
        }
        .register-card {
            max-width: 600px;
            width: 100%;
            background: white;
            border-radius: 1rem;
            box-shadow: 0 20px 60px rgba(0,0,0,0.3);
            overflow: hidden;
        }
        .register-header {
            background: linear-gradient(135deg, #1e3a8a 0%, #1e40af 100%);
            color: white;
            padding: 2rem;
            text-align: center;
        }
        .register-header i {
            font-size: 2.5rem;
            margin-bottom: 0.5rem;
        }
        .register-body {
            padding: 2rem;
        }
        .form-control:focus {
            border-color: #1e40af;
            box-shadow: 0 0 0 0.2rem rgba(30, 64, 175, 0.25);
        }
        .btn-register {
            background: linear-gradient(135deg, #1e3a8a 0%, #1e40af 100%);
            border: none;
            padding: 0.75rem;
            font-weight: 600;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="register-card mx-auto">
            <div class="register-header">
                <i class="bi bi-person-plus-fill"></i>
                <h3 class="mb-0">Create Account</h3>
                <p class="mb-0 mt-2">ITSO Equipment System</p>
            </div>
            <div class="register-body">
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
                
                <?php if (session()->getFlashdata('error')): ?>
                    <div class="alert alert-danger alert-dismissible fade show">
                        <?= session()->getFlashdata('error') ?>
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                <?php endif; ?>
                
                <form method="post" action="<?= base_url('auth/register') ?>">
                    <?= csrf_field() ?>
                    
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label class="form-label">First Name <span class="text-danger">*</span></label>
                            <input type="text" name="first_name" class="form-control" required 
                                   value="<?= old('first_name') ?>">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Last Name <span class="text-danger">*</span></label>
                            <input type="text" name="last_name" class="form-control" required 
                                   value="<?= old('last_name') ?>">
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label">Email Address <span class="text-danger">*</span></label>
                        <input type="email" name="email" class="form-control" required 
                               value="<?= old('email') ?>">
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label">Contact Number <span class="text-danger">*</span></label>
                        <input type="text" name="contact_number" class="form-control" required 
                               value="<?= old('contact_number') ?>">
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label">Role <span class="text-danger">*</span></label>
                        <select name="role_id" class="form-select" required>
                            <option value="">Select Role</option>
                            <?php foreach ($roles as $role): ?>
                                <option value="<?= $role['id'] ?>" <?= old('role_id') == $role['id'] ? 'selected' : '' ?>>
                                    <?= ucfirst($role['name']) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label">Password <span class="text-danger">*</span></label>
                        <input type="password" name="password" class="form-control" required>
                        <small class="text-muted">Min 8 characters, at least one uppercase letter and one number</small>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label">Confirm Password <span class="text-danger">*</span></label>
                        <input type="password" name="password_confirm" class="form-control" required>
                    </div>
                    
                    <div class="d-grid">
                        <button type="submit" class="btn btn-primary btn-register">
                            <i class="bi bi-person-plus me-2"></i>Register
                        </button>
                    </div>
                </form>
                
                <div class="text-center mt-3">
                    Already have an account? 
                    <a href="<?= base_url('auth/login') ?>" class="text-decoration-none">Login here</a>
                </div>
            </div>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>