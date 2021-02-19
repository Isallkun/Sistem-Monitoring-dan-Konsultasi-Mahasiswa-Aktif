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

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct()
    {
    
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->from('konsultasi.dosenwali@gmail.com')
                    ->view('master_notifikasi.notifikasi_mailformat')
                    ->with([
                        'title' => 'nanti akan diisi judul dari DB ...',
                        'date' => 'nanti akan diisi tanggal dari DB ...',
                        'subject' => 'isi'
                    ]);
    }
}
