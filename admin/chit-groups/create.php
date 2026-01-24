<?php
include '../auth.php';
?>
<!DOCTYPE html>
<html>

<head>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Create Chit Group</title>
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" rel="stylesheet">
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
                        <h4 class="mb-0 fw-bold">Create Chit Group</h4>
                        <small class="text-secondary">Set up a new chit fund group scheme</small>
                    </div>
                </div>

                <div class="row justify-content-center">
                    <div class="col-12 col-lg-8">
                        <div class="card border-0 shadow-sm">
                            <div class="card-header bg-white border-bottom-0 pt-4 pb-0">
                                <h5 class="card-title fw-bold text-primary"><i class="fas fa-layer-group me-2"></i>Group
                                    Details</h5>
                            </div>
                            <div class="card-body p-4">
                                <form id="createGroupForm">

                                    <div class="form-floating mb-3">
                                        <input type="text" name="group_name" id="group_name" class="form-control"
                                            placeholder="Group Name" required>
                                        <label for="group_name">Chit Group Name *</label>
                                    </div>

                                    <div class="row g-3 mb-3">
                                        <div class="col-md-6">
                                            <div class="form-floating">
                                                <input type="number" name="total_members" id="total_members"
                                                    class="form-control" placeholder="Total Members" required>
                                                <label for="total_members">Total Members *</label>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-floating">
                                                <input type="number" name="duration" id="duration" class="form-control"
                                                    placeholder="Duration" required>
                                                <label for="duration">Duration (Months) *</label>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="row g-3 mb-3">
                                        <div class="col-md-6">
                                            <div class="form-floating">
                                                <select name="auction_type" id="auction_type" class="form-select"
                                                    required>
                                                    <option value="" selected disabled>Select Type</option>
                                                    <option value="Reverse">Reverse (Lowest Bid Wins)</option>
                                                    <option value="Open">Open Auction</option>
                                                </select>
                                                <label for="auction_type">Auction Type *</label>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-floating">
                                                <input type="date" name="start_date" id="start_date"
                                                    class="form-control" required>
                                                <label for="start_date">Start Date *</label>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="form-floating mb-4">
                                        <select name="status" id="status" class="form-select" required>
                                            <option value="upcoming">Upcoming</option>
                                            <option value="active">Active</option>
                                            <option value="completed">Completed</option>
                                        </select>
                                        <label for="status">Initial Status *</label>
                                    </div>

                                    <div class="form-check form-switch mb-4">
                                        <input class="form-check-input" type="checkbox" role="switch" name="is_active"
                                            id="is_active" value="1" checked>
                                        <label class="form-check-label" for="is_active">Enable Group Immediately</label>
                                    </div>

                                    <div class="d-flex gap-2 justify-content-end">
                                        <a href="index.php" class="btn btn-light border">Cancel</a>
                                        <button class="btn btn-primary px-4" type="submit">
                                            <i class="fas fa-check me-2"></i> Create Group
                                        </button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </div>

    <?php include '../layout/scripts.php'; ?>
    <script>
        document.getElementById('createGroupForm').addEventListener('submit', function (e) {
            e.preventDefault();

            fetch('store.php', {
                method: 'POST',
                body: new FormData(this)
            })
                .then(res => res.text())
                .then(result => {
                    if (result.trim() === 'success') {
                        alert('Chit Group Created Successfully');
                        window.location.href = 'index.php';
                    } else {
                        alert(result);
                    }
                });
        });
    </script>
</body>

</html>