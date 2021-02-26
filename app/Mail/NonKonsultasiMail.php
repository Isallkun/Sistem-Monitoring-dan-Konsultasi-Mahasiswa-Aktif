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
use App\Non_konsultasi;

class NonKonsultasiMail extends Mailable
{
    use Queueable, SerializesModels;
    public $data;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($data)
    {
        $this->data = $data;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->from($address = 'noreply@domain.com', $name = 'konsultasi.dosenwali@gmail.com')
            ->subject('Informasi Non-Konsultasi Mahasiswa')
            ->view('data_nonkonsultasi.nonkonsultasi_mailformat');


    }
}
 