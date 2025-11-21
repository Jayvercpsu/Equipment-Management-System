<?= $this->include('templates/header') ?>
<?= $this->include('templates/sidebar') ?>

<div class="main-content">
    <div class="top-navbar">
        <div>
            <button class="btn btn-link sidebar-toggle" id="sidebarToggle">
                <i class="bi bi-list fs-4"></i>
            </button>
            <span class="fs-5 fw-bold">Reservation Management</span>
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
                <span><i class="bi bi-calendar-check me-2"></i>Reservations</span>
                <?php if (session()->get('role_name') !== 'student'): ?>
                <a href="<?= base_url('admin/reservation/create') ?>" class="btn btn-primary btn-sm">
                    <i class="bi bi-plus-circle me-1"></i>New Reservation
                </a>
                <?php endif; ?>
            </div>
            <div class="card-body">
                <?php if (empty($reservations)): ?>
                    <p class="text-muted text-center py-4">No reservations found</p>
                <?php else: ?>
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>Reservation Code</th>
                                    <th>Equipment</th>
                                    <?php if (session()->get('role_name') === 'itso_personnel'): ?>
                                    <th>Reserved By</th>
                                    <?php endif; ?>
                                    <th>Start Date</th>
                                    <th>End Date</th>
                                    <th>Status</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($reservations as $reservation): ?>
                                <tr>
                                    <td><strong><?= esc($reservation['reservation_code']) ?></strong></td>
                                    <td><?= esc($reservation['equipment_name']) ?></td>
                                    <?php if (session()->get('role_name') === 'itso_personnel'): ?>
                                    <td><?= esc($reservation['first_name'] . ' ' . $reservation['last_name']) ?></td>
                                    <?php endif; ?>
                                    <td><?= date('M d, Y H:i', strtotime($reservation['start_datetime'])) ?></td>
                                    <td><?= date('M d, Y H:i', strtotime($reservation['end_datetime'])) ?></td>
                                    <td>
                                        <?php
                                        $statusColors = [
                                            'active' => 'success',
                                            'cancelled' => 'danger',
                                            'completed' => 'info'
                                        ];
                                        $color = $statusColors[$reservation['status']] ?? 'secondary';
                                        ?>
                                        <span class="badge bg-<?= $color ?>"><?= ucfirst($reservation['status']) ?></span>
                                    </td>
                                    <td>
                                        <div class="btn-group btn-group-sm">
                                            <a href="<?= base_url('admin/reservation/view/' . $reservation['id']) ?>" class="btn btn-info" title="View">
                                                <i class="bi bi-eye"></i>
                                            </a>
                                            <?php if ($reservation['status'] === 'active'): ?>
                                            <a href="<?= base_url('admin/reservation/reschedule/' . $reservation['id']) ?>" class="btn btn-warning" title="Reschedule">
                                                <i class="bi bi-calendar-event"></i>
                                            </a>
                                            <form method="post" action="<?= base_url('admin/reservation/cancel/' . $reservation['id']) ?>" class="d-inline" onsubmit="return confirmDelete('Cancel this reservation?')">
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