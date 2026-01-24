<?php
include '../../config/database.php';
include '../auth.php';

$settings = $conn->query("SELECT * FROM settings ORDER BY id ASC");
?>

<!DOCTYPE html>
<html>

<head>
    <title>Settings</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <!-- Bootstrap 5 -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <link rel="stylesheet" href="../../assets/css/style.css">
</head>

<body>

    <div class="d-flex" id="wrapper">
        <?php include '../layout/sidebar.php'; ?>

        <div id="page-content-wrapper">
            <?php include '../layout/header.php'; ?>

            <div class="container-fluid p-4">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <div>
                        <h4 class="mb-0 fw-bold">Settings</h4>
                        <small class="text-secondary">Configure system parameters</small>
                    </div>
                </div>

                <div class="card border-0 shadow-sm">
                    <div class="card-header bg-white py-3">
                        <h5 class="mb-0 text-primary fw-bold"><i class="fas fa-sliders-h me-2"></i>System Configuration
                        </h5>
                    </div>
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table table-hover align-middle mb-0">
                                <thead class="table-light">
                                    <tr>
                                        <th class="ps-4">Setting Key</th>
                                        <th>Value</th>
                                        <th>Description</th>
                                        <th>Status</th>
                                        <th class="text-end pe-4">Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php while ($s = $settings->fetch_assoc()): ?>
                                        <tr>
                                            <form method="post" action="update.php">
                                                <td class="ps-4">
                                                    <span class="fw-bold text-dark"><?= $s['setting_key'] ?></span>
                                                </td>

                                                <td>
                                                    <input type="text" name="setting_value"
                                                        class="form-control form-control-sm"
                                                        value="<?= $s['setting_value'] ?>" style="max-width: 200px;">
                                                </td>

                                                <td class="text-secondary small">
                                                    <?= $s['description'] ?>
                                                </td>

                                                <td>
                                                    <div class="form-check form-switch">
                                                        <input class="form-check-input" type="checkbox" role="switch"
                                                            name="is_active" <?= $s['is_active'] ? 'checked' : '' ?>>
                                                    </div>
                                                </td>

                                                <td class="text-end pe-4">
                                                    <input type="hidden" name="id" value="<?= $s['id'] ?>">
                                                    <button class="btn btn-primary btn-sm">
                                                        <i class="fas fa-save me-1"></i> Save
                                                    </button>
                                                </td>
                                            </form>
                                        </tr>
                                    <?php endwhile; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </div>

    <?php include '../layout/scripts.php'; ?>
</body>

</html>