<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class DosenMatkul extends CI_Controller {

	public function __construct(){
		parent::__construct();
		if (!$this->ion_auth->logged_in()){
			redirect('auth');
		}else if (!$this->ion_auth->is_admin()){
			show_error('Only Administrators are authorized to access this page, <a href="'.base_url('dashboard').'">Back to main menu</a>', 403, 'Forbidden Access');			
		}
		$this->load->library(['datatables', 'form_validation']);// Load Library Ignited-Datatables
		$this->load->model('Master_model', 'master');
		$this->form_validation->set_error_delimiters('','');
	}

	public function output_json($data, $encode = true)
	{
        if($encode) $data = json_encode($data);
        $this->output->set_content_type('application/json')->set_output($data);
    }

    public function index()
	{
		$data = [
			'user' => $this->ion_auth->user()->row(),
			'judul'	=> 'Lecturer Course',
			'subjudul'=> 'Data Lecturer Course'
		];
		$this->load->view('_templates/dashboard/_header.php', $data);
		$this->load->view('relasi/dosenmatkul/data');
		$this->load->view('_templates/dashboard/_footer.php');
    }

    public function data()
    {
        $this->output_json($this->master->getDosenMatkul(), false);
	}

	public function getMatkulId($id)
	{
		$this->output_json($this->master->getAllMatkulById($id));
	}
	
	public function add()
	{
		$data = [
			'user' 		=> $this->ion_auth->user()->row(),
			'judul'		=> 'Add Lecturer Course',
			'subjudul'	=> 'Add Lecturer Course Data',
			'dosen'	=> $this->master->getDosen()
		];
		$this->load->view('_templates/dashboard/_header.php', $data);
		$this->load->view('relasi/dosenmatkul/add');
		$this->load->view('_templates/dashboard/_footer.php');
	}

	public function edit($id)
	{
		$data = [
			'user' 			=> $this->ion_auth->user()->row(),
			'judul'			=> 'Edit Lecturer Course',
			'subjudul'		=> 'Edit Data Lecturer Course',
			'dosen'		    => $this->master->getDosenByIdByCourse($id, true),
			'id_dosen'		=> $id,
			'all_matkul'	=> $this->master->getAllMatkulById(),
			'matkul'		=> $this->master->getMatkulByIdDosen($id)
		];
		$this->load->view('_templates/dashboard/_header.php', $data);
		$this->load->view('relasi/dosenmatkul/edit');
		$this->load->view('_templates/dashboard/_footer.php');
	}

	public function save()
	{
		$method = $this->input->post('method', true);
		$this->form_validation->set_rules('dosen_id', 'Lecturer', 'required');
		$this->form_validation->set_rules('matkul_id[]', 'Course', 'required');
	
		if($this->form_validation->run() == FALSE){
			$data = [
				'status'	=> false,
				'errors'	=> [
					'dosen_id' => form_error('dosen_id'),
					'matkul_id[]' => form_error('matkul_id[]'),
				]
			];
			$this->output_json($data);
		}else{
			$dosen_id 	= $this->input->post('dosen_id', true);
			$matkul_id = $this->input->post('matkul_id', true);
			$input = [];
			foreach ($matkul_id as $key => $val) {
				$input[] = [
					'dosen_id' 	=> $dosen_id,
					'matkul_id'  	=> $val
				];
			}
			if($method==='add'){
				$action = $this->master->create('dosen_matkul', $input, true);
			}else if($method==='edit'){
				$id = $this->input->post('dosen_id', true);
				$this->master->delete('dosen_matkul', $id, 'dosen_id');
				$action = $this->master->create('dosen_matkul', $input, true);
			}
			$data['status'] = $action ? TRUE : FALSE ;
		}
		$this->output_json($data);
	}

	public function delete()
    {
        $chk = $this->input->post('checked', true);
        if(!$chk){
            $this->output_json(['status'=>false]);
        }else{
            if($this->master->delete('dosen_matkul', $chk, 'dosen_id')){
                $this->output_json(['status'=>true, 'total'=>count($chk)]);
            }
        }
	}
}