<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Ujian_model extends CI_Model {

    public function getDataUjian($id)
    {
        $this->datatables->select('a.id_ujian, a.token, a.nama_ujian, b.nama_matkul, a.jumlah_soal, CONCAT(a.tgl_mulai, " <br/> (", a.waktu, " Minute)") as waktu, a.jenis');
        $this->datatables->from('m_ujian a');
        $this->datatables->join('matkul b', 'a.matkul_id = b.id_matkul');
        if($id!==null){
            $this->datatables->where('dosen_id', $id);
        }
        return $this->datatables->generate();
    }
    
    public function getListUjian($id, $kelas)
    {
        $this->datatables->select("a.id_ujian, e.nama_dosen, d.nama_kelas, a.nama_ujian, b.nama_matkul, a.jumlah_soal, CONCAT(a.tgl_mulai, ' <br/> (', a.waktu, ' Minute)') as waktu,  (SELECT COUNT(id) FROM h_ujian h WHERE h.mahasiswa_id = {$id} AND h.ujian_id = a.id_ujian) AS ada");
        $this->datatables->from('m_ujian a');
        $this->datatables->join('matkul b', 'a.matkul_id = b.id_matkul');
        $this->datatables->join('kelas_dosen c', "a.dosen_id = c.dosen_id");
        $this->datatables->join('kelas d', 'c.kelas_id = d.id_kelas');
        $this->datatables->join('dosen e', 'e.id_dosen = c.dosen_id');
        $this->datatables->where('d.id_kelas', $kelas);
        return $this->datatables->generate();
    }

    public function getUjianById($id)
    {
        $this->db->select('*');
        $this->db->from('m_ujian a');
        $this->db->join('dosen b', 'a.dosen_id=b.id_dosen');
        $this->db->join('matkul c', 'a.matkul_id=c.id_matkul');
        $this->db->where('id_ujian', $id);
        return $this->db->get()->row();
    }

    public function getIdSoalUjian($id){
        $idSoalByUjian = $this->getUjianById($id);
        $this->db->select('a. id_soal');
        $this->db->from('soal_ujian a');
        $this->db->where('a.id_soal_ujian', $idSoalByUjian->soal_ujian_id);
        return $this->db->get();
    }

    public function getIdDosen($nip)
    {
        $this->db->select('id_dosen, nama_dosen')->from('dosen')->where('nip', $nip);
        return $this->db->get()->row();
    }

    public function getJumlahSoal($dosen)
    {
        $this->db->select('COUNT(id_soal) as jml_soal');
        $this->db->from('tb_soal');
        $this->db->where('dosen_id', $dosen);
        return $this->db->get()->row();
    }

    public function getIdMahasiswa($nim)
    {
        $this->db->select('*');
        $this->db->from('mahasiswa a');
        $this->db->join('kelas b', 'a.kelas_id=b.id_kelas');
        $this->db->join('jurusan c', 'b.jurusan_id=c.id_jurusan');
        $this->db->where('nim', $nim);
        return $this->db->get()->row();
    }

    public function HslUjian($id, $mhs)
    {
        $this->db->select('*, UNIX_TIMESTAMP(tgl_selesai) as waktu_habis');
        $this->db->from('h_ujian');
        $this->db->where('ujian_id', $id);
        $this->db->where('mahasiswa_id', $mhs);
        return $this->db->get();
    }

    public function getSoal($id)
    {
        $ujian = $this->getUjianById($id);
        $order = $ujian->jenis === "Random" ? 'rand()' : 'tb_soal.id_soal';

        $this->db->select('tb_soal.id_soal, tb_soal.soal, tb_soal.file, tb_soal.tipe_file, tb_soal.opsi_a, tb_soal.opsi_b, tb_soal.opsi_c, tb_soal.opsi_d, tb_soal.opsi_e, tb_soal.jawaban, tb_soal.matkul_id');
        $this->db->from('tb_soal');
        $this->db->join('soal_ujian', 'tb_soal.id_soal = soal_ujian.id_soal');
        $this->db->join('m_ujian', 'soal_ujian.id_soal_ujian = m_ujian.soal_ujian_id');
        $this->db->where('m_ujian.dosen_id', $ujian->dosen_id);
        $this->db->where('m_ujian.matkul_id', $ujian->matkul_id);

        if ($ujian->jenis === "Sort") {
            // Include selected questions in the result based on soal_ujian
            $this->db->join('soal_ujian su', 'tb_soal.id_soal = su.id_soal');
            $this->db->order_by('su.id_soal_ujian');
        } else {
            $this->db->order_by($order);
        }

        $this->db->limit($ujian->jumlah_soal);
        return $this->db->get()->result();
    }




//    public function getSoal($id, $selectedQuestions)
//    {
//        $ujian = $this->getUjianById($id);
//        $order = $ujian->jenis==="Random" ? 'rand()' : 'id_soal';
//
//        $this->db->select('id_soal, soal, file, tipe_file, opsi_a, opsi_b, opsi_c, opsi_d, opsi_e, jawaban', 'matkul_id');
//        $this->db->from('tb_soal');
//        $this->db->where('dosen_id', $ujian->dosen_id);
//        $this->db->where('matkul_id', $ujian->matkul_id);
//        if ($ujian->jenis === "Sort" && !empty($selectedQuestions)) {
//            // Include selected questions in the result
//
//            $this->db->where_in('id_soal', $selectedQuestions);
//        }else{
//            $this->db->order_by($order);
//        }
//
//        $selectedQuestionsString = implode(', ', $selectedQuestions);
//        log_message('error', $selectedQuestionsString);
//
//        $this->db->limit($ujian->jumlah_soal);
//        return $this->db->get()->result();
//    }

    public function ambilSoal($pc_urut_soal1, $pc_urut_soal_arr)
    {
        $this->db->select("*, {$pc_urut_soal1} AS jawaban");
        $this->db->from('tb_soal');
        $this->db->where('id_soal', $pc_urut_soal_arr);
        return $this->db->get()->row();
    }

    public function getJawaban($id_tes)
    {
        $this->db->select('list_jawaban');
        $this->db->from('h_ujian');
        $this->db->where('id', $id_tes);
        return $this->db->get()->row()->list_jawaban;
    }

    public function getRealJawaban($dosen_id, $matkul_id)
    {
        $this->db->select('jawaban');
        $this->db->from('tb_soal');
        $this->db->where('dosen_id', $dosen_id);
        $this->db->where('matkul_id', $matkul_id);
        return $this->db->get()->row()->jawaban;
    }

    public function getAllJawaban($id_tes)
    {
        $this->db->select('list_jawaban');
        $this->db->from('h_ujian');
        $this->db->where('id', $id_tes);
        return $this->db->get()->result();
    }


    public function getHasilUjian($nip = null)
    {
        $this->datatables->select('b.id_ujian, b.nama_ujian, b.jumlah_soal, CONCAT(b.waktu, " Minute") as waktu, b.tgl_mulai');
        $this->datatables->select('c.nama_matkul, d.nama_dosen');
        $this->datatables->from('h_ujian a');
        $this->datatables->join('m_ujian b', 'a.ujian_id = b.id_ujian');
        $this->datatables->join('matkul c', 'b.matkul_id = c.id_matkul');
        $this->datatables->join('dosen d', 'b.dosen_id = d.id_dosen');
        $this->datatables->group_by('b.id_ujian');
        if($nip !== null){
            $this->datatables->where('d.nip', $nip);
        }
        return $this->datatables->generate();
    }

    public function HslUjianById($id, $dt=false)
    {
        if($dt===false){
            $db = "db";
            $get = "get";
        }else{
            $db = "datatables";
            $get = "generate";
        }
        
        $this->$db->select('d.id, a.nama, a.nim, b.nama_kelas, c.nama_jurusan, d.jml_benar, d.nilai');
        $this->$db->from('mahasiswa a');
        $this->$db->join('kelas b', 'a.kelas_id=b.id_kelas');
        $this->$db->join('jurusan c', 'b.jurusan_id=c.id_jurusan');
        $this->$db->join('h_ujian d', 'a.id_mahasiswa=d.mahasiswa_id');
        $this->$db->where(['d.ujian_id' => $id]);
        return $this->$db->$get();
    }


    public function bandingNilai($id)
    {
        $this->db->select_min('nilai', 'min_nilai');
        $this->db->select_max('nilai', 'max_nilai');
        $this->db->select_avg('FORMAT(FLOOR(nilai),0)', 'avg_nilai');
        $this->db->where('ujian_id', $id);
        return $this->db->get('h_ujian')->row();
    }

    public function getPesertaKelas($id, $limit, $order, $jml_soal, $dt = false)
    {
        if ($dt === false) {
            $db = "db";
            $get = "get";
        } else {
            $db = "datatables";
            $get = "generate";
        }

        $this->$db->select('d.id, a.nama, a.nim, b.nama_kelas, c.nama_jurusan, d.list_jawaban, d.jml_benar, d.nilai');
        $this->$db->from('mahasiswa a');
        $this->$db->join('kelas b', 'a.kelas_id = b.id_kelas');
        $this->$db->join('jurusan c', 'b.jurusan_id = c.id_jurusan');
        $this->$db->join('h_ujian d', 'a.id_mahasiswa = d.mahasiswa_id');
        $this->$db->where(['d.ujian_id' => $id]);

        // Menetapkan urutan descending berdasarkan kolom 'jml_benar'
        $this->$db->order_by('d.jml_benar', 'desc');

         if ($order == 'bawah' && $limit == 1){
             if ($jml_soal == 3){
                 $this->$db->limit($limit, 2);
             }else if ($jml_soal == 2){
                 $this->$db->limit($limit, 1);
             }else {
                 $this->$db->limit($limit);
             }

        }else if ($order == 'bawah') {

             if ($jml_soal % 2 == 0){
                 // Menghitung offset untuk mengambil 10 data terbawah
                 $offset = max(0, $limit);

                 // Tambahkan limit untuk mengambil 10 data terbawah
                 $this->$db->limit($limit, $offset);
             }else {
                 $offset = max(0, +1);

                 // Tambahkan limit untuk mengambil 10 data terbawah
                 $this->$db->limit($limit, $offset);
             }


        }else {
            $this->$db->limit($limit);
        }

        return $this->$db->$get()->result();
    }

}