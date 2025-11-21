<?= $this->include('templates/header') ?>
<?= $this->include('templates/sidebar') ?>

<div class="main-content">
    <div class="top-navbar">
        <div>
            <button class="btn btn-link sidebar-toggle" id="sidebarToggle">
                <i class="bi bi-list fs-4"></i>
            </button>
            <span class="fs-5 fw-bold">Equipment Management</span>
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
                <span><i class="bi bi-laptop me-2"></i>Equipment List</span>
                <?php if (session()->get('role_name') === 'itso_personnel'): ?>
                <a href="<?= base_url('admin/equipment/create') ?>" class="btn btn-primary btn-sm">
                    <i class="bi bi-plus-circle me-1"></i>Add Equipment
                </a>
                <?php endif; ?>
            </div>
            <div class="card-body">
                <form method="get" class="mb-3">
                    <div class="row g-2">
                        <div class="col-md-10">
                            <input type="text" name="search" class="form-control" placeholder="Search by item ID, name, category, or brand..." value="<?= esc($search ?? '') ?>">
                        </div>
                        <div class="col-md-2">
                            <button type="submit" class="btn btn-primary w-100">
                                <i class="bi bi-search"></i> Search
                            </button>
                        </div>
                    </div>
                </form>
                
                <?php if (empty($equipment)): ?>
                    <p class="text-muted text-center py-4">No equipment found</p>
                <?php else: ?>
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>Image</th>
                                    <th>Item ID</th>
                                    <th>Name</th>
                                    <th>Category</th>
                                    <th>Brand</th>
                                    <th>Stock</th>
                                    <th>Status</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($equipment as $item): ?>
                                <tr>
                                    <td>
                                        <?php if ($item['thumbnail_path']): ?>
                                            <img src="<?= base_url($item['thumbnail_path']) ?>" alt="<?= esc($item['name']) ?>" style="width: 50px; height: 50px; object-fit: cover; border-radius: 4px;">
                                        <?php else: ?>
                                            <div style="width: 50px; height: 50px; background: #e9ecef; display: flex; align-items: center; justify-content: center; border-radius: 4px;">
                                                <i class="bi bi-image text-muted"></i>
                                            </div>
                                        <?php endif; ?>
                                    </td>
                                    <td><strong><?= esc($item['item_id']) ?></strong></td>
                                    <td><?= esc($item['name']) ?></td>
                                    <td><span class="badge bg-secondary"><?= esc($item['category']) ?></span></td>
                                    <td><?= esc($item['brand'] ?? '-') ?></td>
                                    <td>
                                        <span class="<?= $item['stock_count'] <= $item['minimum_stock'] ? 'text-danger fw-bold' : '' ?>">
                                            <?= $item['stock_count'] ?>
                                        </span>
                                    </td>
                                    <td>
                                        <?php
                                        $statusColors = [
                                            'available' => 'success',
                                            'borrowed' => 'warning',
                                            'unusable' => 'danger',
                                            'reserved' => 'info'
                                        ];
                                        $color = $statusColors[$item['status']] ?? 'secondary';
                                        ?>
                                        <span class="badge bg-<?= $color ?>"><?= ucfirst($item['status']) ?></span>
                                    </td>
                                    <td>
                                        <div class="btn-group btn-group-sm">
                                            <a href="<?= base_url('admin/equipment/view/' . $item['id']) ?>" class="btn btn-info" title="View">
                                                <i class="bi bi-eye"></i>
                                            </a>
                                            <?php if (session()->get('role_name') === 'itso_personnel'): ?>
                                            <a href="<?= base_url('admin/equipment/edit/' . $item['id']) ?>" class="btn btn-warning" title="Edit">
                                                <i class="bi bi-pencil"></i>
                                            </a>
                                            <form method="post" action="<?= base_url('admin/equipment/delete/' . $item['id']) ?>" class="d-inline" onsubmit="return confirmDelete()">
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