<?= $this->include('templates/header') ?>
<?= $this->include('templates/sidebar') ?>

<div class="main-content">
    <div class="top-navbar">
        <div>
            <button class="btn btn-link sidebar-toggle" id="sidebarToggle">
                <i class="bi bi-list fs-4"></i>
            </button>
            <span class="fs-5 fw-bold">Process Return</span>
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
                <div class="card mb-3">
                    <div class="card-header bg-primary text-white">
                        <i class="bi bi-info-circle me-2"></i>Borrow Information
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6">
                                <p><strong>Borrow Code:</strong> <?= esc($borrow['borrow_code']) ?></p>
                                <p><strong>Borrower:</strong> <?= esc($borrow['first_name'] . ' ' . $borrow['last_name']) ?></p>
                                <p><strong>Equipment:</strong> <?= esc($borrow['equipment_name']) ?></p>
                            </div>
                            <div class="col-md-6">
                                <p><strong>Item ID:</strong> <?= esc($borrow['item_id']) ?></p>
                                <p><strong>Borrow Date:</strong> <?= date('M d, Y', strtotime($borrow['borrow_date'])) ?></p>
                                <p><strong>Due Date:</strong> <?= date('M d, Y', strtotime($borrow['due_date'])) ?></p>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="card">
                    <div class="card-header">
                        <i class="bi bi-arrow-left-circle me-2"></i>Return Details
                    </div>
                    <div class="card-body">
                        <form method="post" action="<?= base_url('admin/return/store') ?>">
                            <?= csrf_field() ?>
                            <input type="hidden" name="borrow_id" value="<?= $borrow['id'] ?>">
                            
                            <div class="mb-3">
                                <label class="form-label">Return Date <span class="text-danger">*</span></label>
                                <input type="datetime-local" name="return_date" class="form-control" 
                                       value="<?= date('Y-m-d\TH:i') ?>" required>
                            </div>
                            
                            <div class="mb-3">
                                <label class="form-label">Condition on Return <span class="text-danger">*</span></label>
                                <select name="condition_on_return" class="form-select" required>
                                    <option value="good">Good - Equipment is in excellent condition</option>
                                    <option value="damaged">Damaged - Equipment has some damage</option>
                                    <option value="lost">Lost - Equipment was not returned</option>
                                </select>
                            </div>
                            
                            <div class="mb-3">
                                <label class="form-label">Fines (if any)</label>
                                <div class="input-group">
                                    <span class="input-group-text">PHP</span>
                                    <input type="number" name="fines" class="form-control" 
                                           min="0" step="0.01" value="0" placeholder="0.00">
                                </div>
                                <small class="text-muted">Enter any fines for late return or damages</small>
                            </div>
                            
                            <div class="mb-3">
                                <label class="form-label">Notes</label>
                                <textarea name="notes" class="form-control" rows="3" 
                                          placeholder="Any additional notes about the return..."></textarea>
                            </div>
                            
                            <div class="alert alert-warning">
                                <i class="bi bi-exclamation-triangle me-2"></i>
                                <strong>Important:</strong> Please verify the equipment condition before processing the return. 
                                Stock will be updated automatically based on the condition.
                            </div>
                            
                            <div class="d-flex gap-2">
                                <button type="submit" class="btn btn-success">
                                    <i class="bi bi-check-circle me-1"></i>Process Return
                                </button>
                                <a href="<?= base_url('admin/return') ?>" class="btn btn-secondary">
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