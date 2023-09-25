<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class ProfilController extends Controller
{
  function show(Request $request){
    $profil = User::find($request->user()->id);
    if (!$profil) {
      return response()->json([
        'status' => false,
        'messages' => 'data user tidak di temukan'
      ]);
    }
    return response()->json([
      'status' => true,
      'messages' => $profil
    ]);
  }
  function edit(Request $request)
  {
    $profil = User::find($request->user()->id);
    if (!$profil) {
      return response()->json([
        'status' => false,
        'messages' => 'data user tidak di temukan'
      ]);
    }
    $validator = Validator::make($request->all(), [
      'name' => ['required'],
      'city' => ['required'],
      'image' => ['image', 'max:1024']
    ], [
      'required' => ':attribute tidak boleh kosong'
    ]);
    if ($validator->fails()) {
      return response()->json([
        'status' => false,
        'messages' => $validator->errors()
      ], 400);
    }
    $path = "";
    if ($request->hasFile('image')) {
      if (isset($profil->image->url)) {
        $path = str_replace($request->getSchemeAndHttpHost() . "/storage/", "public/", $profil->image->url);
        Storage::delete([$path]);
      }
      $path = Storage::putFile('public/files', $request->file('image'));
      $path = str_replace("public/", $request->getSchemeAndHttpHost() . "/storage/", $path);

      $profil->image()->delete();
      $profil->image()->create([
        'url' => $path
      ]);
    }
    $profil ->update(
      $request->only('name','city')
    );
    return response()->json([
      'staus' => true,
      'messages' => 'berhasil mengupdate profil'
    ]);
  }
}
