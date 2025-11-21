<?= $this->include('templates/header') ?>
<?= $this->include('templates/sidebar') ?>

<div class="main-content">
    <div class="top-navbar">
        <div>
            <button class="btn btn-link sidebar-toggle" id="sidebarToggle">
                <i class="bi bi-list fs-4"></i>
            </button>
            <span class="fs-5 fw-bold"><?= $equipment ? 'Edit' : 'Add' ?> Equipment</span>
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
                <div class="card">
                    <div class="card-header">
                        <i class="bi bi-<?= $equipment ? 'pencil' : 'plus-circle' ?> me-2"></i>
                        <?= $equipment ? 'Edit' : 'Add New' ?> Equipment
                    </div>
                    <div class="card-body">
                        <form method="post" action="<?= $equipment ? base_url('admin/equipment/update/' . $equipment['id']) : base_url('admin/equipment/store') ?>" enctype="multipart/form-data">
                            <?= csrf_field() ?>
                            
                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <label class="form-label">Item ID <span class="text-danger">*</span></label>
                                    <input type="text" name="item_id" class="form-control" 
                                           value="<?= $equipment['item_id'] ?? $next_item_id ?>" 
                                           <?= $equipment ? 'readonly' : 'required' ?>>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">Status <span class="text-danger">*</span></label>
                                    <select name="status" class="form-select" required>
                                        <option value="available" <?= ($equipment['status'] ?? '') == 'available' ? 'selected' : '' ?>>Available</option>
                                        <option value="borrowed" <?= ($equipment['status'] ?? '') == 'borrowed' ? 'selected' : '' ?>>Borrowed</option>
                                        <option value="unusable" <?= ($equipment['status'] ?? '') == 'unusable' ? 'selected' : '' ?>>Unusable</option>
                                        <option value="reserved" <?= ($equipment['status'] ?? '') == 'reserved' ? 'selected' : '' ?>>Reserved</option>
                                    </select>
                                </div>
                            </div>
                            
                            <div class="mb-3">
                                <label class="form-label">Equipment Name <span class="text-danger">*</span></label>
                                <input type="text" name="name" class="form-control" 
                                       value="<?= old('name', $equipment['name'] ?? '') ?>" required>
                            </div>
                            
                            <div class="mb-3">
                                <label class="form-label">Description</label>
                                <textarea name="description" class="form-control" rows="3"><?= old('description', $equipment['description'] ?? '') ?></textarea>
                            </div>
                            
                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <label class="form-label">Category <span class="text-danger">*</span></label>
                                    <select name="category" class="form-select" required>
                                        <option value="">Select Category</option>
                                        <option value="laptop" <?= ($equipment['category'] ?? '') == 'laptop' ? 'selected' : '' ?>>Laptop</option>
                                        <option value="cable" <?= ($equipment['category'] ?? '') == 'cable' ? 'selected' : '' ?>>Cable</option>
                                        <option value="webcam" <?= ($equipment['category'] ?? '') == 'webcam' ? 'selected' : '' ?>>Webcam</option>
                                        <option value="key" <?= ($equipment['category'] ?? '') == 'key' ? 'selected' : '' ?>>Key</option>
                                        <option value="projector" <?= ($equipment['category'] ?? '') == 'projector' ? 'selected' : '' ?>>Projector</option>
                                        <option value="other" <?= ($equipment['category'] ?? '') == 'other' ? 'selected' : '' ?>>Other</option>
                                    </select>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">Brand</label>
                                    <input type="text" name="brand" class="form-control" 
                                           value="<?= old('brand', $equipment['brand'] ?? '') ?>">
                                </div>
                            </div>
                            
                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <label class="form-label">Model</label>
                                    <input type="text" name="model" class="form-control" 
                                           value="<?= old('model', $equipment['model'] ?? '') ?>">
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">Serial Number</label>
                                    <input type="text" name="serial_number" class="form-control" 
                                           value="<?= old('serial_number', $equipment['serial_number'] ?? '') ?>">
                                </div>
                            </div>
                            
                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <label class="form-label">Stock Count <span class="text-danger">*</span></label>
                                    <input type="number" name="stock_count" class="form-control" min="0"
                                           value="<?= old('stock_count', $equipment['stock_count'] ?? 0) ?>" required>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">Minimum Stock <span class="text-danger">*</span></label>
                                    <input type="number" name="minimum_stock" class="form-control" min="1"
                                           value="<?= old('minimum_stock', $equipment['minimum_stock'] ?? 1) ?>" required>
                                </div>
                            </div>
                            
                            <div class="mb-3">
                                <label class="form-label">Equipment Image</label>
                                <?php if ($equipment && $equipment['image_path']): ?>
                                    <div class="mb-2">
                                        <img src="<?= base_url($equipment['image_path']) ?>" alt="Current" class="img-thumbnail" style="max-width: 200px;">
                                    </div>
                                <?php endif; ?>
                                <input type="file" name="image" class="form-control" accept="image/*">
                                <small class="text-muted">Maximum 2MB. JPG, PNG, GIF allowed.</small>
                            </div>
                            
                            <?php if (!$equipment): ?>
                            <div class="mb-3">
                                <label class="form-label">Accessories</label>
                                <div id="accessories-container">
                                    <div class="row mb-2">
                                        <div class="col-md-6">
                                            <input type="text" name="accessories[0][name]" class="form-control" placeholder="Accessory name">
                                        </div>
                                        <div class="col-md-3">
                                            <input type="number" name="accessories[0][quantity]" class="form-control" placeholder="Qty" value="1" min="1">
                                        </div>
                                        <div class="col-md-3">
                                            <div class="form-check mt-2">
                                                <input type="checkbox" name="accessories[0][required]" class="form-check-input">
                                                <label class="form-check-label">Required</label>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <button type="button" class="btn btn-sm btn-secondary" onclick="addAccessory()">
                                    <i class="bi bi-plus"></i> Add Accessory
                                </button>
                            </div>
                            <?php endif; ?>
                            
                            <div class="d-flex gap-2">
                                <button type="submit" class="btn btn-primary">
                                    <i class="bi bi-save me-1"></i>Save Equipment
                                </button>
                                <a href="<?= base_url('admin/equipment') ?>" class="btn btn-secondary">
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

<script>
let accessoryCount = 1;
function addAccessory() {
    const container = document.getElementById('accessories-container');
    const div = document.createElement('div');
    div.className = 'row mb-2';
    div.innerHTML = `
        <div class="col-md-6">
            <input type="text" name="accessories[${accessoryCount}][name]" class="form-control" placeholder="Accessory name">
        </div>
        <div class="col-md-3">
            <input type="number" name="accessories[${accessoryCount}][quantity]" class="form-control" placeholder="Qty" value="1" min="1">
        </div>
        <div class="col-md-3">
            <div class="form-check mt-2">
                <input type="checkbox" name="accessories[${accessoryCount}][required]" class="form-check-input">
                <label class="form-check-label">Required</label>
            </div>
        </div>
    `;
    container.appendChild(div);
    accessoryCount++;
}
</script>

<?= $this->include('templates/footer') ?>