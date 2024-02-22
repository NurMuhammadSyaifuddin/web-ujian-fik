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
                <?= form_open('ujian/save', array('id' => 'formujian'), array('method' => 'edit', 'dosen_id' => $dosen->id_dosen, 'id_ujian' => $ujian->id_ujian)) ?>
                <div class="form-group">
                    <label for="nama_matkul">Course Name</label>
                    <select required="required" name="matkul_id" id="matkul_id" class="form-control select2" style="width:100% !important">
                        <option value="" disabled selected>Choose Course</option>
                        <?php foreach ($matkul as $m) : ?>
                            <option <?= $ujian->id_matkul === $m->matkul_id ? "selected" : ""; ?> value="<?= $m->matkul_id ?>"><?= $m->nama_matkul ?></option>
                        <?php endforeach; ?>
                    </select>
                    <small class="help-block"></small>
                </div>
                <div class="form-group">
                    <label for="nama_ujian">Exam Name</label>
                    <input value="<?= $ujian->nama_ujian ?>" autofocus="autofocus" placeholder="Exam Name" type="text" class="form-control" name="nama_ujian">
                    <small class="help-block"></small>
                </div>
                <div class="form-group">
                    <label for="jumlah_soal">Number of Questions</label>
                    <input value="<?= $ujian->jumlah_soal ?>" placeholder="Number of Questions" type="number" class="form-control" name="jumlah_soal" id="jumlah_soal">
                    <small class="help-block"></small>
                </div>
                <div class="form-group">
                    <label for="tgl_mulai">Start Date</label>
                    <input id="tgl_mulai" name="tgl_mulai" type="text" class="datetimepicker form-control" placeholder="Start Date">
                    <small class="help-block"></small>
                </div>
                <div class="form-group">
                    <label for="tgl_selesai">Completion Date</label>
                    <input id="tgl_selesai" name="tgl_selesai" type="text" class="datetimepicker form-control" placeholder="Completion Date">
                    <small class="help-block"></small>
                </div>
                <div class="form-group">
                    <label for="waktu">Time</label>
                    <input value="<?= $ujian->waktu ?>" placeholder="In Minutes" type="number" class="form-control" name="waktu">
                    <small class="help-block"></small>
                </div>
                <div class="form-group">
                    <label for="jenis">Question Pattern</label>
                    <select name="jenis" class="form-control" id="jenis">
                        <option value="" disabled selected>--- Choose ---</option>
                        <option <?= $ujian->jenis === "Random" ? "selected" : ""; ?> value="Random">Random Question</option>
                        <option <?= $ujian->jenis === "Sort" ? "selected" : ""; ?> value="Sort">Sort Question</option>
                    </select>
                    <small class="help-block"></small>
                </div>

                <input type="hidden" name="selectedQuestions" id="selectedQuestions" value="[]">

                <div id="soal_ujian_container" class="table-responsive px-4 pb-3" style="border: 0;">
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
                        <?php
                        $nomorSoal = 1; // Nomor soal dimulai dari 1
                        foreach ($soal as $s) :
                            ?>
                            <?php if ($s->matkul_id === $ujian->id_matkul) : ?>
                            <tr>
                                <td class="text-center"><input type="checkbox" class="checkbox-soal" data-soal-id="<?= $s->id_soal ?>"></td>
                                <td><?= $nomorSoal ?></td>
                                <td><?= $s->soal ?></td>
                            </tr>
                            <?php $nomorSoal++; // Tambahkan 1 setiap kali iterasi ?>
                        <?php endif; ?>
                        <?php endforeach; ?>

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

<script src="<?= base_url() ?>assets/dist/js/app/ujian/edit.js"></script>

<script type="text/javascript">
    var tgl_mulai = '<?= $ujian->tgl_mulai ?>';
    var terlambat = '<?= $ujian->terlambat ?>';
</script>

<script>
    $(document).ready(function () {
        // Variabel yang berisi data soal yang akan dicentang
        var idSoalChecked = <?php echo json_encode($id_soal_ujian); ?>;
        <?= log_message('debug', json_encode($id_soal_ujian) )?>

        // Mencentang checkbox soal berdasarkan id_soal yang sesuai dalam variabel $soal_checked
        idSoalChecked.forEach(function (soal) {
            // Mencari elemen checkbox dengan data-soal-id yang sesuai dengan id_soal dalam variabel $soal_checked
            var checkbox = $('#soal_ujian tbody input[type="checkbox"][data-soal-id="' + soal.id_soal + '"]');
            // Memeriksa apakah elemen checkbox ditemukan
            if (checkbox.length > 0) {
                // Jika ditemukan, checkbox tersebut dicentang
                checkbox.prop('checked', true);
            }

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

        // Memperbarui status tombol "Save"
        function updateSaveButtonState() {
            var jumlahSoal = parseInt($('#jumlah_soal').val()) || 0;
            var jumlahCheckboxChecked = $('#soal_ujian tbody input[type="checkbox"]:checked').length;

            // Mengaktifkan tombol "Save" jika jumlah checkbox yang tercentang sama dengan jumlah soal yang dipilih
            $('#submit').prop('disabled', jumlahCheckboxChecked !== jumlahSoal);
        }

        // Event listener untuk perubahan pada checkbox soal
        $('#soal_ujian tbody').on('change', '.checkbox-soal', function () {
            updateSaveButtonState(); // Panggil fungsi pembaruan status tombol "Save"
        });

        // Event listener untuk perubahan pada input jumlah soal
        $('#jumlah_soal').change(function () {
            updateSaveButtonState(); // Panggil fungsi pembaruan status tombol "Save"
        });

        updateSaveButtonState(); // Memanggil fungsi pembaruan status tombol "Save" saat dokumen siap
    });
</script>
