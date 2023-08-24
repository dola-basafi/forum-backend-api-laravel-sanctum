<?php

namespace App\Http\Controllers;

use App\Models\Question;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class QuestionController extends Controller
{
  function index()
  {
    $question = Question::with('image')->get();
    return response()->json([
      'status' => true,
      'messages' => $question
    ],200);
  }
  function store(Request $request)
  {
    $validator = Validator::make($request->all, [
      'title' => ['required'],
      'body' => ['required'],
      'catergory_id' => ['required'],
      'image' => ['image', 'max:1024'],
    ], [
      'required' => ':attribute tidak boleh kosong',
      'max' => ':attribute tidak boleh lebih dari 1 Megabyte'
    ]);
    if ($validator->fails()) {
      return response()->json([
        'status' => false,
        'messages' => $validator->errors()
      ],400);
    }
    if ($request->hasFile('image')) {
      $path = Storage::putFile('public/files', $request->file('image'));
      $path = str_replace('public/', $request->getSchemeAndHttpHost() . "/storage/", $path);
    }
    $request['user_id'] = $request->user()->id;
    $question = Question::create(
      $request->only('title', 'body', 'category_id', 'user_id')
    );
    $question->image()->create([
      'url' => $path,
    ]);
    return response()->json([
      'status' => true,
      'messages' => 'berhasil membuat pertanyaan'
    ],201);
  }
  function update(Request $request, $id)
  {
    $question = Question::find($id);    
    if (!$question) {
      return response()->json([
        'status' => false,
        'messages' => 'data pertanyaan tidak di temukan'
      ],400);
    }
    if ($question->user_id !== $request->user()->id) {  
      return response()->json([
        'status' => false,
        'messages' => 'anda bukan pemilik dari pertanyaan ini'
      ],403);
    }
    $validator = Validator::make($request->all, [
      'title' => ['required'],
      'body' => ['required'],
      'catergory_id' => ['required'],
      'image' => ['image', 'max:1024'],
    ], [
      'required' => ':attribute tidak boleh kosong',
      'max' => ':attribute tidak boleh lebih dari 1 Megabyte'
    ]);
    if ($validator->fails()) {
      return response()->json([
        'status' => false,
        'messages' => $validator->errors()
      ],400);
    }
    $path="";
    if ($request->hasFile('image')) {
      $path = str_replace($request->getSchemeAndHttpHost()."/storage/","/public/",$question->image->url);
      Storage::disk('public')->delete($path);
      $question->image()->delete();
      $question->image()->create([
        'url' => $path,
      ]);
    }
    $question->update(
      $request->only('title', 'body', 'category_id')
    );
    return response()->json([
      'status' => true,
      'messages' => 'berhasil mengubah pertanyaan'
    ],201);
  }
  function destroy($id, Request $request)
  {
    $question = Question::find($id);
    if (!$question) {
        return response()->json([
          'status' => false,
          'messages' => 'pertanyaan tidak di temukan'
        ],403);
    }
    if ($question->user_id !== $request->user()->id) {  
      return response()->json([
        'status' => false,
        'messages' => 'anda bukan pemilik dari pertanyaan ini'
      ],403);
    }
    if ($question->image->url) {
      $path = str_replace($request->getSchemeAndHttpHost()."/storage/","/public/",$question->image->url);
      Storage::disk('public')->delete($path);
      $question->delete();
    }
    $question->delete();
    return response()->json([
      'status' => true,
      'messages' => 'berhasil menghapus pertanyaan'
    ],201);
  }
  function detail($id)
  {
    $question = Question::find($id);
    if (!$question) {
        return response()->json([
          'status' => false,
          'messages' => 'Pertanyaan tidak di temukan'
        ],403);
    }
    return response()->json([
      'status' => true,
      'messages' => $question
    ],200);
  }
}
