<?php

namespace App\Http\Controllers;

use App\Models\Comment;
use Illuminate\Http\Request;

class CommentController extends Controller
{
    public function index()
    {
        $comments = Comment::all(); // Récupère tous les commentaires de la base de données
        return view('Admin.Comments', compact('comments'));
    }
    public function destroy(Comment $comment)
    {
        $comment->delete();
        return redirect()->route('comments.index')->with('success', 'Le commentaire a été supprimé avec succès.');
    }
}
