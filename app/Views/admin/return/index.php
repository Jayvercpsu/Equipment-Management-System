<?= $this->include('templates/header') ?>
<?= $this->include('templates/sidebar') ?>

<div class="main-content">
    <div class="top-navbar">
        <div>
            <button class="btn btn-link sidebar-toggle" id="sidebarToggle">
                <i class="bi bi-list fs-4"></i>
            </button>
            <span class="fs-5 fw-bold">Return Management</span>
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
        
        <div class="row">
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header">
                        <i class="bi bi-clock-history me-2"></i>Active Borrows (Pending Return)
                    </div>
                    <div class="card-body">
                        <?php if (empty($active_borrows)): ?>
                            <p class="text-muted text-center py-4">No active borrows</p>
                        <?php else: ?>
                            <div class="table-responsive">
                                <table class="table table-sm table-hover">
                                    <thead>
                                        <tr>
                                            <th>Borrow Code</th>
                                            <th>Equipment</th>
                                            <th>Borrower</th>
                                            <th>Action</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($active_borrows as $borrow): ?>
                                        <tr>
                                            <td><?= esc($borrow['borrow_code']) ?></td>
                                            <td><?= esc($borrow['equipment_name']) ?></td>
                                            <td><?= esc($borrow['first_name'] . ' ' . $borrow['last_name']) ?></td>
                                            <td>
                                                <?php if ($borrow['status'] === 'approved'): ?>
                                                <a href="<?= base_url('admin/return/create/' . $borrow['id']) ?>" class="btn btn-sm btn-primary">
                                                    <i class="bi bi-arrow-left-circle"></i> Return
                                                </a>
                                                <?php else: ?>
                                                <span class="badge bg-warning"><?= ucfirst($borrow['status']) ?></span>
                                                <?php endif; ?>
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
            
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header">
                        <i class="bi bi-check-circle me-2"></i>Recent Returns
                    </div>
                    <div class="card-body">
                        <?php if (empty($returns)): ?>
                            <p class="text-muted text-center py-4">No return records</p>
                        <?php else: ?>
                            <div class="table-responsive">
                                <table class="table table-sm table-hover">
                                    <thead>
                                        <tr>
                                            <th>Borrow Code</th>
                                            <th>Equipment</th>
                                            <th>Return Date</th>
                                            <th>Condition</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach (array_slice($returns, 0, 10) as $return): ?>
                                        <tr>
                                            <td><?= esc($return['borrow_code']) ?></td>
                                            <td><?= esc($return['equipment_name']) ?></td>
                                            <td><?= date('M d, Y', strtotime($return['return_date'])) ?></td>
                                            <td>
                                                <?php
                                                $conditionColors = [
                                                    'good' => 'success',
                                                    'damaged' => 'warning',
                                                    'lost' => 'danger'
                                                ];
                                                $color = $conditionColors[$return['condition_on_return']] ?? 'secondary';
                                                ?>
                                                <span class="badge bg-<?= $color ?>"><?= ucfirst($return['condition_on_return']) ?></span>
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
    </div>
</div>

<?= $this->include('templates/footer') ?>