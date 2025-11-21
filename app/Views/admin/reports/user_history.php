<?= $this->include('templates/header') ?>
<?= $this->include('templates/sidebar') ?>

<div class="main-content">
    <div class="top-navbar">
        <div>
            <button class="btn btn-link sidebar-toggle" id="sidebarToggle">
                <i class="bi bi-list fs-4"></i>
            </button>
            <span class="fs-5 fw-bold">User Borrowing History</span>
        </div>
    </div>
    
    <div class="content-area">
        <div class="card">
            <div class="card-header">
                <i class="bi bi-clock-history me-2"></i>Filter History
            </div>
            <div class="card-body">
                <form method="get" action="<?= base_url('admin/reports/user-history') ?>">
                    <div class="row g-3">
                        <div class="col-md-4">
                            <label class="form-label">Select User</label>
                            <select name="user_id" class="form-select" required>
                                <option value="">Choose a user...</option>
                                <?php foreach ($users as $user): ?>
                                    <option value="<?= $user['id'] ?>" <?= $selected_user == $user['id'] ? 'selected' : '' ?>>
                                        <?= esc($user['first_name'] . ' ' . $user['last_name']) ?> - <?= esc($user['email']) ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">From Date</label>
                            <input type="date" name="date_from" class="form-control" value="<?= $date_from ?? '' ?>">
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">To Date</label>
                            <input type="date" name="date_to" class="form-control" value="<?= $date_to ?? '' ?>">
                        </div>
                        <div class="col-md-2">
                            <label class="form-label">&nbsp;</label>
                            <button type="submit" class="btn btn-primary w-100">
                                <i class="bi bi-search"></i> Search
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
        
        <?php if (!empty($borrows)): ?>
        <div class="card mt-3">
            <div class="card-header d-flex justify-content-between align-items-center">
                <span>History Results (<?= count($borrows) ?> records)</span>
                <a href="<?= base_url('admin/reports/export/user-history?format=csv&user_id=' . $selected_user . ($date_from ? '&date_from=' . $date_from : '') . ($date_to ? '&date_to=' . $date_to : '')) ?>" class="btn btn-success btn-sm">
                    <i class="bi bi-download me-1"></i>Export CSV
                </a>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-bordered table-hover">
                        <thead class="table-light">
                            <tr>
                                <th>Borrow Code</th>
                                <th>Equipment</th>
                                <th>Item ID</th>
                                <th>Borrow Date</th>
                                <th>Due Date</th>
                                <th>Return Date</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($borrows as $borrow): ?>
                            <tr>
                                <td><strong><?= esc($borrow['borrow_code']) ?></strong></td>
                                <td><?= esc($borrow['equipment_name']) ?></td>
                                <td><?= esc($borrow['item_id']) ?></td>
                                <td><?= date('M d, Y', strtotime($borrow['borrow_date'])) ?></td>
                                <td><?= date('M d, Y', strtotime($borrow['due_date'])) ?></td>
                                <td>
                                    <?php if ($borrow['expected_return_date']): ?>
                                        <?= date('M d, Y', strtotime($borrow['expected_return_date'])) ?>
                                    <?php else: ?>
                                        <span class="text-muted">Not returned</span>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <?php
                                    $statusColors = [
                                        'pending' => 'warning',
                                        'approved' => 'primary',
                                        'returned' => 'success',
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
            </div>
        </div>
        <?php elseif ($selected_user): ?>
        <div class="alert alert-info mt-3">
            <i class="bi bi-info-circle me-2"></i>No borrowing history found for the selected criteria.
        </div>
        <?php endif; ?>
    </div>
</div>

<?= $this->include('templates/footer') ?>