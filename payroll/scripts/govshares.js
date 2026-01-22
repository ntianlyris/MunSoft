$(document).ready(function () {

    // Handle form submission
  /*let clickedButton;
  $('#govShareForm button[type=submit]').click(function() {
      clickedButton = $(this);
  });*/

  $('#govShareForm').submit(function (e) {
      e.preventDefault();
      
      // Serialize form data
      let formData = $(this).serialize();

      var action = 'save_govshare';
      var govshare_id = $('#govshare_id').val();

      if(govshare_id !== ''){
        action = 'edit_govshare';
      }

      formData += '&action=' + action;
      
      // Append clicked button name=value if available
      /*if (clickedButton) {
          formData += '&' + encodeURIComponent(clickedButton.attr('name')) + '=' + encodeURIComponent(clickedButton.val());
      }*/

      $.ajax({
          url: 'govshare_handler.php',
          type: 'POST',
          data: formData,
          success: function (res) {
              if(res.status === 'success'){
                $('#govShareModal').modal('hide');
                $('#govShareForm')[0].reset();
                Swal.fire({
                    title: 'Success',
                    text: res.message,
                    icon: 'success',
                    confirmButtonColor: '#28a745',
                    confirmButtonText: 'Ok'
                    }).then((result) => {
                        if (result.isConfirmed) {
                            window.location.reload();
                        }
                });
              } else {
                Swal.fire({
                  icon: "error",
                  title: "Oopss...",
                  text: res.message,
                  confirmButtonColor: '#dc3545',
                });
              }
          },
          error: function () {
              alert('Something went wrong. Try again.');
          }
      });
  });

    // Edit
  $('#govShareTable').on('click', '.edit-btn', function() {
    const id = $(this).data('govshare-id');
    var action = 'get_govshare';
    $.getJSON(`govshare_handler.php?id=${id}&action=${action}`, function(data) {
      $('#govshare_id').val(data.govshare_id);
      $('#deduction_type').val(data.deduction_type_id);
      $('#govshare_name').val(data.govshare_name);
      $('#govshare_code').val(data.govshare_code);
      $('#govshare_acctcode').val(data.govshare_acctcode);
      $('#govshare_rate').val(data.govshare_rate);
      $('#is_percentage').val(data.is_percentage);
      $('#govShareModal').modal('show');
    });
  });

  // Delete
  $('#govShareTable').on('click', '.delete-btn', function() {
    const id = $(this).data('govshare-id');
    var action = 'delete_govshare';
    var url = 'govshare_handler.php';

    Swal.fire({
        title: 'Delete Government Share',
        text: "Other data may be affected. Are you sure to delete this Government Share data?",
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
                    data: {"action" : action, "govshare_id" : id},
                    success: function(data) {
                        //var obj = $.parseJSON(data);
                        var result = data.result;
                        var msg = data.message;
                        if(result === "success"){
                            Swal.fire({
                                title: 'Success',
                                text: msg,
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
                                text: msg,
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
  });

  // Toggle Active
  $('#govShareTable').on('click', '.toggle-btn', function() {
    const id = $(this).data('id');
    const current = $(this).data('active');
    $.post('../ajax/toggle_gov_share.php', { id, status: current == 1 ? 0 : 1 }, function() {
      table.ajax.reload();
    });
  });
});

$(function () {
    $('.select2').select2();

    $('#employee_id').on('change', function () {
      const employee_id = $(this).val();

      if (!employee_id){
          // Clear all fields if no employee is selected
          $('#monthly_rate').val('');
          $('#govshare-container').empty();
          return;
      } 

      $.ajax({
        url: 'govshare_handler.php',
        type: 'POST',
        data: { employee_id: employee_id, action: 'compute_govshares' },
        dataType: 'json',
        success: function (res) {
          if (res.status === 'success') {
            $('#monthly_rate').val(res.data.monthly_rate);

            // Clear old entries first if you are dynamically generating them
            $('#govshare-container').empty();

            // Loop through each govshare type (GSIS, PAGIBIG, PHIC, etc.)
            $.each(res.data.shares, function (type, sharesArray) {
              sharesArray.forEach(function (item) {
                // Create a row for each item using jQuery
                const inputGroup = `
                  <div class="col-md-3 mb-3">
                    <label>${item.govshare_name}</label>
                    <input type="text" class="form-control govshare-amount" name="govshare_amount[]" value="${item.amount}">
                    <input type="hidden" class="govshare-id" name="govshare_id[]" value="${item.govshare_id}">
                  </div>
                `;
                $('#govshare-container').append(inputGroup);
              });
            });
          } else {
            Swal.fire({
              icon: 'error',
              title: 'Error',
              text: res.message || 'Unable to compute government shares.',
            });
          }
        },
        error: function () {
          Swal.fire({
            icon: 'error',
            title: 'Request Failed',
            text: 'Something went wrong with the request.',
          });
        }
      });

    });

    // Submit handler
    $('#govShareFormEmployee').on('submit', function (e) {
      e.preventDefault();

      const employee_id = $('#employee_id').val();

      if (!employee_id) {
        Swal.fire({
          icon: 'warning',
          title: 'Missing Employee',
          text: 'Please select an employee before saving.',
        });
        return;
      }

      // Serialize form data
      let formData = $(this).serialize();
      formData += '&action=save_employee_govshares';
      
      $.ajax({
        url: 'govshare_handler.php',
        type: 'POST',
        data: formData, // Send as JSON JSON.stringify(formData)
        //contentType: 'application/json', // Important
        dataType: 'json',
        success: function (res) {
          if (res.status === 'success') {
            Swal.fire({
              icon: 'success',
              title: 'Saved!',
              text: res.message,
              confirmButtonColor: '#28a745',
              confirmButtonText: 'Ok'
                }).then((result) => {
                  if (result.isConfirmed) {
                    window.location.reload();
                    $('#govShareFormEmployee')[0].reset();
                    $('#employee_id').val(null).trigger('change');
                  }
            }); 
          } else {
            Swal.fire({
              icon: 'error',
              title: 'Failed to Save',
              text: res.message || 'Something went wrong while saving data.',
            });
          }
        },
        error: function () {
          Swal.fire({
            icon: 'error',
            title: 'Request Failed',
            text: 'There was a problem with the server request.',
          });
        }
      });
    });

  });