<?php

namespace App\Http\Controllers;

use App\Models\Answer;
use App\Models\Question;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class AnswerController extends Controller
{
  function index($id)
  {
    $answer = Answer::with('image', 'user:id,name')->where('question_id', '=', $id)->get();
    return response()->json([
      'status' => true,
      'messages' => $answer
    ], 200);
  }
  function store(Request $request)
  {
    if (!(Question::find($request['question_id']))) {
      return response()->json([
        'status' => false,
        'messages' => 'data pertanyaan  tidak di temukan'
      ]);
    }
    $validator = Validator::make($request->all(), [
      'body' => ['required'],
      'question_id' => ['required'],
      'image' => ['image', 'max:1024']
    ], [
      'required' => ':attribute tidak boleh kosong',
    ]);
    if ($validator->fails()) {
      return response()->json([
        'status' => false,
        'messages' => $validator->errors()
      ], 400);
    }
    $path = "";
    if ($request->hasFile('image')) {
      $path = Storage::putFile('public/files', $request->file('image'));
      $path = str_replace('public/', $request->getSchemeAndHttpHost() . "/storage/", $path);
    }
    $request['user_id'] = $request->user()->id;
    $answer = Answer::create(
      $request->only('body', 'question_id', 'user_id')
    );
    $answer->image()->create([
      'url' => $path
    ]);
    return response()->json([
      'status' => true,
      'messages' => 'berhasil mengirim jawaban'
    ], 201);
  }
  function update(Request $request, $id)
  {
    $answer = Answer::find($id);
    if (!$answer) {
      return response()->json([
        'status' => false,
        'messages' => 'data jawaban tidak di temukan'
      ], 400);
    }
    if ($answer->user_id !== $request->user()->id) {
      return response()->json([
        'status' => false,
        'messages' => 'anda bukan pemilik dari pertanyaan ini'
      ], 403);
    }
    $validator = Validator::make($request->all(), [
      'body' => ['required'],
      'image' => ['image', 'max:1024']
    ], [
      'required' => ':attribute tidak boleh kosong',
    ]);
    if ($validator->fails()) {
      return response()->json([
        'status' => false,
        'messages' => $validator->errors()
      ], 400);
    }
    $path = "";
    if ($request->hasFile('image')) {
      if (isset($answer->image->url)) {
        $path = str_replace($request->getSchemeAndHttpHost() . "/storage/", "public/", $answer->image->url);
        Storage::delete([$path]);
      }
      $path = Storage::putFile('public/files', $request->file('image'));
      $path = str_replace("public/", $request->getSchemeAndHttpHost() . "/storage/", $path);
      $answer->image()->delete();
      $answer->image()->create([
        'url' => $path
      ]);
    }
    $answer->update(
      $request->only('body')
    );
    return response()->json([
      'status' => true,
      'messages' => 'berhasil mengubah jawaban'
    ]);
  }
  function detail($id)
  {
    $answer = Answer::with('image')->find($id);
    if (!$answer) {
      return response()->json([
        'status' => false,
        'messages' => 'data jawaban yang anda cari tidak di temukan'
      ]);
    }
    return response()->json([
      'status' => true,
      'messages' => $answer
    ], 200);
  }
  function destroy(Request $request, $id)
  {
    $answer = Answer::find($id);
    if (!$answer) {
      return response()->json([
        'status' => false,
        'messages' => ' data jawaban yang anda cari tidak di temukan'
      ], 400);
    }
    if ($answer->user_id !== $request->user()->id) {
      return response()->json([
        'status' => false,
        'messages' => 'anda bukan pemilik dari jawaban ini'
      ], 403);
    }
    if (isset($answer->image->url)) {
      $path = str_replace($request->getSchemeAndHttpHost() . "/storage/", "public/", $answer->image->url);
      Storage::delete([$path]);
      $answer->delete();
    }
    $answer->delete();
    return response()->json([
      'status' => true,
      'messages' => 'berhasil menghapus jawaban'
    ]);
  }
}
