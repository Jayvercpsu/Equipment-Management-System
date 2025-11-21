<?= $this->include('templates/header') ?>
<?= $this->include('templates/sidebar') ?>

<div class="content-wrapper">
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1 class="m-0">Return Processed</h1>
                </div>
            </div>
        </div>
    </div>

    <section class="content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-md-12">
                    <div class="card">
                        <div class="card-body text-center">
                            <?php if (session()->getFlashdata('success')): ?>
                                <div class="alert alert-success">
                                    <i class="fas fa-check-circle fa-3x mb-3"></i>
                                    <h4><?= session()->getFlashdata('success') ?></h4>
                                    <p>The equipment return has been processed successfully.</p>
                                </div>
                            <?php elseif (session()->getFlashdata('error')): ?>
                                <div class="alert alert-danger">
                                    <i class="fas fa-exclamation-circle fa-3x mb-3"></i>
                                    <h4><?= session()->getFlashdata('error') ?></h4>
                                </div>
                            <?php endif; ?>
                            
                            <a href="<?= base_url('admin/return') ?>" class="btn btn-primary">
                                <i class="fas fa-arrow-left"></i> Back to Returns
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
</div>

<?= $this->include('templates/footer') ?>