<?php
defined('BASEPATH') or exit('No direct script access allowed');

class HasilUjian extends CI_Controller
{
    public function __construct()
    {
        parent::__construct();
        if (!$this->ion_auth->logged_in()) {
            redirect('auth');
        }

        $this->load->library(['datatables']);// Load Library Ignited-Datatables
        $this->load->model('Master_model', 'master');
        $this->load->model('Ujian_model', 'ujian');

        $this->user = $this->ion_auth->user()->row();
    }

    public function output_json($data, $encode = true)
    {
        if ($encode) $data = json_encode($data);
        $this->output->set_content_type('application/json')->set_output($data);
    }

    public function data()
    {
        $nip_dosen = null;

        if ($this->ion_auth->in_group('Lecturer')) {
            $nip_dosen = $this->user->username;
        }

        $this->output_json($this->ujian->getHasilUjian($nip_dosen), false);
    }

    public function NilaiMhs($id)
    {
        $this->output_json($this->ujian->HslUjianById($id, true), false);
    }

    public function index()
    {
        $data = [
            'user' => $this->user,
            'judul' => 'Exam',
            'subjudul' => 'Exam results',
        ];
        $this->load->view('_templates/dashboard/_header.php', $data);
        $this->load->view('ujian/hasil');
        $this->load->view('_templates/dashboard/_footer.php');
    }

    public function detail($id)
    {
        $ujian = $this->ujian->getUjianById($id);
        $nilai = $this->ujian->bandingNilai($id);
        $data = $this->ujian->HslUjianById($this->user->username, true);

        $data = [
            'user' => $this->user,
            'judul' => 'Exam',
            'subjudul' => 'Detail Exam results',
            'ujian' => $ujian,
            'nilai' => $nilai,
            'data' => $data,
        ];

        $this->load->view('_templates/dashboard/_header.php', $data);
        $this->load->view('ujian/detail_hasil');
        $this->load->view('_templates/dashboard/_footer.php');
    }

    public function cetak($id)
    {
        $this->load->library('Pdf');

        $mhs = $this->ujian->getIdMahasiswa($this->user->username);
        $hasil = $this->ujian->HslUjian($id, $mhs->id_mahasiswa)->row();
        $ujian = $this->ujian->getUjianById($id);

        $data = [
            'ujian' => $ujian,
            'hasil' => $hasil,
            'mhs' => $mhs
        ];

        $this->load->view('ujian/cetak', $data);
    }

    public function cetak_detail($id)
    {
        $this->load->library('Pdf');

        $ujian = $this->ujian->getUjianById($id);
        $nilai = $this->ujian->bandingNilai($id);
        $hasil = $this->ujian->HslUjianById($id)->result();

        $data = [
            'ujian' => $ujian,
            'nilai' => $nilai,
            'hasil' => $hasil
        ];

        $this->load->view('ujian/cetak_detail', $data);
    }

    public function cetak_analisis($id)
    {
        $this->load->library('Pdf');

        $dosen_id = $this->input->get('id_dosen');
        $matkul_id = $this->input->get('id_matkul');


        $ujian = $this->ujian->getUjianById($id);
        $hasil = $this->ujian->HslUjianById($id)->result();

        $list_jawaban = $this->ujian->getJawaban($id);
        $real_jawaban = $this->ujian->getRealJawaban($dosen_id, $matkul_id);

        $jumlahBenarPerSoal = $this->hitungJumlahBenarPerSoal(json_encode($list_jawaban), json_encode($real_jawaban));
        $jumlah_soal = $this->ujian->getAllJawaban($id);

        $jmlPesertaKelasAtasOrBawah = 1;
        $jumlah_soal_real = count($jumlah_soal);
        if ($jumlah_soal_real > 30){
            $jmlPesertaKelasAtasOrBawah = ceil($jumlah_soal_real * 0.27);
        } else if ($jumlah_soal_real > 1){
            $jmlPesertaKelasAtasOrBawah = floor($jumlah_soal_real * 0.50);
        }

        if ($jmlPesertaKelasAtasOrBawah > 2) {
            $kelasAtas = $this->hitungJumlahBenarPerSoal(json_encode($this->ujian->getPesertaKelas($id, $jmlPesertaKelasAtasOrBawah, 'atas', $jumlah_soal_real)), json_encode($real_jawaban));
            $kelasBawah = $this->hitungJumlahBenarPerSoal(json_encode($this->ujian->getPesertaKelas($id, $jmlPesertaKelasAtasOrBawah, 'bawah', $jumlah_soal_real)), json_encode($real_jawaban));
        }else {
            $kelasAtas = $this->hitungJumlahBenarPerSoal(json_encode($this->ujian->getPesertaKelas($id, $jmlPesertaKelasAtasOrBawah, 'atas', $jumlah_soal_real)), json_encode($real_jawaban));
            $kelasBawah = $this->hitungJumlahBenarPerSoal(json_encode($this->ujian->getPesertaKelas($id, $jmlPesertaKelasAtasOrBawah, 'bawah', $jumlah_soal_real)), json_encode($real_jawaban));
        }

        $data = [
            'jml_benar' => $jumlahBenarPerSoal,
            'jml_soal' => $jumlah_soal,
            'jml_atas_bawah' => $jmlPesertaKelasAtasOrBawah,
            'ujian' => $ujian,
            'hasil' => $hasil,
            'kelas_atas' => $kelasAtas,
            'kelas_bawah' => $kelasBawah,
            'real_jawaban' => json_encode($real_jawaban),
            'list_jawaban' => json_encode($this->ujian->getPesertaKelas($id, $jmlPesertaKelasAtasOrBawah, 'atas', $jumlah_soal_real)[0]->list_jawaban)
        ];

        $this->load->view('ujian/cetak_analisis', $data);
    }

    function hitungJumlahBenarPerSoal($list_jawaban, $real_jawaban) {

        $new_list_jawaban = explode(',', $list_jawaban);
        $new_list_jawaban_real = array_fill(0, count($new_list_jawaban), 0);

        for ($i = 0; $i < count($new_list_jawaban); $i++){
            $new_list_jawaban_real[$i] = $new_list_jawaban[$i][1];
        }

        $new_real = explode(',', $real_jawaban);

        for ($i = 0; $i < count($new_list_jawaban_real); $i++){
            for ($j = 0; $j < count($new_real); $j++){
                $jawaban = $new_list_jawaban_real[$i][$j];
                if ($jawaban == $real_jawaban[$j]){
                    $new_list_jawaban[$i]++;
                }
            }

        }

       return $new_list_jawaban;
    }


}