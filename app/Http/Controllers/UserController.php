<?php


namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use App\Models\University;
use App\Models\Rating;
use App\Models\Comment;
use App\Models\Criteria;
class UserController extends Controller
{
    public function history()
{
    // Vérifier si l'utilisateur actuellement authentifié n'est pas un administrateur
    if (!auth()->user()->hasRole('admin')) {
        $user = Auth::user();
        $ratings = $user->ratings()->with('university', 'criteria')->latest()->get();
        
        // Transmettre les notations à la vue en utilisant la méthode compact()
        return view('Users.history', compact('ratings'));
    } else {
        // Rediriger l'administrateur vers une autre page ou afficher un message d'erreur
        return redirect()->route('dashboard')->with('error', 'Accès interdit.');
    }
}

    public function montrer()
    {
        return view('rien');
    }
    public function __invoke()
{
    if (auth()->check()) {
        // L'utilisateur est authentifié
        if (!auth()->user()->hasRole('admin')) {
            // Passez les données des universités à la vue
            $universities = University::all();
            return view('dashboard', compact('universities'));
        } else {
            // L'utilisateur est un administrateur, redirigez-le vers le tableau de bord de l'administrateur
            return redirect()->route('admindashboard');
        }
    } else {
        // L'utilisateur n'est pas authentifié, redirigez-le vers la page de connexion
        return redirect()->route('login');
    }
}


    public function show(University $university)
    {
        $criteria = Criteria::all();
        return view('Users.université', compact('university','criteria'));
    }
    public function store(Request $request)
    {
        $request->validate([
            'comment' => 'required',
            'university_id' => 'required|exists:universities,id',
        ]);

        Comment::create([
            'user_id' => auth()->id(), // Vous pouvez adapter cela selon votre système d'authentification
            'university_id' => $request->university_id,
            'comment' => $request->comment,
        ]);

        return redirect()->back()->with('success', 'Commentaire ajouté avec succès!');
    }
}
