<?php

namespace App\Http\Controllers;

use Spatie\PdfToText\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Elastic\Elasticsearch\ClientBuilder;

class StudentController extends Controller
{

    protected $client;

    public function __construct()
    {
        $this->client = ClientBuilder::create()
        ->setElasticCloudId('myapp:dXMtY2VudHJhbDEuZ2NwLmNsb3VkLmVzLmlvJDMwNTU5MjI3ODY5NTQyMjJhZjM3YmE5NGJkMzZkOTQyJGFhMmEyYTIzZDQyODRmMjc5OTM0NmU4OWIyYmQ0ZTZk')
        ->setBasicAuthentication('elastic', 'TaA1QNF4Irx6bYHIRbPHpevn')
        ->build();
    }

    public function index()
    {
        return view('pages.index');
    }

    public function searchIndex()
    {
        return view('pages.search');
    }


    public function search(Request $request)
    {

        $searchText = $request->input('search_text') ?? '';

        // dd($searchText);
        
        if($searchText != ''){
            $params = [
                'index' => 'student_grades',
                'body' => [
                    "query" => [
                        "bool" => [
                            "should" => [
                                [
                                    "match_phrase_prefix" => [
                                        "attachment.content" => [
                                            "query" => $searchText
                                        ]
                                    ]
                                ],
                                [
                                    "match_phrase_prefix" => [
                                        "title" => [
                                            "query" => $searchText
                                        ]
                                    ]
                                ]
                            ]
                        ]
                    ]
                ]

            ];

            $response = $this->client->search($params);
        }else{
            $response = $this->client->search(['index' => 'student_grades']);
        }

        $data = [];

        foreach ($response['hits']['hits'] as $hit) {
            $data[] = [
                '_id' => $hit['_id'],
                'title' => $hit['_source']['title'] ?? '',
                'file_path' => $hit['_source']['file_path'] ?? '',
                'attachment' => $hit['_source']['attachment'] ?? '',
            ];
        }


        return response()->json($data,200);
    }



    public function upload(Request $request)
    {
        $pdfFiles = $request->file('pdf_files');

        $client =  $this->client;

        foreach ($pdfFiles as $pdfFile) {

            $doc = $pdfFile->getClientOriginalName();

            $pdfPath = 'public/files/' . $doc;

            Storage::put($pdfPath, file_get_contents($pdfFile));

            // $pdfContent = base64_encode(Storage::get($pdfPath));
            $pdfContent = base64_encode(file_get_contents($pdfFile));

            $pipeline = $this->createPipeline();

            $params = [
                'index' => 'student_grades',
                'id' => md5($doc),
                'pipeline' => $pipeline,
                'body' => [
                    'pdf_file' => $pdfContent,
                    'title' => $doc,
                    'file_path' => Storage::url($pdfPath),
                ]
            ];


            $client->index($params);
        }

        return response()->json('sucess',200);
    }


    private function createPipeline()
    {
        $pipeID = 'pdf-attachment';

        $this->client->ingest()->putPipeline([
            'id' => $pipeID,
            'body' => [
                'description' => 'Extract attachment information from PDF files',
                'processors' => [
                    [
                        'attachment' => [
                            'field' => 'pdf_file',
                            'indexed_chars' => -1,
                            'ignore_missing' => true,
                        ]
                    ]
                ]
            ]
        ]);


        return $pipeID;
    }


}
