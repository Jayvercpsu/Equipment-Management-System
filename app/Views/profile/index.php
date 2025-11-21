<?= $this->include('templates/header') ?>
<?= $this->include('templates/sidebar') ?>

<div class="main-content">
    <div class="top-navbar">
        <div>
            <button class="btn btn-link sidebar-toggle" id="sidebarToggle">
                <i class="bi bi-list fs-4"></i>
            </button>
            <span class="fs-5 fw-bold">My Profile</span>
        </div>
    </div>
    
    <div class="content-area">
        <?php if (session()->getFlashdata('success')): ?>
            <div class="alert alert-success alert-dismissible fade show">
                <i class="bi bi-check-circle me-2"></i><?= session()->getFlashdata('success') ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        <?php endif; ?>

        <?php if (session()->getFlashdata('error')): ?>
            <div class="alert alert-danger alert-dismissible fade show">
                <i class="bi bi-x-circle me-2"></i><?= session()->getFlashdata('error') ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        <?php endif; ?>

        <?php if (session()->getFlashdata('errors')): ?>
            <div class="alert alert-danger alert-dismissible fade show">
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                <ul class="mb-0">
                    <?php foreach (session()->getFlashdata('errors') as $error): ?>
                        <li><?= esc($error) ?></li>
                    <?php endforeach; ?>
                </ul>
            </div>
        <?php endif; ?>

        <div class="row">
            <!-- Profile Sidebar -->
            <div class="col-md-4 col-lg-3 mb-4">
                <div class="card">
                    <div class="card-body text-center">
                        <div class="profile-user-img bg-primary rounded-circle d-inline-flex align-items-center justify-content-center mb-3" 
                             style="width: 100px; height: 100px;">
                            <span class="text-white fs-1 fw-bold">
                                <?= strtoupper(substr($user['first_name'], 0, 1)) ?>
                            </span>
                        </div>

                        <h5 class="mb-1"><?= esc($user['first_name'] . ' ' . $user['last_name']) ?></h5>

                        <span class="badge bg-<?= $user['role_id'] == 1 ? 'primary' : 'info' ?> mb-3">
                            <?= $user['role_id'] == 1 ? 'ITSO Personnel' : 'Student/Faculty' ?>
                        </span>

                        <ul class="list-group list-group-flush text-start">
                            <li class="list-group-item d-flex justify-content-between align-items-center px-0">
                                <strong>UUID</strong>
                                <span class="text-muted small"><?= esc(substr($user['uuid'], 0, 8)) ?>...</span>
                            </li>
                            <li class="list-group-item d-flex justify-content-between align-items-center px-0">
                                <strong>Email</strong>
                                <span class="text-muted small text-end" style="max-width: 150px; word-break: break-word;">
                                    <?= esc($user['email']) ?>
                                </span>
                            </li>
                            <li class="list-group-item d-flex justify-content-between align-items-center px-0">
                                <strong>Contact</strong>
                                <span class="text-muted"><?= esc($user['contact_number'] ?: 'N/A') ?></span>
                            </li>
                            <li class="list-group-item d-flex justify-content-between align-items-center px-0">
                                <strong>Status</strong>
                                <span class="badge bg-<?= $user['is_active'] ? 'success' : 'danger' ?>">
                                    <?= $user['is_active'] ? 'Active' : 'Inactive' ?>
                                </span>
                            </li>
                            <li class="list-group-item d-flex justify-content-between align-items-center px-0">
                                <strong>Email Verified</strong>
                                <span>
                                    <?php if ($user['email_verified_at']): ?>
                                        <i class="bi bi-check-circle-fill text-success"></i>
                                    <?php else: ?>
                                        <i class="bi bi-x-circle-fill text-danger"></i>
                                    <?php endif; ?>
                                </span>
                            </li>
                            <li class="list-group-item d-flex justify-content-between align-items-center px-0 border-bottom-0">
                                <strong>Member Since</strong>
                                <span class="text-muted small"><?= date('M d, Y', strtotime($user['created_at'])) ?></span>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>

            <!-- Profile Content -->
            <div class="col-md-8 col-lg-9">
                <div class="card">
                    <div class="card-header">
                        <ul class="nav nav-tabs card-header-tabs" role="tablist">
                            <li class="nav-item">
                                <a class="nav-link active" id="profile-tab" data-bs-toggle="tab" href="#profile" role="tab">
                                    <i class="bi bi-person me-2"></i>Update Profile
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" id="password-tab" data-bs-toggle="tab" href="#password" role="tab">
                                    <i class="bi bi-key me-2"></i>Change Password
                                </a>
                            </li>
                        </ul>
                    </div>
                    <div class="card-body">
                        <div class="tab-content">
                            <!-- Update Profile Tab -->
                            <div class="tab-pane fade show active" id="profile" role="tabpanel">
                                <form action="<?= base_url('profile/update') ?>" method="post">
                                    <?= csrf_field() ?>
                                    
                                    <div class="row mb-3">
                                        <label for="first_name" class="col-sm-3 col-form-label">First Name <span class="text-danger">*</span></label>
                                        <div class="col-sm-9">
                                            <input type="text" class="form-control" id="first_name" name="first_name" 
                                                   value="<?= esc($user['first_name']) ?>" required>
                                        </div>
                                    </div>

                                    <div class="row mb-3">
                                        <label for="last_name" class="col-sm-3 col-form-label">Last Name <span class="text-danger">*</span></label>
                                        <div class="col-sm-9">
                                            <input type="text" class="form-control" id="last_name" name="last_name" 
                                                   value="<?= esc($user['last_name']) ?>" required>
                                        </div>
                                    </div>

                                    <div class="row mb-3">
                                        <label for="email" class="col-sm-3 col-form-label">Email <span class="text-danger">*</span></label>
                                        <div class="col-sm-9">
                                            <input type="email" class="form-control" id="email" name="email" 
                                                   value="<?= esc($user['email']) ?>" required>
                                            <?php if (!$user['email_verified_at']): ?>
                                                <small class="form-text text-warning">
                                                    <i class="bi bi-exclamation-triangle me-1"></i>Your email is not verified yet
                                                </small>
                                            <?php endif; ?>
                                        </div>
                                    </div>

                                    <div class="row mb-3">
                                        <label for="contact_number" class="col-sm-3 col-form-label">Contact Number</label>
                                        <div class="col-sm-9">
                                            <input type="text" class="form-control" id="contact_number" name="contact_number" 
                                                   value="<?= esc($user['contact_number']) ?>" placeholder="09XXXXXXXXX">
                                        </div>
                                    </div>

                                    <div class="row">
                                        <div class="col-sm-9 offset-sm-3">
                                            <button type="submit" class="btn btn-primary">
                                                <i class="bi bi-save me-2"></i>Update Profile
                                            </button>
                                        </div>
                                    </div>
                                </form>
                            </div>

                            <!-- Change Password Tab -->
                            <div class="tab-pane fade" id="password" role="tabpanel">
                                <form action="<?= base_url('profile/update') ?>" method="post">
                                    <?= csrf_field() ?>
                                    
                                    <input type="hidden" name="first_name" value="<?= esc($user['first_name']) ?>">
                                    <input type="hidden" name="last_name" value="<?= esc($user['last_name']) ?>">
                                    <input type="hidden" name="email" value="<?= esc($user['email']) ?>">
                                    <input type="hidden" name="contact_number" value="<?= esc($user['contact_number']) ?>">

                                    <div class="alert alert-info">
                                        <i class="bi bi-info-circle me-2"></i>Password must be at least 8 characters long
                                    </div>

                                    <div class="row mb-3">
                                        <label for="current_password" class="col-sm-3 col-form-label">Current Password <span class="text-danger">*</span></label>
                                        <div class="col-sm-9">
                                            <input type="password" class="form-control" id="current_password" 
                                                   name="current_password" required>
                                        </div>
                                    </div>

                                    <div class="row mb-3">
                                        <label for="new_password" class="col-sm-3 col-form-label">New Password <span class="text-danger">*</span></label>
                                        <div class="col-sm-9">
                                            <input type="password" class="form-control" id="new_password" 
                                                   name="new_password" required>
                                            <small class="form-text text-muted">Minimum 8 characters</small>
                                        </div>
                                    </div>

                                    <div class="row mb-3">
                                        <label for="confirm_password" class="col-sm-3 col-form-label">Confirm Password <span class="text-danger">*</span></label>
                                        <div class="col-sm-9">
                                            <input type="password" class="form-control" id="confirm_password" 
                                                   name="confirm_password" required>
                                        </div>
                                    </div>

                                    <div class="row">
                                        <div class="col-sm-9 offset-sm-3">
                                            <button type="submit" class="btn btn-primary">
                                                <i class="bi bi-key me-2"></i>Change Password
                                            </button>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?= $this->include('templates/footer') ?>