<?= $this->include('templates/header') ?>
<?= $this->include('templates/sidebar') ?>

<div class="main-content">
    <div class="top-navbar">
        <div>
            <button class="btn btn-link sidebar-toggle" id="sidebarToggle">
                <i class="bi bi-list fs-4"></i>
            </button>
            <span class="fs-5 fw-bold">Active Equipment Report</span>
        </div>
    </div>
    
    <div class="content-area">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <span><i class="bi bi-file-earmark-text me-2"></i>Active Equipment</span>
                <div class="btn-group">
                    <a href="<?= base_url('admin/reports/export/active-equipment?format=csv') ?>" class="btn btn-success btn-sm">
                        <i class="bi bi-file-earmark-spreadsheet me-1"></i>Export CSV
                    </a>
                    <a href="<?= base_url('admin/reports/export/active-equipment?format=pdf') ?>" class="btn btn-danger btn-sm">
                        <i class="bi bi-file-earmark-pdf me-1"></i>Export PDF
                    </a>
                </div>
            </div>
            <div class="card-body">
                <div class="mb-3">
                    <p class="text-muted">Total Active Equipment: <strong><?= count($equipment) ?></strong></p>
                    <p class="text-muted">Report Generated: <strong><?= date('F d, Y H:i:s') ?></strong></p>
                </div>
                
                <?php if (empty($equipment)): ?>
                    <p class="text-muted text-center py-4">No active equipment found</p>
                <?php else: ?>
                    <div class="table-responsive">
                        <table class="table table-bordered table-hover">
                            <thead class="table-light">
                                <tr>
                                    <th>Item ID</th>
                                    <th>Name</th>
                                    <th>Category</th>
                                    <th>Brand</th>
                                    <th>Model</th>
                                    <th>Stock Count</th>
                                    <th>Min Stock</th>
                                    <th>Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($equipment as $item): ?>
                                <tr>
                                    <td><strong><?= esc($item['item_id']) ?></strong></td>
                                    <td><?= esc($item['name']) ?></td>
                                    <td><?= esc($item['category']) ?></td>
                                    <td><?= esc($item['brand'] ?? 'N/A') ?></td>
                                    <td><?= esc($item['model'] ?? 'N/A') ?></td>
                                    <td class="text-center">
                                        <span class="<?= $item['stock_count'] <= $item['minimum_stock'] ? 'text-danger fw-bold' : '' ?>">
                                            <?= $item['stock_count'] ?>
                                        </span>
                                    </td>
                                    <td class="text-center"><?= $item['minimum_stock'] ?></td>
                                    <td>
                                        <?php
                                        $statusColors = [
                                            'available' => 'success',
                                            'borrowed' => 'warning'
                                        ];
                                        $color = $statusColors[$item['status']] ?? 'secondary';
                                        ?>
                                        <span class="badge bg-<?= $color ?>"><?= ucfirst($item['status']) ?></span>
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