<!DOCTYPE html>
<html>

<head>
    <title>Create Chit Group</title>
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

            <div class="content">

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
                        <div class="form-group">
                            <label>Monthly Contribution (₹) *</label>
                            <input type="number" name="monthly_installment" class="form-control" required>
                        </div>
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
    <script>
        document.getElementById('createGroupForm').addEventListener('submit', function(e) {
            e.preventDefault();

            const formData = new FormData(this);

            fetch('store.php', {
                    method: 'POST',
                    body: formData
                })
                .then(res => res.text())
                .then(result => {
                    if (result === 'success') {
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
