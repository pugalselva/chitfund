<!DOCTYPE html>
<html>

<head>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Create Chit Group</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap-grid.min.css" rel="stylesheet">
    <link rel="stylesheet" href="../../assets/css/style.css">

</head>

<body>

    <div class="wrapper">
        <?php include '../layout/sidebar.php'; ?>
        <div class="main">

            <div class="topbar">
                <div>
                    <div class="page-title">Create Chit Group</div>
                    <div class="page-subtitle">Set up a new chit fund group</div>
                </div>
            </div>

            <div class="content container-fluid">
                <div class="col-12 col-md-10 col-lg-8">
                    <div class="row justify-content-center">
                        <form id="createGroupForm">
                            <div class="form-box">
                                <h4>Group Details</h4><br>
                                <div class="form-group">
                                    <label>Chit Group Name *</label>
                                    <input type="text" name="group_name" class="form-control" required>
                                </div>
                                <div class="form-row">
                                    <div class="form-group">
                                        <label>Total Members *</label>
                                        <input type="number" name="total_members" class="form-control" required>
                                    </div>
                                    <div class="form-group">
                                        <label>Duration (Months) *</label>
                                        <input type="number" name="duration" class="form-control" required>
                                    </div>
                                </div>
                                <!-- <div class="form-group">
                                    <label>Monthly Contribution (₹) *</label>
                                    <input type="number" name="monthly_installment" class="form-control" required>
                                </div> -->
                                <div class="form-row">
                                    <div class="form-group">
                                        <label>Auction Type *</label>
                                        <select name="auction_type" class="form-control" required>
                                            <option value="">Select</option>
                                            <option value="Reverse">Reverse (Lowest Bid Wins)</option>
                                            <option value="Open">Open Auction</option>
                                        </select>
                                    </div>
                                    <!-- <div class="form-group">
                                <label>Foreman Commission (%) *</label>
                                <input type="number" name="commission" class="form-control" required>
                            </div> -->
                                </div>
                                <div class="form-group">
                                    <label>Start Date *</label>
                                    <input type="date" name="start_date" class="form-control" required>
                                </div>
                                <div class="form-group">
                                    <label>Status *</label>
                                    <select name="status" class="form-control" required>
                                        <option value="upcoming">Upcoming</option>
                                        <option value="active">Active</option>
                                        <option value="completed">Completed</option>
                                    </select>
                                </div>
                                <div class="form-group toggle">
                                    <input type="checkbox" name="is_active" value="1" checked>
                                    <label>Active Status</label>
                                </div>
                                <br>
                                <button class="btn-primary" type="submit">Create Group</button>

                                <a href="index.php">
                                    <button type="button" class="btn-secondary">Cancel</button>
                                </a>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div id="toast" class="toast">
        <i class="fas fa-check-circle toast-icon"></i>
        <span id="toastMsg">Message</span>
        <button class="toast-close" onclick="hideToast()">
            <i class="fas fa-times"></i>
        </button>
    </div>

<!-- script -->
    <script>
        document.getElementById('createGroupForm').addEventListener('submit', function(e) {
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
