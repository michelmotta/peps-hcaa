<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreVideoRequest;
use App\Http\Requests\UpdateVideoRequest;
use App\Models\Video;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class VideoController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'file' => 'required|file|mimes:mp4|max:102400',
        ], [
            'file.required' => 'Por favor, selecione um arquivo de vídeo.',
            'file.mimes' => 'O vídeo deve ser um arquivo do tipo: mp4, webm.',
            'file.max' => 'O vídeo não pode ser maior que 100MB.',
        ]);

        try {
            $video = Video::uploadSingleVideo($request->file('file'));

            return response()->json([
                'success' => true,
                'message' => 'Vídeo enviado com sucesso!',
                'video_id' => $video->id,
                'thumbnail_url' => Storage::url($video->thumbnail_path)
            ]);
        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Falha no envio: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Video $video)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Video $video)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateVideoRequest $request, Video $video)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Video $video)
    {
        try {
            if (Storage::disk('public')->exists($video->path)) {
                Storage::disk('public')->delete($video->path);
            }

            if (Storage::disk('public')->exists($video->thumbnail_path)) {
                Storage::disk('public')->delete($video->thumbnail_path);
            }

            $video->delete();

            return response()->json([
                'success' => true,
                'message' => 'Vídeo removido com sucesso.'
            ]);
        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erro ao remover o vídeo: ' . $e->getMessage()
            ], 500);
        }
    }
}
