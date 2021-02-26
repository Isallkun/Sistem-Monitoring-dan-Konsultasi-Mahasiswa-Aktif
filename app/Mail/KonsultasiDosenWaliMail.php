<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;

use DB;
use Session;

use App\Dosen;
use App\Mahasiswa;
use App\Jadwal_konsultasi;

class KonsultasiDosenWaliMail extends Mailable
{
    use Queueable, SerializesModels;
    public $data_konsultasi;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($data_konsultasi)
    {
        $this->data_konsultasi = $data_konsultasi;
    }
 
    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->from($address = 'noreply@domain.com', $name = 'konsultasi.dosenwali@gmail.com')
        ->subject('Pengumuman Jadwal Konsultasi Dosen Wali')
        ->view('master_notifikasi.konsultasidosenwali_mailformat');
    }
}
 