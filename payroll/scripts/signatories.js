$(document).ready(function() {
  // Initialize Bootstrap Switch for existing checkboxes
  $('#signatory_modal').on('shown.bs.modal', function () {
    $("input[data-bootstrap-switch]").bootstrapSwitch();
  });
});

$("#signatoryForm").on("submit", function(e) {
  e.preventDefault();

  var action_val = 'saveSignatory';
  /*var signatory_id = $('#signatory_id').val();      Needed if we want to separate the edit action
  if (signatory_id !== '') {
    action_val = 'editSignatory';
  }
  else{
    action_val = 'saveSignatory';
  }*/

  var formData = $(this).serializeArray();
  formData.push({ name: "action", value: action_val });

  $.ajax({
    url: "signatories_handler.php",
    type: "POST",
    data: formData,
    dataType: "json",
    success: function(response) {
      if (response.status === "success") {
        $('#signatory_modal').modal('hide');
        Swal.fire({
                    title: 'Success',
                    text: response.message,
                    icon: 'success',
                    confirmButtonColor: '#28a745',
                    confirmButtonText: 'Ok'
                    }).then((result) => {
                            if (result.isConfirmed) {
                                window.location.reload();
                        }
                });
      } else {
        Swal.fire("Error", response.message, "error");
      }
    },
    error: function() {
      Swal.fire("Error", "Unexpected error occurred.", "error");
    }
  });
});

function GetSignatoryDetails(signatory_id) {
  $.ajax({
    url: "signatories_handler.php",
    type: "GET",
    data: { action: "getSignatoryDetails", signatory_id: signatory_id },
    dataType: "json",
    success: function(response) {
      if (response.status === "success") {
        var data = response.data;
        $("#signatory_id").val(data.signatory_id);
        $("#employee_name").val(data.full_name);
        $("#position_title").val(data.position_title);
        $("#role_type").val(data.role_type).trigger('change');
        $("#report_code").val(data.report_type).trigger('change');
        $("#dept_id").val(data.dept_id).trigger('change');
        $("#sign_order").val(data.sign_order).trigger('change');
        $("#sign_particulars").val(data.sign_particulars);
        $("#is_active").prop('checked', Number(data.is_active) === 1).bootstrapSwitch('state', Number(data.is_active) === 1);
        $('#signatory_modal').modal('show');
      } else {
        Swal.fire("Error", response.message, "error");
      }
    },
    error: function() {
      Swal.fire("Error", "Unexpected error occurred.", "error");
    }
  });
}

function DeleteSignatory(signatory_id) {
  var action = 'delete_signatory';
    var url = 'signatories_handler.php';
    
    Swal.fire({
        title: 'Delete Signatory',
        text: "Are you sure to delete this signatory?",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#28a745',
        cancelButtonColor: '#dc3545',
        confirmButtonText: 'Yes Delete'
      }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    type: "POST",
                    url: url,
                    data: {"action" : action, "signatory_id" : signatory_id},
                    success: function(data) {
                        var obj = $.parseJSON(data);
                        var result = obj.status;
                        if(result == "success"){
                            Swal.fire({
                                title: 'Success',
                                text: obj.message,
                                icon: 'success',
                                confirmButtonColor: '#28a745',
                                confirmButtonText: 'Ok'
                                }).then((result) => {
                                        if (result.isConfirmed) {
                                            window.location.reload();
                                    }
                            });
                        }
                        else{
                            Swal.fire({
                                title: 'Error',
                                text: 'Failed to Delete Signatory.',
                                icon: 'error',
                                confirmButtonColor: '#dc3545',
                                confirmButtonText: 'Ok'
                                }).then((result) => {
                                        if (result.isConfirmed) {
                                            window.location.reload();
                                    }
                            });
                        }
                    }
                });
            }
            else{ result.dismiss === Swal.DismissReason.cancel; }
        });
}

