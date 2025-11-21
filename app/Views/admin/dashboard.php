<?= $this->include('templates/header') ?>
<?= $this->include('templates/sidebar') ?>

<div class="main-content">
    <div class="top-navbar">
        <div>
            <button class="btn btn-link sidebar-toggle" id="sidebarToggle">
                <i class="bi bi-list fs-4"></i>
            </button>
            <span class="fs-5 fw-bold">Dashboard</span>
        </div>
        <div>
            <span class="text-muted">Welcome, <?= esc(session()->get('first_name')) ?></span>
        </div>
    </div>
    
    <div class="content-area">
        <?php if (session()->getFlashdata('success')): ?>
            <div class="alert alert-success alert-dismissible fade show">
                <i class="bi bi-check-circle me-2"></i><?= session()->getFlashdata('success') ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        <?php endif; ?>
        
        <div class="row g-3 mb-4">
            <div class="col-md-3">
                <div class="card border-start border-primary border-4">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <h6 class="text-muted mb-1">Total Equipment</h6>
                                <h2 class="mb-0"><?= $total_equipment ?></h2>
                            </div>
                            <div class="fs-1 text-primary">
                                <i class="bi bi-laptop"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="col-md-3">
                <div class="card border-start border-success border-4">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <h6 class="text-muted mb-1">Available</h6>
                                <h2 class="mb-0"><?= $available_equipment ?></h2>
                            </div>
                            <div class="fs-1 text-success">
                                <i class="bi bi-check-circle"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="col-md-3">
                <div class="card border-start border-warning border-4">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <h6 class="text-muted mb-1">Borrowed</h6>
                                <h2 class="mb-0"><?= $borrowed_equipment ?></h2>
                            </div>
                            <div class="fs-1 text-warning">
                                <i class="bi bi-arrow-right-circle"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="col-md-3">
                <div class="card border-start border-danger border-4">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <h6 class="text-muted mb-1">Unusable</h6>
                                <h2 class="mb-0"><?= $unusable_equipment ?></h2>
                            </div>
                            <div class="fs-1 text-danger">
                                <i class="bi bi-x-circle"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="row g-3 mb-4">
            <div class="col-md-4">
                <div class="card">
                    <div class="card-body text-center">
                        <i class="bi bi-people fs-1 text-info mb-2"></i>
                        <h3 class="mb-0"><?= $total_users ?></h3>
                        <p class="text-muted mb-0">Total Users</p>
                    </div>
                </div>
            </div>
            
            <div class="col-md-4">
                <div class="card">
                    <div class="card-body text-center">
                        <i class="bi bi-clock-history fs-1 text-warning mb-2"></i>
                        <h3 class="mb-0"><?= $active_borrows ?></h3>
                        <p class="text-muted mb-0">Active Borrows</p>
                    </div>
                </div>
            </div>
            
            <div class="col-md-4">
                <div class="card">
                    <div class="card-body text-center">
                        <i class="bi bi-exclamation-triangle fs-1 text-danger mb-2"></i>
                        <h3 class="mb-0"><?= $overdue_borrows ?></h3>
                        <p class="text-muted mb-0">Overdue Items</p>
                    </div>
                </div>
            </div>
        </div>
        
        <?php if (session()->get('role_name') !== 'itso_personnel' && isset($my_borrows)): ?>
        <div class="card mb-4">
            <div class="card-header">
                <i class="bi bi-list-check me-2"></i>My Recent Borrows
            </div>
            <div class="card-body">
                <?php if (empty($my_borrows)): ?>
                    <p class="text-muted mb-0">No borrow records found</p>
                <?php else: ?>
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>Borrow Code</th>
                                    <th>Equipment</th>
                                    <th>Borrow Date</th>
                                    <th>Due Date</th>
                                    <th>Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach (array_slice($my_borrows, 0, 5) as $borrow): ?>
                                <tr>
                                    <td><?= esc($borrow['borrow_code']) ?></td>
                                    <td><?= esc($borrow['equipment_name']) ?></td>
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
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                <?php endif; ?>
            </div>
        </div>
        <?php endif; ?>
        
        <?php if (session()->get('role_name') === 'itso_personnel'): ?>
        <div class="row g-3">
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header">
                        <i class="bi bi-clock-history me-2"></i>Recent Borrows
                    </div>
                    <div class="card-body">
                        <?php if (empty($recent_borrows)): ?>
                            <p class="text-muted mb-0">No borrow records found</p>
                        <?php else: ?>
                            <div class="table-responsive">
                                <table class="table table-sm table-hover mb-0">
                                    <thead>
                                        <tr>
                                            <th>User</th>
                                            <th>Equipment</th>
                                            <th>Status</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach (array_slice($recent_borrows, 0, 5) as $borrow): ?>
                                        <tr>
                                            <td><?= esc($borrow['first_name'] . ' ' . $borrow['last_name']) ?></td>
                                            <td><?= esc($borrow['equipment_name']) ?></td>
                                            <td>
                                                <?php
                                                $statusColors = [
                                                    'pending' => 'warning',
                                                    'approved' => 'success',
                                                    'returned' => 'info',
                                                    'overdue' => 'danger'
                                                ];
                                                $color = $statusColors[$borrow['status']] ?? 'secondary';
                                                ?>
                                                <span class="badge bg-<?= $color ?>"><?= ucfirst($borrow['status']) ?></span>
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
                        <i class="bi bi-arrow-left-circle me-2"></i>Recent Returns
                    </div>
                    <div class="card-body">
                        <?php if (empty($recent_returns)): ?>
                            <p class="text-muted mb-0">No return records found</p>
                        <?php else: ?>
                            <div class="table-responsive">
                                <table class="table table-sm table-hover mb-0">
                                    <thead>
                                        <tr>
                                            <th>User</th>
                                            <th>Equipment</th>
                                            <th>Condition</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach (array_slice($recent_returns, 0, 5) as $return): ?>
                                        <tr>
                                            <td><?= esc($return['first_name'] . ' ' . $return['last_name']) ?></td>
                                            <td><?= esc($return['equipment_name']) ?></td>
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
        <?php endif; ?>
    </div>
</div>

<?= $this->include('templates/footer') ?>