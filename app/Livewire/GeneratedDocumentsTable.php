<?php

namespace App\Livewire;

use App\Actions\GenerateContractPayloads;
use App\Models\Payload;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Http;
use Livewire\Component;

class GeneratedDocumentsTable extends Component
{
    public Model $record;
    public array $generatedDocuments = [];
    public array $documentSets = [];
    public string $selectedSet = '';

    public function mount(Model $record)
    {
        $this->record = $record;
        app(GenerateContractPayloads::class)->run($record);

        $payloads = Payload::with(['mapping' => function ($query) {
            $query->select('code', 'title', 'category');  // Select only title and category, and 'code' for join
        }])
            ->get(['mapping_code', 'value'])  // Select only necessary columns from the payloads table
            ->map(function ($payload) {
                dd($payload,[$payload->mapping_code=>$payload->value]);
                return [$payload->mapping_code=>$payload->value];
            })->toArray();

        // Fetch document sets from the API
        try {
            $response = Http::post('https://merge.homeful.ph/api/sets');

            if ($response->successful()) {
                $this->documentSets = $response->json() ?? [];
            } else {
                \Log::error('Failed to fetch document sets', [
                    'status' => $response->status(),
                    'response' => $response->body(),
                ]);
            }
        } catch (\Exception $exception) {
            \Log::error('Error fetching document sets', ['error' => $exception->getMessage()]);
            $this->documentSets = [];
        }
    }

    public function fetchDocuments(): void
    {
        $this->generatedDocuments = [];
        // Force Livewire to update UI after each document is added
        $this->dispatch('$refresh');
        try {
            // Fetch templates
            $response = Http::post("https://merge.homeful.ph/api/templates/{$this->selectedSet}");

            if ($response->successful()) {
                $templates = $response->json()['templates'] ?? [];

                foreach ($templates as $template) {
                    $response = Http::timeout(120)
                        ->retry(3, 5000)
                        ->post("https://merge.homeful.ph/api/generate-document/{$template}", [
                            'code' => $this->selectedSet,
                            'data' => [
                                'buyer_name' => 'Renzo',
                            ] ,
                        ]);

                    if ($response->successful()) {
                        // Append each document dynamically
                        array_push($this->generatedDocuments, [
                            "name" => $response->json()['name'],
                            "url" => $response->json()['url'],
                        ]);

                        // Force Livewire to update UI after each document is added
                        $this->dispatch('$refresh');
                    }
                }
            }
        } catch (\Exception $exception) {
            \Log::error('Error fetching generated documents', ['error' => $exception->getMessage()]);
        }
    }

    public function fetchGeneratedDocuments()
    {
        $this->generatedDocuments = [];

        try {

            $response = Http::timeout(120) // Set timeout to 120 seconds
            ->retry(3, 5000) // Retry up to 3 times with a 5-second delay
            ->post("https://merge.homeful.ph/api/folder-documents/{$this->selectedSet}", [
                'code' => $this->selectedSet,
                'data' => [
                    'buyer_name' => 'Renzo',
                ],
            ]);

            if ($response->successful() && isset($response->json()['documents'])) {
                $this->generatedDocuments = $response->json()['documents'];
            } else {
                \Log::warning('Unexpected response when fetching documents', [
                    'status' => $response->status(),
                    'response' => $response->body(),
                ]);
            }
        } catch (\Exception $exception) {
            \Log::error('Error fetching generated documents', ['error' => $exception->getMessage()]);
        }
    }

    public function downloadDocument(string $url)
    {
        try {
            $response = Http::get($url);

            if ($response->successful()) {
                $filename = basename(parse_url($url, PHP_URL_PATH));

                return response()->streamDownload(function () use ($response) {
                    echo $response->body();
                }, $filename);
            } else {
                \Log::error('Failed to download document', [
                    'status' => $response->status(),
                    'url' => $url,
                ]);
                abort(404, 'File not found.');
            }
        } catch (\Exception $exception) {
            \Log::error('Error downloading document', ['error' => $exception->getMessage(), 'url' => $url]);
            abort(404, 'File not found.');
        }
    }

    public function render()
    {
        return view('livewire.generated-documents-table', [
            'generatedDocumentsCount' => count($this->generatedDocuments),
        ]);
    }
}
