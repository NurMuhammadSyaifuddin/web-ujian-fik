
var table;

$(document).ready(function() {
  ajaxcsrf();

  table = $("#soal_ujian").DataTable({
    initComplete: function() {
      var api = this.api();
      $("#soal_ujian_filter input")
        .off(".DT")
        .on("keyup.DT", function(e) {
          api.search(this.value).draw();
        });
    },
    dom:
      "<'row'<'col-sm-3'l><'col-sm-6 text-center'B><'col-sm-3'f>>" +
      "<'row'<'col-sm-12'tr>>" +
      "<'row'<'col-sm-5'i><'col-sm-7'p>>",
    oLanguage: {
      sProcessing: "loading..."
    },
    processing: true,
    serverSide: true,
    ajax: {
      url: base_url + "soal/data_ujian",
      type: "POST"
    },
    columns: [
      {
        data: "id_soal",
        orderable: false,
        searchable: false
      },
      { data: "soal" },
    ],
    columnDefs: [
      {
        targets: 0,
        data: "id_soal",
        render: function(data, type, row, meta) {
          return `<div class="text-center">
									<input name="checked[]" class="check" value="${data}" type="checkbox">
								</div>`;
        }
      },

    ],
    rowId: function(a) {
      return a;
    },
    rowCallback: function(row, data, iDisplayIndex) {
      var info = this.fnPagingInfo();
      var page = info.iPage;
      var length = info.iLength;
      var index = page * length + (iDisplayIndex + 1);
      $("td:eq(1)", row).html(index);
    }
  });

  table
    .buttons()
    .container()
    .appendTo("#soal_ujian_wrapper .col-md-6:eq(0)");

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

  $("#soal_ujian tbody").on("click", "tr .check", function() {
    var check = $("#soal_ujian tbody tr .check").length;
    var checked = $("#soal_ujian tbody tr .check:checked").length;
    if (check === checked) {
      $(".select_all").prop("checked", true);
    } else {
      $(".select_all").prop("checked", false);
    }
  });

  $("#bulk").on("submit", function(e) {
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
  });
});

function bulk_delete() {
  if ($("#soal_ujian tbody tr .check:checked").length == 0) {
    Swal({
      title: "Failed",
      text: "No data selected",
      type: "error"
    });
  } else {
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
