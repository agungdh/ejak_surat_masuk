<?php

namespace App\Console\Commands;

use App\Models\Bidang;
use App\Models\PangkatGolongan;
use App\Models\Pegawai;
use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Str;
use PhpOffice\PhpSpreadsheet\IOFactory;

class ImportDataPegawai extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:import-data-pegawai';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     */
    public function handle()
    {

        // Path ke file ODS Anda
        $inputFileName = base_path().'/dev/pegawai bpkp.ods';

        // Buat reader untuk format ODS
        $reader = IOFactory::createReader('Ods');
        // (Opsional) Nonaktifkan read data formatting untuk nilai mentah
        // $reader->setReadDataOnly(true);

        // Load spreadsheet
        $spreadsheet = $reader->load($inputFileName);

        // Ambil sheet aktif (atau gunakan getSheetByName / getSheet($index))
        $sheet = $spreadsheet->getActiveSheet();

        // Konversi sheet jadi array (baris × kolom)
        $data = $sheet->toArray(null, true, true, true);

        // Ambil header
        $header = array_shift($data);

        foreach ($data as $row) {
            $nip = Str::replace(' ', '', $row['C']);

            $user = new User;
            $user->username = $nip;
            $user->password = $nip;
            $user->save();

            $pegawai = new Pegawai;
            $pegawai->user_id = $user->id;
            $pegawai->nip = $nip;
            $pegawai->nama = $row['B'];
            $pangkat = PangkatGolongan::where('pangkat', $row['D'])->first();
            $pegawai->pangkat_golongan_id = $pangkat?->id;
            $pegawai->jabatan = $row['F'];
            $pegawai->peran = $row['G'];

            $bidang = Bidang::where('bidang', $row['I'])->firstOrFail();
            $pegawai->bidang_id = $bidang->id;

            $pegawai->save();

            if ($pegawai->bidang->bidang == 'SUBBAGIAN KEPEGAWAIAN') {
                $pegawai->tipe = 'admin';
                $user->assignRole('admin');
            } else {
                $pegawai->tipe = 'pegawai';
                $user->assignRole('pegawai');
            }

            $pegawai->save();
        }
    }
}
