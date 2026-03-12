<?php
    include_once '../includes/layout/head.php';
    include_once '../includes/layout/navbar.php';
    include_once '../includes/layout/sidebar.php';
    include_once '../includes/view/view.php';
    include_once '../includes/view/showModals.php';
    include_once '../includes/class/Employee.php';

    $employee_id = "";
    if (isset($_GET['emp_id']) && !empty($_GET['emp_id'])) {
      $employee_id = $_GET['emp_id'];
    }
?>

<!-- Content Wrapper. Contains page content -->
<div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <section class="content-header">
      <div class="container-fluid">
        <div class="row mb-2">
          <div class="col-sm-6">
            <h1>Employment</h1>
          </div>
          <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
              <li class="breadcrumb-item"><a href="./">Dashboard</a></li>
              <li class="breadcrumb-item"><a href="profile.php?emp_id=<?php echo $employee_id; ?>">Profile</a></li>
              <li class="breadcrumb-item active">Employment History / Service Records</li>
            </ol>
          </div>
        </div>
      </div><!-- /.container-fluid -->
    </section>
    
    <!-- Main content -->
    <section class="content">
      <div class="container-fluid">
        <div class="row">
          <div class="col-12">
            <div class="card card-primary card-outline">
              <div class="card-header" style="width: 100%;">
                <div class="row">
                  <div class="col-6">
                    <h5 class="card-title m-0">Employment Record</h5>
                  </div>
                  <div class="col-6 text-right">
                    <a href="../prints/print_service_record.php?employee_id=<?php echo $employee_id; ?>" target="_blank" class="btn btn-sm btn-primary">
                      <i class="fas fa-file-pdf"></i> Print Service Record
                    </a>
                  </div>
              </div>
              <div class="card-body p-0">
                <div id="employmentsList" class="list-group list-group-flush">
                  <div class="p-4 text-center text-muted">
                    <i class="fas fa-spinner fa-spin fa-2x mb-2"></i>
                    <p>Loading employment records...</p>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
        <!-- /.row -->
      </div>
      <!-- /.container-fluid -->

      <!-- Modals -->
      <?php ShowEmploymentModal(); ?>

      <!-- Employment Detail Modal (Read-Only) -->
      <div class="modal fade" id="employmentDetailModal" tabindex="-1" role="dialog" aria-labelledby="employmentDetailModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
          <div class="modal-content">
            <div class="modal-header bg-primary text-white">
              <h5 class="modal-title" id="employmentDetailModalLabel"><i class="fas fa-briefcase"></i> Employment Details</h5>
              <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">&times;</span>
              </button>
            </div>
            <div class="modal-body p-0">
              <table class="table table-striped mb-0">
                <tbody id="employmentDetailContent">
                  <!-- Content populated by JS -->
                </tbody>
              </table>
            </div>
            <div class="modal-footer">
              <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
            </div>
          </div>
        </div>
      </div>
    </section>
    <!-- /.content -->
  </div>
  <!-- /.content-wrapper -->

<!-- jQuery -->
<script src="../plugins/jquery/jquery.min.js"></script>
<!-- Bootstrap 4 -->
<script src="../plugins/bootstrap/js/bootstrap.bundle.min.js"></script>
<!-- Select2 -->
<script src="../plugins/select2/js/select2.full.min.js"></script>
<!-- DataTables  & Plugins -->
<script src="../plugins/datatables/jquery.dataTables.min.js"></script>
<script src="../plugins/datatables-bs4/js/dataTables.bootstrap4.min.js"></script>
<script src="../plugins/datatables-responsive/js/dataTables.responsive.min.js"></script>
<script src="../plugins/datatables-responsive/js/responsive.bootstrap4.min.js"></script>
<script src="../plugins/datatables-buttons/js/dataTables.buttons.min.js"></script>
<script src="../plugins/datatables-buttons/js/buttons.bootstrap4.min.js"></script>
<script src="../plugins/jszip/jszip.min.js"></script>
<script src="../plugins/pdfmake/pdfmake.min.js"></script>
<script src="../plugins/pdfmake/vfs_fonts.js"></script>
<script src="../plugins/datatables-buttons/js/buttons.html5.min.js"></script>
<script src="../plugins/datatables-buttons/js/buttons.print.min.js"></script>
<script src="../plugins/datatables-buttons/js/buttons.colVis.min.js"></script>

<!-- AdminLTE App -->
<script src="../dist/js/adminlte.js"></script>
<!-- Moment.js -->
<script src="../plugins/moment/moment.min.js"></script>

<script>
  var _cachedEmployments = [];

  $(document).ready(function() {
    loadEmploymentData();
  });

  function loadEmploymentData() {
    $.ajax({
      url: 'get_dashboard_data.php',
      type: 'GET',
      dataType: 'json',
      data: { action: 'all' },
      success: function (response) {
        if (response.success && response.employments) {
          _cachedEmployments = response.employments;
          renderEmploymentsList(_cachedEmployments);
          checkHighlight();
        } else {
          $('#employmentsList').html('<div class="p-4 text-center text-muted">No employment records found.</div>');
        }
      },
      error: function (err) {
        console.error('AJAX error:', err);
        $('#employmentsList').html('<div class="p-4 text-center text-danger"><i class="fas fa-exclamation-circle"></i> Failed to load employment records.</div>');
      }
    });
  }

  function renderEmploymentsList(employments) {
    var html = '';
    employments.forEach(function (e) {
      var start = formatEmploymentDate(e.employment_start, '0'); // Start usually has a date
      var end = formatEmploymentDate(e.employment_end, e.employment_status);
      var pos = e.designation || e.position_title || 'Position';
      var dept = e.dept_name || e.dept_assigned || 'Department';
      var badge = e.employment_status == '1' ? 'success' : 'secondary';
      var label = e.employment_status == '1' ? 'Active' : 'Ended';
      var typeBadge = e.employment_type == 'Regular' ? 'info' : 'warning';

      html += 
        '<div class="list-group-item p-3 border-bottom employment-card" id="employment_row_' + e.employment_id + '">' +
          '<div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-2">' +
            '<div class="mb-2 mb-md-0">' +
              '<div class="d-flex align-items-center mb-1">' +
                '<h6 class="font-weight-bold mb-0 mr-2">' + pos + '</h6>' +
                '<span class="badge badge-pill badge-' + badge + ' small">' + label + '</span>' +
              '</div>' +
              '<div class="text-sm text-muted mb-1">' +
                '<i class="fas fa-building mr-1"></i> ' + dept + 
              '</div>' +
              '<div class="text-sm text-muted">' +
                '<i class="fas fa-calendar-alt mr-1"></i> ' + start + ' - ' + end + 
              '</div>' +
            '</div>' +
            '<div class="text-md-right">' +
              '<div class="mb-2">' +
                '<span class="badge badge-outline-' + typeBadge + ' border text-' + typeBadge + ' px-2">' + (e.employment_type || 'N/A') + '</span>' +
                (e.employment_refnum ? '<div class="small text-muted mt-1">Ref: ' + e.employment_refnum + '</div>' : '') +
              '</div>' +
              '<button class="btn btn-sm btn-outline-primary" onclick="showEmploymentDetailModal(\'' + e.employment_id + '\')">' +
                '<i class="fas fa-eye"></i> View Details' +
              '</button>' +
            '</div>' +
          '</div>' +
        '</div>';
    });
    $('#employmentsList').html(html);
  }

  function formatEmploymentDate(dateStr, status) {
    if (!dateStr || dateStr === '0000-00-00' || dateStr === 'null') {
      return status == '1' ? 'PRESENT' : 'N/A';
    }
    var m = moment(dateStr);
    if (!m.isValid()) {
      return status == '1' ? 'PRESENT' : 'N/A';
    }
    return m.format('MMMM D, YYYY');
  }

  function formatCurrency(amount) {
    var val = parseFloat(amount);
    if (isNaN(val)) return "0.00";
    return val.toFixed(2).replace(/\B(?=(\d{3})+(?!\d))/g, ",");
  }

  function showEmploymentDetailModal(id) {
    var e = _cachedEmployments.find(function (item) { return item.employment_id == id; });
    if (!e) return;

    var start = formatEmploymentDate(e.employment_start, '0');
    var end = formatEmploymentDate(e.employment_end, e.employment_status);
    var pos = e.designation || e.position_title || 'Position';
    var dept = e.dept_name || e.dept_assigned || 'Department';

    var html =
      '<tr><th width="40%">Ref Number</th><td>' + (e.employment_refnum || 'N/A') + '</td></tr>' +
      '<tr><th>Type</th><td>' + (e.employment_type || 'N/A') + '</td></tr>' +
      '<tr><th>Position</th><td>' + pos + '</td></tr>' +
      '<tr><th>Department</th><td>' + dept + '</td></tr>' +
      '<tr><th>Designation</th><td>' + (e.designation || 'N/A') + '</td></tr>' +
      '<tr><th>Period</th><td>' + start + ' - ' + end + '</td></tr>' +
      '<tr><th>Nature of Work</th><td>' + (e.work_nature || 'N/A') + '</td></tr>' +
      '<tr><th>Specifics</th><td>' + (e.work_specifics || 'N/A') + '</td></tr>' +
      '<tr><th>Particulars</th><td>' + (e.employment_particulars || 'N/A') + '</td></tr>' +
      '<tr><th>Rate</th><td>' + (e.rate ? '₱' + formatCurrency(e.rate) : 'N/A') + '</td></tr>';

    $('#employmentDetailContent').html(html);
    $('#employmentDetailModal').modal('show');
  }

  function checkHighlight() {
    var urlParams = new URLSearchParams(window.location.search);
    var highlightEmploymentId = urlParams.get('employment_id');
    
    if (highlightEmploymentId) {
      setTimeout(function(){
        var card = $('#employment_row_' + highlightEmploymentId);
        if (card.length) {
          $('html, body').animate({scrollTop: card.offset().top - 120}, 600);
          card.addClass('bg-light border-primary');
          setTimeout(function(){ card.removeClass('bg-light border-primary'); }, 4000);
          showEmploymentDetailModal(highlightEmploymentId);
        }
      }, 500);
    }
  }
</script>

<?php
include_once '../includes/layout/footer.php';
?>
