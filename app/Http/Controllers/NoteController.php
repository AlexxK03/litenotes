<?php

namespace App\Http\Controllers;

use App\Models\Note;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class NoteController extends Controller
{
    public function index()
    {
        // Fetch notes in order of when they were last updated - latest updated returned first
        $notes = Note::where('user_id', Auth::id())->latest('updated_at')-> paginate(5);
        //  dd($notes);
        return view('notes.index')->with('notes', $notes);
    }

    public function create()    
    {
     return view ('notes.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|max:120',
            'text' => 'required'
        ]);

        Note::create([
            // Ensure you have the use statement for
            // Illuminate\Support\Str at the top of this file.
            'uuid' => Str::uuid(),
            'user_id' => Auth::id(),
            'title' => $request->title,
            'text' => $request->text
        ]);

        return to_route('notes.index');
    }

    public function show(Note $note)
    {
        if($note->user_id != Auth::id()){
            return abort(403);
        }

       return view('notes.show')->with('note', $note);
    }

    public function edit(Note $note)
    {
        if($note->user_id != Auth::id()){
            return abort(403);
        }

       return view('notes.edit')->with('note', $note);
    }

    public function update(Request $request, Note $note)
    {

        if($note->user_id != Auth::id()){
            return abort(403);
        }

        $request->validate([
            'title' => 'required|max:120',
            'text' => 'required'
        ]);

        $note->update([
            'title' =>$request->title,
            'text' =>$request ->text
        ]);

        return to_route('notes.show', $note)->with('success', 'Note Updated successfully');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Note $note)
    {
        if($note->user_id != Auth::id()){
            return abort(403);
        }

        $note->delete();

        return to_route('notes.index');
    }
}
