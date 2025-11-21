<?= $this->include('templates/header') ?>
<?= $this->include('templates/sidebar') ?>

<div class="main-content">
    <div class="top-navbar">
        <div>
            <button class="btn btn-link sidebar-toggle" id="sidebarToggle">
                <i class="bi bi-list fs-4"></i>
            </button>
            <span class="fs-5 fw-bold">User Management</span>
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
        
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <span><i class="bi bi-people me-2"></i>User List</span>
                <a href="<?= base_url('admin/users/create') ?>" class="btn btn-primary btn-sm">
                    <i class="bi bi-person-plus me-1"></i>Add User
                </a>
            </div>
            <div class="card-body">
                <form method="get" class="mb-3">
                    <div class="row g-2">
                        <div class="col-md-10">
                            <input type="text" name="search" class="form-control" placeholder="Search by name or email..." value="<?= esc($search ?? '') ?>">
                        </div>
                        <div class="col-md-2">
                            <button type="submit" class="btn btn-primary w-100">
                                <i class="bi bi-search"></i> Search
                            </button>
                        </div>
                    </div>
                </form>
                
                <?php if (empty($users)): ?>
                    <p class="text-muted text-center py-4">No users found</p>
                <?php else: ?>
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>Name</th>
                                    <th>Email</th>
                                    <th>Role</th>
                                    <th>Contact</th>
                                    <th>Status</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($users as $user): ?>
                                <tr>
                                    <td><?= esc($user['first_name'] . ' ' . $user['last_name']) ?></td>
                                    <td><?= esc($user['email']) ?></td>
                                    <td><span class="badge bg-info"><?= ucfirst(str_replace('_', ' ', $user['role_name'])) ?></span></td>
                                    <td><?= esc($user['contact_number'] ?? 'N/A') ?></td>
                                    <td>
                                        <?php if ($user['is_active']): ?>
                                            <span class="badge bg-success">Active</span>
                                        <?php else: ?>
                                            <span class="badge bg-danger">Inactive</span>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <div class="btn-group btn-group-sm">
                                            <a href="<?= base_url('admin/users/edit/' . $user['id']) ?>" class="btn btn-warning" title="Edit">
                                                <i class="bi bi-pencil"></i>
                                            </a>
                                            <form method="post" action="<?= base_url('admin/users/toggle/' . $user['id']) ?>" class="d-inline">
                                                <?= csrf_field() ?>
                                                <button type="submit" class="btn <?= $user['is_active'] ? 'btn-secondary' : 'btn-success' ?>" title="<?= $user['is_active'] ? 'Deactivate' : 'Activate' ?>">
                                                    <i class="bi bi-<?= $user['is_active'] ? 'x-circle' : 'check-circle' ?>"></i>
                                                </button>
                                            </form>
                                            <?php if ($user['id'] != session()->get('user_id')): ?>
                                            <form method="post" action="<?= base_url('admin/users/delete/' . $user['id']) ?>" class="d-inline" onsubmit="return confirmDelete()">
                                                <?= csrf_field() ?>
                                                <button type="submit" class="btn btn-danger" title="Delete">
                                                    <i class="bi bi-trash"></i>
                                                </button>
                                            </form>
                                            <?php endif; ?>
                                        </div>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                    
                    <?php if ($pager): ?>
                        <div class="mt-3">
                            <?= $pager->links() ?>
                        </div>
                    <?php endif; ?>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<?= $this->include('templates/footer') ?>