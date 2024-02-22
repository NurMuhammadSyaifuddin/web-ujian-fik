<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Soal_model extends CI_Model {
    
        public function getDataSoal($nip, $id)
    {
        $this->datatables->select('a.id_soal, a.soal, FROM_UNIXTIME(a.created_on) as created_on, FROM_UNIXTIME(a.updated_on) as updated_on, b.nama_matkul, c.nama_dosen');
        $this->datatables->from('tb_soal a');
        $this->datatables->join('matkul b', 'b.id_matkul=a.matkul_id');
        $this->datatables->join('dosen c', 'c.id_dosen=a.dosen_id');
        $this->datatables->where('c.nip', $nip);
        if ($id!==null) {
            $this->datatables->where('a.matkul_id', $id);
        }
        return $this->datatables->generate();
    }

    public function getDataSoalUjian($nip, $id)
    {
        $this->db->select('a.soal, a.matkul_id, b.nama_matkul, a.id_soal');
        $this->db->from('tb_soal a');
        $this->db->join('matkul b', 'b.id_matkul=a.matkul_id');
        $this->db->join('dosen c', 'c.id_dosen=a.dosen_id');
        $this->db->where('c.nip', $nip);
//        if ($id !== null) {
//            $this->db->where('a.matkul_id', $id);
//        }
        return $this->db->get()->result();
    }

    public function getDataSoalAdmin($id)
    {
        $this->datatables->select('a.id_soal, a.soal, FROM_UNIXTIME(a.created_on) as created_on, FROM_UNIXTIME(a.updated_on) as updated_on, b.nama_matkul, c.nama_dosen');
        $this->datatables->from('tb_soal a');
        $this->datatables->join('matkul b', 'b.id_matkul=a.matkul_id');
        $this->datatables->join('dosen c', 'c.id_dosen=a.dosen_id');
        if ($id!==null) {
            $this->datatables->where('a.matkul_id', $id);
        }
        return $this->datatables->generate();
    }

    public function getSoalById($id)
    {
        return $this->db->get_where('tb_soal', ['id_soal' => $id])->row();
    }

    public function getMatkulDosen($nip)
    {
        $this->db->select('a.matkul_id, b.nama_matkul, c.id_dosen, c.nama_dosen');
        $this->db->join('dosen c', 'c.id_dosen=a.dosen_id');
        $this->db->join('matkul b', 'a.matkul_id=b.id_matkul');
        $this->db->from('dosen_matkul a')->where('c.nip', $nip);
        return $this->db->get()->result();
    }

    public function getMatkulDosenSoal($nip)
    {
        $this->db->select('a.matkul_id, b.nama_matkul, c.id_dosen, c.nama_dosen');
        $this->db->join('dosen c', 'c.id_dosen=a.dosen_id');
        $this->db->join('matkul b', 'a.matkul_id=b.id_matkul');
        $this->db->from('dosen_matkul a')->where('c.nip', $nip);
        return $this->db->get()->row();
    }

    public function getMatkulDosenById($nip){
        $this->db->select('matkul.id_matkul, matkul.nama_matkul, dosen_matkul.matkul_id');
        $this->db->join('dosen_matkul', 'dosen_matkul.dosen_id=dosen.id_dosen');
        $this->db->join('matkul', 'dosen_matkul.matkul_id=matkul.id_matkul');
        $this->db->from('dosen')->where('dosen.nip', $nip);
        $query = $this->db->get()->result();
        return $query;
    }


    public function getAllDosen()
    {
        $this->db->select('*');
        $this->db->from('dosen a');
        $this->db->join('dosen_matkul c', 'c.dosen_id=a.id_dosen');
        $this->db->join('matkul b', 'c.matkul_id=b.id_matkul');
        return $this->db->get()->result();
    }
}