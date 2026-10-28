<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class FeedbackController extends Controller
{
    private string $storageDir;

    public function __construct()
    {
        $this->storageDir = storage_path('app/feedback');
    }

    public function create()
    {
        return view('pages.form');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|min:2|max:100',
            'email' => 'required|email|max:150',
            'subject' => 'required|string|min:3|max:200',
            'message' => 'required|string|min:5|max:2000',
        ]);

        if (!file_exists($this->storageDir)) {
            mkdir($this->storageDir, 0755, true);
        }

        $record = array_merge($validated, [
            'id' => uniqid(),
            'created_at' => now()->toDateTimeString(),
        ]);

        $fileName = 'record_' . now()->format('Ymd_His') . '_' . uniqid() . '.json';
        file_put_contents(
            $this->storageDir . DIRECTORY_SEPARATOR . $fileName,
            json_encode($record, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE)
        );

        return redirect()->route('form.show')->with('success', 'Данные успешно сохранены в файл ' . $fileName);
    }

    public function index()
    {
        $records = [];

        if (file_exists($this->storageDir)) {
            $files = glob($this->storageDir . DIRECTORY_SEPARATOR . '*.json');
            rsort($files);

            foreach ($files as $file) {
                $content = file_get_contents($file);
                $decoded = json_decode($content, true);
                if (is_array($decoded)) {
                    $decoded['_file'] = basename($file);
                    $records[] = $decoded;
                }
            }
        }

        return view('pages.records', compact('records'));
    }
}
