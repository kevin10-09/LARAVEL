<?php

namespace App\Http\Controllers;

use App\Models\Rating;
use App\Models\University;
use App\Models\Criteria;
use Illuminate\Http\Request;
use App\Models\Comment;
class RatingController extends Controller
{

    public function show(University $university)
{
    $criteria = Criteria::all();
    $comments = Comment::all(); 
    
    return view('Users.université', compact('university', 'criteria','comments'));
}
public function store(Request $request, University $university)
{
    // Validation des données envoyées depuis le formulaire
    $request->validate([
        'ratings' => 'required|array',
        'ratings.*' => 'required|numeric|min:0|max:10',
        'criteria_ids' => 'required|array', // Validation pour les IDs des critères
        'criteria_ids.*' => 'required|exists:criterias,id', // Les IDs des critères doivent exister dans la table des critères
    ]);

    // Réindexer le tableau des identifiants des critères à partir de 1
    $criteriaIds = array_values($request->criteria_ids);

    // Parcours des notes envoyées depuis le formulaire
    foreach ($request->ratings as $index => $score) {
        // Ajouter 1 à l'index pour récupérer l'ID du critère correct
        $correctedIndex = $index + 1;
        
        // Vérifier si l'index corrigé existe dans le tableau des IDs des critères
        if (isset($criteriaIds[$correctedIndex - 1])) {
            // Récupération de l'ID du critère à partir du tableau des IDs des critères
            $criterionIdFromRequest = $criteriaIds[$correctedIndex - 1];

            // Création de la note pour ce critère
            Rating::create([
                'user_id' => auth()->id(),
                'university_id' => $university->id,
                'criteria_id' => $criterionIdFromRequest,
                'score' => $score,
            ]);
        }
    }

    // Redirection vers la route university.show avec le paramètre de l'ID de l'université
    return redirect()->route('university.show', ['university' => $university->id]);
}

public function ranking()
{
    // Récupérer les critères
    $criteria = Criteria::all();

    // Initialiser un tableau pour stocker les résultats
    $rankedUniversitiesByCriteria = [];

    // Pour chaque critère, calculer le score total pour chaque université
    foreach ($criteria as $criterion) {
        $rankedUniversities = University::join('ratings', 'universities.id', '=', 'ratings.university_id')
        ->where('ratings.criteria_id', $criterion->id)
        ->groupBy('universities.id', 'universities.name', 'universities.description', 'universities.location', 'universities.website', 'universities.logo', 'universities.created_at', 'universities.updated_at') // Ajouter universities.updated_at dans GROUP BY
        ->selectRaw('universities.*, AVG(ratings.score) as totalScore')
        ->orderByDesc('totalScore')
        ->get();
    
        // Ajouter les résultats dans le tableau
        $rankedUniversitiesByCriteria[$criterion->name] = $rankedUniversities;
    }

    // Afficher la vue avec les données
    return view('Users.ranking', compact('rankedUniversitiesByCriteria', 'criteria'));
}

}