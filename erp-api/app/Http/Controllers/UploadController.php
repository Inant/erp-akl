<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;

class UploadController extends Controller
{
    public function uploadFile(Request $request) {
        // dd($request);
        $error = false;
        try { 
            $user_id = $request->user_id != null ? $request->user_id : "";

            $filename = $request->file('file')->getClientOriginalName();
            $request->file('file')->move('img/signature/' . $user_id, $filename);
            $path = 'img/signature/' . $user_id . '/' . $filename;

        } catch(Exception $e) {
            $error = true;
        } 
        

        return response()->json([
            'error' => $error, 
            'message'=> !$error ? 'Successfully uploaded' : 'Failed to upload',
            'data' => !$error ? [
                'path' => $path
            ] : []
        
        ]);
    }
}