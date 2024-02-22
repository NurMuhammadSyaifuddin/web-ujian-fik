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
                    <select required="required" name="matkul_id" id="matkul_id" class="form-control select2" style="
                    width:100% !important">
                        <option value="" disabled selected>Choose Course</option>
                        <?php foreach ($matkul as $m) : ?>
                            <option value="<?= $m->matkul_id ?>"><?= $m->nama_matkul ?></option>
                        <?php endforeach; ?>
                    </select>
                    <small class="help-block"></small>
                </div>
                <div class="form-group">
                    <label for="nama_ujian">Exam Name</label>
                    <input autofocus="autofocus" onfocus="this.select()" placeholder="Exam Name" type="text" class="form-control" name="nama_ujian">
                    <small class="help-block"></small>
                </div>
                <div class="form-group">
                    <label for="jumlah_soal">Number of Questions</label>
                    <input placeholder="Number of Questions" type="number" class="form-control" name="jumlah_soal" id="jumlah_soal">
                    <small class="help-block"></small>
                </div>
                <div class="form-group">
                    <label for="tgl_mulai">Start Date</label>
                    <input name="tgl_mulai" type="text" class="datetimepicker form-control" placeholder="Start Date">
                    <small class="help-block"></small>
                </div>
                <div class="form-group">
                    <label for="tgl_selesai">Date of completion</label>
                    <input name="tgl_selesai" type="text" class="datetimepicker form-control" placeholder="Date of completion">
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

                <input type="hidden" name="selectedQuestions" id="selectedQuestions" value="[]">

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
                        <!-- Checkbox Questions will be dynamically added here -->
                        </tbody>
                    </table>
                </div>

                <div class="form-group pull-right">
                    <button type="reset" class="btn btn-default btn-flat">
                        <i class="fa fa-rotate-left"></i> Reset
                    </button>
                    <button id="submit" type="submit" class="btn btn-flat bg-purple" disabled>
                        <i class="fa fa-save"></i> Save
                    </button>
                </div>
                <?= form_close() ?>
            </div>
        </div>
    </div>
</div>

<script src="<?= base_url() ?>assets/dist/js/app/ujian/add.js"></script>

<script>
    $(document).ready(function () {
        var soalData = <?php echo json_encode($soal); ?>;
        var selectElement = $('#matkul_id');
        var selectElementJenis = $('#jenis');
        var jumlahSoalInput = $('#jumlah_soal');
        var saveButton = $('#submit');
        <?= log_message('debug', json_encode($soal) )?>
        selectElement.change(function () {
            var selectedValue = $(this).val();
            var filteredSoal = soalData.filter(function (s) {
                return s.matkul_id == selectedValue;
            });

            $('#soal_ujian tbody').empty();
            $.each(filteredSoal, function (index, s) {
                var row = '<tr>' +
                    '<td class="text-center"><input type="checkbox" class="checkbox-soal" data-soal-id="' + s.id_soal + '"></td>' +
                    '<td>' + (index + 1) + '</td>' +
                    '<td>' + s.soal + '</td>' +
                    '</tr>';
                $('#soal_ujian tbody').append(row);
            });

            updateSaveButtonState();
        });

        selectElementJenis.change(function () {
            var selectedValueJenis = $(this).val();
            if (selectedValueJenis === "Sort") {
                $('#soal_ujian_container').show();
            } else {
                $('#soal_ujian_container').hide();
            }
            updateSaveButtonState();
        });

        $('#soal_ujian tbody').on('change', '.checkbox-soal', function () {
            var selectedSoalIds = [];
            $('#soal_ujian tbody input[type="checkbox"]:checked').each(function () {
                var soalId = $(this).data('soal-id');
                selectedSoalIds.push(soalId);
            });
            $('#selectedQuestions').val(JSON.stringify(selectedSoalIds));
            updateSaveButtonState(); // Panggil fungsi pembaruan status tombol "Save"
        });


        jumlahSoalInput.change(function () {
            updateSaveButtonState();
        });

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
