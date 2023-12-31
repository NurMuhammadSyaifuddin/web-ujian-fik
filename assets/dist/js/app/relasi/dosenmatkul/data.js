var save_label;
var table;

$(document).ready(function() {
  ajaxcsrf();

  table = $("#dosenmatkul").DataTable({
    initComplete: function() {
      var api = this.api();
      $("#dosenmatkul_filter input")
        .off(".DT")
        .on("keyup.DT", function(e) {
          api.search(this.value).draw();
        });
    },
    dom:
      "<'row'<'col-sm-3'l><'col-sm-6 text-center'B><'col-sm-3'f>>" +
      "<'row'<'col-sm-12'tr>>" +
      "<'row'<'col-sm-5'i><'col-sm-7'p>>",
    buttons: [
      {
        extend: "copy",
        exportOptions: { columns: [1, 2] }
      },
      {
        extend: "print",
        exportOptions: { columns: [1, 2] }
      },
      {
        extend: "excel",
        exportOptions: { columns: [1, 2] }
      },
      {
        extend: "pdf",
        exportOptions: { columns: [1, 2] }
      }
    ],
    oLanguage: {
      sProcessing: "loading..."
    },
    processing: true,
    serverSide: true,
    ajax: {
      url: base_url + "dosenmatkul/data",
      type: "POST"


    },
    columns: [
      {
        data: "id",
        orderable: false,
        searchable: false
      },
      { data: "nama_dosen" }
    ],
    columnDefs: [
      {
        targets: 2,
        searchable: false,
        orderable: false,
        title: "matkul",
        data: "nama_matkul",
        render: function(data, type, row, meta) {
          let dosen = data.split(",");
          let badge = [];
          $.each(dosen, function(i, val) {
            var newdosen = `<span class="badge bg-green">${val}</span>`;
            badge.push(newdosen);
          });
          return badge.join(" ");
        }
      },
      {
        targets: 3,
        searchable: false,
        orderable: false,
        data: "id_dosen",
        render: function(data, type, row, meta) {
          return `<div class="text-center">
									<a href="${base_url}dosenmatkul/edit/${data}" class="btn btn-warning btn-xs">
										<i class="fa fa-pencil"></i>
									</a>
								</div>`;
        }
      },
      {
        targets: 4,
        data: "id_dosen",
        render: function(data, type, row, meta) {
          return `<div class="text-center">
									<input name="checked[]" class="check" value="${data}" type="checkbox">
								</div>`;
        }
      }
    ],
    order: [[1, "asc"]],
    rowId: function(a) {
      return a;
    },
    rowCallback: function(row, data, iDisplayIndex) {
      var info = this.fnPagingInfo();
      var page = info.iPage;
      var length = info.iLength;
      var index = page * length + (iDisplayIndex + 1);
      $("td:eq(0)", row).html(index);
    }
  });

  table
    .buttons()
    .container()
    .appendTo("#dosenmatkul_wrapper .col-md-6:eq(0)");

  $("#myModal").on("shown.modal.bs", function() {
    $(':input[name="banyak"]').select();
  });

  $(".select_all").on("click", function() {
    if (this.checked) {
      $(".check").each(function() {
        this.checked = true;
        $(".select_all").prop("checked", true);
      });
    } else {
      $(".check").each(function() {
        this.checked = false;
        $(".select_all").prop("checked", false);
      });
    }
  });

  $("#dosenmatkul tbody").on("click", "tr .check", function() {
    var check = $("#dosenmatkul tbody tr .check").length;
    var checked = $("#dosenmatkul tbody tr .check:checked").length;
    if (check === checked) {
      $(".select_all").prop("checked", true);
    } else {
      $(".select_all").prop("checked", false);
    }
  });

  $("#bulk").on("submit", function(e) {
    if ($(this).attr("action") == base_url + "dosenmatkul/delete") {
      e.preventDefault();
      e.stopImmediatePropagation();

      $.ajax({
        url: $(this).attr("action"),
        data: $(this).serialize(),
        type: "POST",
        success: function(respon) {
          if (respon.status) {
            Swal({
              title: "Successful",
              text: respon.total + " data deleted successfully",
              type: "success"
            });
          } else {
            Swal({
              title: "Failed",
              text: "No data selected",
              type: "error"
            });
          }
          reload_ajax();
        },
        error: function() {
          Swal({
            title: "Failed",
            text: "There is data in use",
            type: "error"
          });
        }
      });
    }
  });
});

function bulk_delete() {
  if ($("#dosenmatkul tbody tr .check:checked").length == 0) {
    Swal({
      title: "Failed",
      text: "No data selected",
      type: "error"
    });
  } else {
    $("#bulk").attr("action", base_url + "dosenmatkul/delete");
    Swal({
      title: "You sure?",
      text: "Data will be deleted!",
      type: "warning",
      showCancelButton: true,
      confirmButtonColor: "#3085d6",
      cancelButtonColor: "#d33",
      confirmButtonText: "Delete!"
    }).then(result => {
      if (result.value) {
        $("#bulk").submit();
      }
    });
  }
}
