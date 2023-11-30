  <div class="table-responsive px-4 pb-3" style="border: 0">
        <table id="soal_ujiqn" class="w-100 table table-striped table-bordered table-hover">
        <thead>
            <tr>
				<th class="text-center">
					<input type="checkbox" class="select_all">
				</th>
                <th width="25">No</th>
				<th>Question</th>
            </tr>        
        </thead>
        </table>
    </div>

<script src="<?=base_url()?>assets/dist/js/app/soal/data_ujian.js"></script>

  <?php if ( $this->ion_auth->in_group('Lecturer') ) : ?>
      <script type="text/javascript">
          $(document).ready(function(){
              $('#matkul_id').on('change', function(){
                  let id_matkul = $(this).val();
                  let src = '<?=base_url()?>soal/data';
                  let url;

                  if(id_matkul !== 'all'){
                      let src2 = src + '/' + id_matkul;
                      url = $(this).prop('checked') === true ? src : src2;
                  }else{
                      url = src;
                  }
                  table.ajax.url(url).load();
              });

          });
      </script>
  <?php endif; ?>