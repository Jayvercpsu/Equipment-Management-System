<?= $this->include('templates/header') ?>
<?= $this->include('templates/sidebar') ?>

<div class="main-content">
    <div class="top-navbar">
        <div>
            <button class="btn btn-link sidebar-toggle" id="sidebarToggle">
                <i class="bi bi-list fs-4"></i>
            </button>
            <span class="fs-5 fw-bold">Borrow Equipment</span>
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
                        <i class="bi bi-arrow-right-circle me-2"></i>New Borrow Request
                    </div>
                    <div class="card-body">
                        <form method="post" action="<?= base_url('admin/borrow/store') ?>">
                            <?= csrf_field() ?>
                            
                            <div class="mb-3">
                                <label class="form-label">Borrow Code</label>
                                <input type="text" class="form-control" value="<?= $next_borrow_code ?>" readonly>
                            </div>
                            
                            <div class="mb-3">
                                <label class="form-label">Select Equipment <span class="text-danger">*</span></label>
                                <select name="equipment_id" id="equipment_id" class="form-select" required>
                                    <option value="">Choose equipment...</option>
                                    <?php foreach ($equipment as $item): ?>
                                        <option value="<?= $item['id'] ?>" 
                                                data-name="<?= esc($item['name']) ?>"
                                                data-stock="<?= $item['stock_count'] ?>">
                                            <?= esc($item['item_id']) ?> - <?= esc($item['name']) ?> (Stock: <?= $item['stock_count'] ?>)
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            
                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <label class="form-label">Borrow Date <span class="text-danger">*</span></label>
                                    <input type="datetime-local" name="borrow_date" class="form-control" 
                                           value="<?= date('Y-m-d\TH:i') ?>" required>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">Due Date <span class="text-danger">*</span></label>
                                    <input type="datetime-local" name="due_date" class="form-control" 
                                           value="<?= date('Y-m-d\TH:i', strtotime('+7 days')) ?>" required>
                                </div>
                            </div>
                            
                            <div class="mb-3">
                                <label class="form-label">Notes</label>
                                <textarea name="notes" class="form-control" rows="3" placeholder="Additional notes or special requests..."></textarea>
                            </div>
                            
                            <div class="alert alert-info">
                                <i class="bi bi-info-circle me-2"></i>
                                <strong>Note:</strong> Required accessories will be automatically included with your borrow request. 
                                Your request will be pending until approved by ITSO personnel.
                            </div>
                            
                            <div class="d-flex gap-2">
                                <button type="submit" class="btn btn-primary">
                                    <i class="bi bi-send me-1"></i>Submit Request
                                </button>
                                <a href="<?= base_url('admin/borrow') ?>" class="btn btn-secondary">
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