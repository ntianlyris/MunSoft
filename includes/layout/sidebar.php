<?php include_once "../includes/view/view.php"; ?>

<!-- Main Sidebar Container -->
<aside class="main-sidebar sidebar-dark-primary elevation-4">
    <!-- Brand Logo -->
    <a href="./" class="brand-link">
      <img src="../includes/images/polanco_logo.png" alt="polanco logo" class="brand-image img-circle elevation-3" style="opacity: .8">
      <span class="brand-text font-weight-light"><strong><b>INTELLIGOV</b> NEXUS</strong></span>
    </a>

    <!-- Sidebar -->
    <div class="sidebar">
      <!-- Sidebar user panel (optional) -->
      <div class="user-panel mt-3 pb-3 mb-3 d-flex">
        <div class="image">
          <img src="../includes/images/avatar.jpg" class="img-circle elevation-2" alt="User Image">
        </div>
        <div class="info">
          <a href="#" class="d-block"><?php echo $_SESSION['emailusername']; ?></a>
        </div>
      </div>

      <!-- SidebarSearch Form 
      <div class="form-inline">
        <div class="input-group" data-widget="sidebar-search">
          <input class="form-control form-control-sidebar" type="search" placeholder="Search" aria-label="Search">
          <div class="input-group-append">
            <button class="btn btn-sidebar">
              <i class="fas fa-search fa-fw"></i>
            </button>
          </div>
        </div>
      </div>-->

      <!-- Sidebar Menu -->
      <nav class="mt-2">
        <ul class="nav nav-pills nav-sidebar flex-column" id="sidebar-nav" data-widget="treeview" role="menu" data-accordion="false">
          <!-- Add icons to the links using the .nav-icon class
               with font-awesome or any other icon font library -->
          <li class="nav-item menu-open">
            <a href="./" class="nav-link" id="home_li">
              <i class="nav-icon fas fa-tachometer-alt"></i>
              <p>
                Dashboard
              </p>
            </a>
          </li>
          <?php echo ViewSideBarLink('users'); ?>
          <?php echo ViewSideBarLink('employees'); ?>
          <?php echo ViewSideBarLink('profile'); ?>
          <?php echo ViewSideBarLink('employment'); ?>
          <?php echo ViewSideBarLink('payroll'); ?>
          <?php echo ViewSideBarLink('employee_payslip'); ?>
          <?php echo ViewSideBarLink('employee_gaa_status'); ?>
          <?php echo ViewSideBarLink('leave_application'); ?>
          <?php echo ViewSideBarLink('admin_settings'); ?>
          <?php echo ViewSideBarLink('employee_earnings'); ?>
          <?php echo ViewSideBarLink('employee_deductions'); ?>
          <?php echo ViewSideBarLink('employee_govshares'); ?>
          <?php echo ViewSideBarLink('payrolls'); ?>
          <?php echo ViewSideBarLink('payroll_records'); ?>
          <?php echo ViewSideBarLink('gaa_netpay_status'); ?>
          <?php echo ViewSideBarLink('remittance'); ?>
          <?php echo ViewSideBarLink('leave_applications'); ?>
          <?php echo ViewSideBarLink('manage_leave_credits'); ?>
            <?php if ($role !== 'Employee') { ?>
              <li class="nav-item">
              <a href="#" class="nav-link">
                <i class="nav-icon fas fa-chart-line"></i>
                <p>
                Reports
                <i class="right fas fa-angle-left"></i>
                </p>
              </a>
              <ul class="nav nav-treeview">
                <?php echo ViewSideBarLink('journal_entry'); ?>
                <?php echo ViewSideBarLink('payslip'); ?>
                <?php echo ViewSideBarLink('report_slp'); ?>
                <?php echo ViewSideBarLink('report_abstract'); ?>
              </ul>
              </li>
            <?php } ?>
          <li class="nav-item">
            <a href="#" class="nav-link">
              <i class="nav-icon fas fa-cogs"></i>
              <p>
                Configurations
                <i class="right fas fa-angle-left"></i>
              </p>
            </a>
            <ul class="nav nav-treeview">
              <?php echo ViewSideBarLink('departments'); ?>
              <?php echo ViewSideBarLink('positions'); ?>
              <?php echo ViewSideBarLink('config_earnings'); ?>
              <?php echo ViewSideBarLink('config_deductions'); ?>
              <?php echo ViewSideBarLink('govshares'); ?>
              <?php echo ViewSideBarLink('payroll_settings'); ?>
              <?php echo ViewSideBarLink('config_leave_types'); ?>
              <?php echo ViewSideBarLink('signatories'); ?>
              <?php echo ViewSideBarLink('change_password'); ?>
              <?php echo ViewSideBarLink('database_backup'); ?>
            </ul>
          </li>
        </ul>
      </nav>
      <!-- /.sidebar-menu -->
    </div>
    <!-- /.sidebar -->
  </aside>