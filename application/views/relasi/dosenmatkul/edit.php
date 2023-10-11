<div class="box box-primary">
    <div class="box-header with-border">
        <h3 class="box-title">Form <?=$judul?></h3>
        <div class="box-tools pull-right">
            <a href="<?=base_url()?>dosenmatkul" class="btn btn-warning btn-flat btn-sm">
                <i class="fa fa-arrow-left"></i> Cancel
            </a>
        </div>
    </div>
    <div class="box-body">
        <div class="row">
            <div class="col-sm-4 col-sm-offset-4">
                    <?=form_open('dosenmatkul/save', array('id'=>'dosenmatkul'), array('method'=>'edit', 'dosen_id'=>$id_dosen))?>
                <div class="form-group">
                    <label>Dosen</label>
                    <input type="text" readonly="readonly" value="<?=$dosen->nama_dosen?>" class="form-control">
                    <small class="help-block text-right"></small>
                </div>
                <div class="form-group">
                    <label>Course</label>
                    <select id="matkul" multiple="multiple" name="matkul_id[]" class="form-control select2" style="width: 100%!important">
                        <?php 
                        $sj = [];
                        foreach ($matkul as $key => $val) {
                            $sj[] = $val->id_matkul;
                        }
                        foreach ($all_matkul as $m) : ?>
                            <option <?=in_array($m->id_matkul, $sj) ? "selected" : "" ?> value="<?=$m->id_matkul?>"><?=$m->nama_matkul?></option>
                        <?php endforeach; ?>
                    </select>
                    <small class="help-block text-right"></small>
                </div>
                <div class="form-group pull-right">
                    <button type="reset" class="btn btn-flat btn-default">
                        <i class="fa fa-rotate-left"></i> Reset
                    </button>
                    <button id="submit" type="submit" class="btn btn-flat bg-purple">
                        <i class="fa fa-save"></i> Save
                    </button>
                </div>
                <?=form_close()?>
            </div>
        </div>
    </div>
</div>

<script src="<?=base_url()?>assets/dist/js/app/relasi/dosenmatkul/edit.js"></script>