<h1>Welcome to <?php echo $_settings->info('name') ?> - Admin Panel</h1>
<hr class="border-info">
<style>
    .info-box-link {
        text-decoration: none;
        color: inherit;
        display: block;
    }
    .info-box-link:hover {
        text-decoration: none;
        color: inherit;
    }
    .info-box-link:hover .info-box {
        transform: translateY(-3px);
        box-shadow: 0 4px 12px rgba(0,0,0,0.15) !important;
        transition: all 0.3s ease;
    }
    .info-box {
        cursor: pointer;
        transition: all 0.3s ease;
    }
</style>
<div class="row">
    <div class="col-12 col-sm-12 col-md-6 col-lg-3">
        <a href="?page=tests" class="info-box-link">
            <div class="info-box bg-gradient-light shadow">
                <span class="info-box-icon bg-gradient-dark elevation-1"><i class="fas fa-th-list"></i></span>
                <div class="info-box-content">
                    <span class="info-box-text">Tests</span>
                    <span class="info-box-number text-right">
                        <?php 
                            echo $conn->query("SELECT * FROM `test_list` where delete_flag = 0 and status = 1 ")->num_rows;
                        ?>
                    </span>
                </div>
            </div>
        </a>
    </div>
    <div class="col-12 col-sm-12 col-md-6 col-lg-3">
        <a href="?page=appointments" class="info-box-link">
            <div class="info-box bg-gradient-light shadow">
                <span class="info-box-icon bg-gradient-navy elevation-1"><i class="fas fa-calendar"></i></span>
                <div class="info-box-content">
                    <span class="info-box-text">Booked Appointment</span>
                    <span class="info-box-number text-right">
                        <?php 
                            echo $conn->query("SELECT * FROM `appointment_list` where client_id = '{$_settings->userdata('id')}' ")->num_rows;
                        ?>
                    </span>
                </div>
            </div>
        </a>
    </div>
    <div class="col-12 col-sm-12 col-md-6 col-lg-3">
        <a href="?page=appointments" class="info-box-link">
            <div class="info-box bg-gradient-light shadow">
                <span class="info-box-icon bg-gradient-secondary elevation-1"><i class="fas fa-spinner"></i></span>
                <div class="info-box-content">
                    <span class="info-box-text">Pending Appointment</span>
                    <span class="info-box-number text-right">
                        <?php 
                            echo $conn->query("SELECT * FROM `appointment_list` where client_id = '{$_settings->userdata('id')}' and status = 0 ")->num_rows;
                        ?>
                    </span>
                </div>
            </div>
        </a>
    </div>
    <div class="col-12 col-sm-12 col-md-6 col-lg-3">
        <a href="?page=appointments" class="info-box-link">
            <div class="info-box bg-gradient-light shadow">
                <span class="info-box-icon bg-gradient-primary elevation-1"><i class="fas fa-thumbs-up"></i></span>
                <div class="info-box-content">
                    <span class="info-box-text">Approved Appointment</span>
                    <span class="info-box-number text-right">
                        <?php 
                            echo $conn->query("SELECT * FROM `appointment_list` where client_id = '{$_settings->userdata('id')}' and status  in (1,2,3) ")->num_rows;
                        ?>
                    </span>
                </div>
            </div>
        </a>
    </div>
    <div class="col-12 col-sm-12 col-md-6 col-lg-3">
        <a href="?page=reports" class="info-box-link">
            <div class="info-box bg-gradient-light shadow">
                <span class="info-box-icon bg-gradient-maroon elevation-1"><i class="fas fa-vial"></i></span>
                <div class="info-box-content">
                    <span class="info-box-text">Finished Test</span>
                    <span class="info-box-number text-right">
                        <?php 
                            echo $conn->query("SELECT * FROM `appointment_list` where client_id = '{$_settings->userdata('id')}' and status = 6 ")->num_rows;
                        ?>
                    </span>
                </div>
            </div>
        </a>
    </div>
    <div class="col-12 col-sm-12 col-md-6 col-lg-3">
        <a href="?page=clients" class="info-box-link">
            <div class="info-box bg-gradient-light shadow">
                <span class="info-box-icon bg-gradient-teal elevation-1"><i class="fas fa-users"></i></span>
                <div class="info-box-content">
                    <span class="info-box-text">Registered Users</span>
                    <span class="info-box-number text-right">
                        <?php 
                            echo $conn->query("SELECT * FROM `client_list` ")->num_rows;
                        ?>
                    </span>
                </div>
            </div>
        </a>
    </div>
</div>
<hr>
