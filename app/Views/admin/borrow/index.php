<?= $this->include('templates/header') ?>
<?= $this->include('templates/sidebar') ?>

<div class="main-content">
    <div class="top-navbar">
        <div>
            <button class="btn btn-link sidebar-toggle" id="sidebarToggle">
                <i class="bi bi-list fs-4"></i>
            </button>
            <span class="fs-5 fw-bold">Borrow Management</span>
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
                <span><i class="bi bi-arrow-right-circle me-2"></i>Borrow Records</span>
                <a href="<?= base_url('admin/borrow/create') ?>" class="btn btn-primary btn-sm">
                    <i class="bi bi-plus-circle me-1"></i>New Borrow
                </a>
            </div>
            <div class="card-body">
                <?php if (empty($borrows)): ?>
                    <p class="text-muted text-center py-4">No borrow records found</p>
                <?php else: ?>
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>Borrow Code</th>
                                    <th>Equipment</th>
                                    <?php if (session()->get('role_name') === 'itso_personnel'): ?>
                                    <th>Borrower</th>
                                    <?php endif; ?>
                                    <th>Borrow Date</th>
                                    <th>Due Date</th>
                                    <th>Status</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($borrows as $borrow): ?>
                                <tr>
                                    <td><strong><?= esc($borrow['borrow_code']) ?></strong></td>
                                    <td><?= esc($borrow['equipment_name'] ?? 'N/A') ?></td>
                                    <?php if (session()->get('role_name') === 'itso_personnel'): ?>
                                    <td><?= esc(($borrow['first_name'] ?? '') . ' ' . ($borrow['last_name'] ?? '')) ?></td>
                                    <?php endif; ?>
                                    <td><?= date('M d, Y', strtotime($borrow['borrow_date'])) ?></td>
                                    <td><?= date('M d, Y', strtotime($borrow['due_date'])) ?></td>
                                    <td>
                                        <?php
                                        $statusColors = [
                                            'pending' => 'warning',
                                            'approved' => 'success',
                                            'returned' => 'info',
                                            'overdue' => 'danger',
                                            'lost' => 'dark'
                                        ];
                                        $color = $statusColors[$borrow['status']] ?? 'secondary';
                                        ?>
                                        <span class="badge bg-<?= $color ?>"><?= ucfirst($borrow['status']) ?></span>
                                    </td>
                                    <td>
                                        <div class="btn-group btn-group-sm">
                                            <a href="<?= base_url('admin/borrow/view/' . $borrow['id']) ?>" class="btn btn-info" title="View">
                                                <i class="bi bi-eye"></i>
                                            </a>
                                            <?php if ($borrow['status'] === 'pending' && session()->get('role_name') === 'itso_personnel'): ?>
                                            <form method="post" action="<?= base_url('admin/borrow/approve/' . $borrow['id']) ?>" class="d-inline">
                                                <?= csrf_field() ?>
                                                <button type="submit" class="btn btn-success" title="Approve">
                                                    <i class="bi bi-check-lg"></i>
                                                </button>
                                            </form>
                                            <?php endif; ?>
                                            <?php if ($borrow['status'] === 'pending'): ?>
                                            <form method="post" action="<?= base_url('admin/borrow/cancel/' . $borrow['id']) ?>" class="d-inline" onsubmit="return confirmDelete('Cancel this borrow request?')">
                                                <?= csrf_field() ?>
                                                <button type="submit" class="btn btn-danger" title="Cancel">
                                                    <i class="bi bi-x-lg"></i>
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
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<?= $this->include('templates/footer') ?>