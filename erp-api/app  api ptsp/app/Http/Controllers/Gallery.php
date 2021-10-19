<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use DB;
use Carbon\Carbon;

class Gallery extends Controller
{
    private $dateNow;
    private $now;
    public function __construct()
    {
        $this->dateNow = Carbon::now()->toDateString();
        $this->now = Carbon::now();
    }

    public function getGallery()
    {
        $gallery = DB::select("SELECT u.*, g.*, u.id as user_id from gallery_photos g inner join users u on g.creator = u.id");

        return response()->json(['data'=>$gallery]);
    }
    public function getGalleryById($id)
    {
        $gallery = DB::select("SELECT u.*, g.*, u.id as user_id from gallery_photos g inner join users u on g.creator = u.id where g.id = $id");

        return response()->json(['data'=>$gallery]);
    }
    public function saveGallery(Request $request)
    {
        DB::table('gallery_photos')->insert([
            'filename'       => $request->filename,
            'creator'        => $request->creator,
            'created_at'     => $this->now
        ]);

        return response()->json(
            [
                'responseMessage' => 'success'
            ], 201);
    }

    public function deleteGallery($id)
    {
        DB::table('gallery_photos')->where('id', $id)->delete();

        return response()->json(
            [
                'responseMessage' => 'success hapus '.$id
            ], 200);
    }

    public function updateGallery($id, Request $request)
    {
        // update data category
        DB::table('gallery_photos')->where('id', $id)->update([
            'filename'       => $request->filename,
            'creator'        => $request->creator,
            'updated_at'     => $this->now

        ]);

        return response()->json(
            [
                'responseMessage' => 'success update'
            ], 200);
    }


}
