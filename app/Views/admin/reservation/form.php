<?= $this->include('templates/header') ?>
<?= $this->include('templates/sidebar') ?>

<div class="main-content">
    <div class="top-navbar">
        <div>
            <button class="btn btn-link sidebar-toggle" id="sidebarToggle">
                <i class="bi bi-list fs-4"></i>
            </button>
            <span class="fs-5 fw-bold">Create Reservation</span>
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
        
        <?php if (session()->getFlashdata('error')): ?>
            <div class="alert alert-danger alert-dismissible fade show">
                <?= session()->getFlashdata('error') ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        <?php endif; ?>
        
        <div class="row">
            <div class="col-md-8 mx-auto">
                <div class="card">
                    <div class="card-header">
                        <i class="bi bi-calendar-plus me-2"></i>New Reservation
                    </div>
                    <div class="card-body">
                        <form method="post" action="<?= base_url('admin/reservation/store') ?>">
                            <?= csrf_field() ?>
                            
                            <div class="mb-3">
                                <label class="form-label">Reservation Code</label>
                                <input type="text" class="form-control" value="<?= $next_reservation_code ?>" readonly>
                            </div>
                            
                            <div class="mb-3">
                                <label class="form-label">Select Equipment <span class="text-danger">*</span></label>
                                <select name="equipment_id" class="form-select" required>
                                    <option value="">Choose equipment...</option>
                                    <?php foreach ($equipment as $item): ?>
                                        <option value="<?= $item['id'] ?>">
                                            <?= esc($item['item_id']) ?> - <?= esc($item['name']) ?> (Available: <?= $item['stock_count'] ?>)
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            
                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <label class="form-label">Start Date & Time <span class="text-danger">*</span></label>
                                    <input type="datetime-local" name="start_datetime" class="form-control" 
                                           min="<?= date('Y-m-d\TH:i', strtotime('+1 day')) ?>" required>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">End Date & Time <span class="text-danger">*</span></label>
                                    <input type="datetime-local" name="end_datetime" class="form-control" 
                                           min="<?= date('Y-m-d\TH:i', strtotime('+1 day')) ?>" required>
                                </div>
                            </div>
                            
                            <div class="alert alert-info">
                                <i class="bi bi-info-circle me-2"></i>
                                <strong>Reservation Policy:</strong>
                                <ul class="mb-0 mt-2">
                                    <li>Reservations must be made at least 1 day in advance</li>
                                    <li>End date must be after start date</li>
                                    <li>Equipment will be reserved for your specified time period</li>
                                    <li>You can reschedule or cancel active reservations</li>
                                </ul>
                            </div>
                            
                            <div class="d-flex gap-2">
                                <button type="submit" class="btn btn-primary">
                                    <i class="bi bi-calendar-check me-1"></i>Create Reservation
                                </button>
                                <a href="<?= base_url('admin/reservation') ?>" class="btn btn-secondary">
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