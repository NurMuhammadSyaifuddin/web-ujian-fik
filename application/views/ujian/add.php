<div class="box">
    <div class="box-header with-border">
        <h3 class="box-title"><?= $subjudul ?></h3>
        <div class="box-tools pull-right">
            <a href="<?= base_url() ?>ujian/master" class="btn btn-sm btn-flat btn-warning">
                <i class="fa fa-arrow-left"></i> Cancel
            </a>
        </div>
    </div>
    <div class="box-body">
        <div class="row">
            <div class="col-sm-4">
                <div class="alert bg-purple">
                    <h4>Lecturer <i class="fa fa-address-book-o pull-right"></i></h4>
                    <p><?= $dosen->nama_dosen ?></p>
                </div>
            </div>
            <div class="col-sm-4">
                <?= form_open('ujian/save', array('id' => 'formujian'), array('method' => 'add', 'dosen_id' => $dosen->id_dosen)) ?>
                <div class="form-group">
                    <label for="nama_matkul">Course Name</label>
                    <select required="required" name="matkul_id" id="matkul_id" class="form-control select2"
                            style="width:100% !important">
                        <option value="" disabled selected>Choose Course</option>
                        <?php foreach ($matkul as $m) : ?>
                            <option value="<?= $m->matkul_id ?>"><?= $m->nama_matkul ?></option>
                        <?php endforeach; ?>
                    </select>
                    <small class="help-block"></small>
                </div>
                <div class="form-group">
                    <label for="nama_ujian">Exam Name</label>
                    <input autofocus="autofocus" onfocus="this.select()" placeholder="Exam Name" type="text"
                           class="form-control" name="nama_ujian">
                    <small class="help-block"></small>
                </div>
                <div class="form-group">
                    <label for="jumlah_soal">Number of Questions</label>
                    <input placeholder="Number of Questions" type="number" class="form-control" name="jumlah_soal">
                    <small class="help-block"></small>
                </div>
                <div class="form-group">
                    <label for="tgl_mulai">Start Date</label>
                    <input name="tgl_mulai" type="text" class="datetimepicker form-control" placeholder="Start Date">
                    <small class="help-block"></small>
                </div>
                <div class="form-group">
                    <label for="tgl_selesai">Date of completion</label>
                    <input name="tgl_selesai" type="text" class="datetimepicker form-control"
                           placeholder="Date of completion">
                    <small class="help-block"></small>
                </div>
                <div class="form-group">
                    <label for="waktu">Time</label>
                    <input placeholder="In Minute" type="number" class="form-control" name="waktu">
                    <small class="help-block"></small>
                </div>
                <div class="form-group">
                    <label for="jenis">Question Pattern</label>
                    <select name="jenis" class="form-control" id="jenis">
                        <option value="" disabled selected>--- Choose ---</option>
                        <option value="Random">Random Question</option>
                        <option value="Sort">Sort Questions</option>
                    </select>
                    <small class="help-block"></small>
                </div>

                <div id="soal_ujian_container" class="table-responsive px-4 pb-3" style="border: 0; display: none">
                    <table id="soal_ujian" class="w-100 table table-striped table-bordered table-hover">
                        <thead>
                        <tr>
                            <th></th>
                            <th width="25">No</th>
                            <th>Question</th>
                        </tr>
                        </thead>
                        <tbody>

                        <script>
                            $(document).ready(function () {
                                var selectedQuestions = [];
                                // Simpan data soal di sisi klien
                                var soalData = <?php echo json_encode($soal); ?>;

                                // Ambil elemen <select> menggunakan ID
                                var selectElement = $('#matkul_id');
                                var selectElementJenis = $('#jenis');
                                var jumlahSoalInput = $('input[name="jumlah_soal"]');
                                var saveButton = $('#submit');

                                // Tambahkan event listener untuk mendeteksi perubahan nilai
                                selectElement.change(function () {
                                    // Ambil nilai yang dipilih
                                    var selectedValue = $(this).val();

                                    // Filter data soal berdasarkan nilai yang dipilih
                                    var filteredSoal = soalData.filter(function (s) {
                                        return s.matkul_id === selectedValue;
                                    });

                                    // Tampilkan atau sembunyikan baris tabel berdasarkan hasil filter
                                    $('#soal_ujian tbody').empty();
                                    $.each(filteredSoal, function (index, s) {
                                        var row = '<tr>' +
                                            '<td class="text-center"><input type="checkbox"></td>' +
                                            '<td>' + (index + 1) + '</td>' +
                                            '<td>' + s.soal + '</td>' +
                                            '</tr>';
                                        $('#soal_ujian tbody').append(row);
                                    });

                                    selectElementJenis.change(function () {
                                        var selectedValueJenis = $(this).val();
                                        if (filteredSoal.length > 0 && selectedValueJenis === "Sort") {
                                            $('#soal_ujian_container').show();
                                        } else {
                                            $('#soal_ujian_container').hide();
                                        }
                                    });

                                    // Enable or disable the "Save" button based on the condition
                                    updateSaveButtonState();
                                });

                                // Tambahkan event listener untuk mendeteksi perubahan pada input jumlah_soal
                                jumlahSoalInput.change(function () {
                                    // Enable or disable the "Save" button based on the condition
                                    updateSaveButtonState();
                                });

                                // Event listener for checkbox changes
                                $('#soal_ujian tbody').on('change', '.checkbox-soal', function () {
                                    var soalId = $(this).data('soal-id');
                                    if ($(this).prop('checked')) {
                                        // Add the selected question to the array
                                        selectedQuestions.push(soalId);
                                    } else {
                                        // Remove the deselected question from the array
                                        var index = selectedQuestions.indexOf(soalId);
                                        if (index !== -1) {
                                            selectedQuestions.splice(index, 1);
                                        }
                                    }

                                    // Enable or disable the "Save" button based on the condition
                                    updateSaveButtonState();
                                });

                                // Function to update the state of the "Save" button
                                function updateSaveButtonState() {
                                    var jumlahSoal = parseInt(jumlahSoalInput.val()) || 0;
                                    var jumlahCheckboxChecked = $('#soal_ujian tbody input[type="checkbox"]:checked').length;

                                    if (jumlahCheckboxChecked === jumlahSoal && jumlahSoal !== 0) {
                                        saveButton.prop('disabled', false);
                                    } else {
                                        saveButton.prop('disabled', true);
                                    }

                                }
                            });
                        </script>


                        </tbody>
                    </table>
                </div>

                <div class="form-group pull-right">
                    <button type="reset" class="btn btn-default btn-flat">
                        <i class="fa fa-rotate-left"></i> Reset
                    </button>
                    <button id="submit" type="submit" class="btn btn-flat bg-purple"><i class="fa fa-save"></i> Save
                    </button>
                </div>
                <?= form_close() ?>
            </div>
        </div>
    </div>
</div>

<script src="<?= base_url() ?>assets/dist/js/app/ujian/add.js"></script>