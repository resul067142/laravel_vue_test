<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Symfony\Component\Process\Process;
use Symfony\Component\Process\Exception\ProcessFailedException;

class MediaController extends Controller
{
    public function upload(Request $request)
    {
        // Dosyaları kontrol et
        if (!$request->hasFile('files')) {
            return response()->json([
                'error' => 'Dosya bulunamadı. Lütfen bir dosya yükleyin.'
            ], 400);
        }

        $files = $request->file('files');
        $filePaths = [];

        try {
            // Dosyaları kaydet ve yollarını al
            foreach ($files as $file) {
                $path = $file->store('uploads');
                $filePaths[] = storage_path('app/' . $path);
            }

            // Python betiğini çalıştır
            $process = new Process([
                'python3',
                base_path('scripts/analyze_media.py'),
                json_encode($filePaths)
            ]);

            $process->run();

            // Eğer betik çalışmazsa hata döndür
            if (!$process->isSuccessful()) {
                $errorOutput = $process->getErrorOutput();
                \Log::error('Python Script Error:', ['error' => $errorOutput]); // Log hata detayları
                return response()->json([
                    'error' => 'Analiz sırasında hata oluştu.',
                    'details' => $errorOutput
                ], 500);
            }

            $output = json_decode($process->getOutput(), true);

            // Python çıktısını kontrol et
            if (is_null($output) || !is_array($output)) {
                \Log::error('Unexpected Python Output:', ['output' => $process->getOutput()]);
                return response()->json([
                    'error' => 'Python betiği beklenen çıktıyı döndürmedi.',
                    'details' => $process->getOutput()
                ], 500);
            }

            // Başarılı yanıt
            \Log::info('Python Script Successful:', ['output' => $output]);
            return response()->json(['faces' => $output], 200);

        } catch (\Exception $e) {
            // Genel bir hata durumunda
            \Log::error('Exception Occurred:', ['exception' => $e->getMessage()]);
            return response()->json([
                'error' => 'Bir hata oluştu.',
                'details' => $e->getMessage()
            ], 500);
        }
    }
}
