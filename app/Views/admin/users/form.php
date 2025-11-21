<?= $this->include('templates/header') ?>
<?= $this->include('templates/sidebar') ?>

<div class="main-content">
    <div class="top-navbar">
        <div>
            <button class="btn btn-link sidebar-toggle" id="sidebarToggle">
                <i class="bi bi-list fs-4"></i>
            </button>
            <span class="fs-5 fw-bold"><?= $user ? 'Edit' : 'Add' ?> User</span>
        </div>
    </div>
    
    <div class="content-area">
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
        
        <div class="row">
            <div class="col-md-8 mx-auto">
                <div class="card">
                    <div class="card-header">
                        <i class="bi bi-person me-2"></i><?= $user ? 'Edit' : 'Add New' ?> User
                    </div>
                    <div class="card-body">
                        <form method="post" action="<?= $user ? base_url('admin/users/update/' . $user['id']) : base_url('admin/users/store') ?>">
                            <?= csrf_field() ?>
                            
                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <label class="form-label">First Name <span class="text-danger">*</span></label>
                                    <input type="text" name="first_name" class="form-control" required 
                                           value="<?= old('first_name', $user['first_name'] ?? '') ?>">
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">Last Name <span class="text-danger">*</span></label>
                                    <input type="text" name="last_name" class="form-control" required 
                                           value="<?= old('last_name', $user['last_name'] ?? '') ?>">
                                </div>
                            </div>
                            
                            <div class="mb-3">
                                <label class="form-label">Email <span class="text-danger">*</span></label>
                                <input type="email" name="email" class="form-control" required 
                                       value="<?= old('email', $user['email'] ?? '') ?>">
                            </div>
                            
                            <div class="mb-3">
                                <label class="form-label">Contact Number <span class="text-danger">*</span></label>
                                <input type="text" name="contact_number" class="form-control" required 
                                       value="<?= old('contact_number', $user['contact_number'] ?? '') ?>">
                            </div>
                            
                            <div class="mb-3">
                                <label class="form-label">Role <span class="text-danger">*</span></label>
                                <select name="role_id" class="form-select" required>
                                    <option value="">Select Role</option>
                                    <?php foreach ($roles as $role): ?>
                                        <option value="<?= $role['id'] ?>" <?= ($user['role_id'] ?? '') == $role['id'] ? 'selected' : '' ?>>
                                            <?= ucfirst(str_replace('_', ' ', $role['name'])) ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            
                            <?php if (!$user): ?>
                            <div class="mb-3">
                                <label class="form-label">Password <span class="text-danger">*</span></label>
                                <input type="password" name="password" class="form-control" required>
                                <small class="text-muted">Min 8 characters, at least one uppercase and one number</small>
                            </div>
                            
                            <div class="mb-3">
                                <label class="form-label">Confirm Password <span class="text-danger">*</span></label>
                                <input type="password" name="password_confirm" class="form-control" required>
                            </div>
                            <?php else: ?>
                            <div class="mb-3">
                                <label class="form-label">New Password</label>
                                <input type="password" name="password" class="form-control">
                                <small class="text-muted">Leave blank to keep current password</small>
                            </div>
                            
                            <div class="mb-3">
                                <label class="form-label">Confirm New Password</label>
                                <input type="password" name="password_confirm" class="form-control">
                            </div>
                            <?php endif; ?>
                            
                            <div class="mb-3">
                                <div class="form-check">
                                    <input type="checkbox" name="is_active" class="form-check-input" 
                                           <?= ($user['is_active'] ?? 1) ? 'checked' : '' ?> value="1">
                                    <label class="form-check-label">Active</label>
                                </div>
                            </div>
                            
                            <div class="d-flex gap-2">
                                <button type="submit" class="btn btn-primary">
                                    <i class="bi bi-save me-1"></i>Save User
                                </button>
                                <a href="<?= base_url('admin/users') ?>" class="btn btn-secondary">
                                    <i class="bi bi-x-circle me-1"></i>Cancel
                                </a>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?= $this->include('templates/footer') ?>