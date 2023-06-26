<?php

namespace App\Http\Controllers;

use App\Contracts\OccupationParser;

/* Imported from /app/services/OnetMatchUtility.php*/ 
use App\Services\OnetMatchUtility;
/**/
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class OccupationsController extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;

    private $occparser;

    public function __construct(OccupationParser $parser)
    {
        $this->occparser = $parser;
        $this->match_utility = new OnetMatchUtility();
    }

    public function index()
    {
        return $this->occparser->list();
    }

            
    public function compare(Request $request)
    {

        /**
         * This implementation is written as a functional application
         * so the objectives of each line of code can be described in their
         * variable names and not in comments.
         */
        $this->occparser->setScope('skills');
        $occupation_1 = $this->occparser->get($request->get('occupation_1'));
        $occupation_2 = $this->occparser->get($request->get('occupation_2'));
        /**
         * 
         * Return value of the occpaser->get() is
         * 
         * occupations = [['score', 'name', 'description'], ...]
         * 
         * 
         * Turn the array values into maps to make them more work-with-able
         * 
         * In an ideal world would do this in the parser 
         * but doing it here so youre not running around
         * 
         * occupation_map = [
         *      score => skill.score,
         *      description => skill.description
         * ]
         */
        $occupation_one_map = $this->match_utility->occupationMap($occupation_1);
        $occupation_two_map = $this->match_utility->occupationMap($occupation_2);

        /**
         * Create a similarity score for each skill and map to assoc array
         * skill_averages = [
         *      similarity => (skill1.score - skill2.score) / 2,
         *      descriptions -> skill.description
         * ]
         */
        $skill_averages = $this->match_utility->makeSkillAverages($occupation_one_map, $occupation_two_map);
        array_multisort($skill_averages, SORT_DESC);
        /**
         * Get the top 8 skills. Why 8? Thats a great question!
         * 
         * For skills that are at the bottom they aren't as relevant as most 
         * occupations seem to have at least half of all fields score less 
         * than 50, this way we are comparing results that already have 
         * similarities and can get a more realistic number for similarity
         * 
         * using this method most of the time we will get between 50 and 95 for 
         * the score knowing this, on the frontend we provide a message describing 
         * similarity to give the user a more accurate reading on what the number 
         * means
         *  
         * And why 8? Why 8?! WHY 8!!
         */

        $top_eight_skills = array_slice($skill_averages, 0, 8, true);

        /**
         * Get the top 8 and generate an average score based on their values.
         */
        $match = $this->match_utility->makeFinalScore($top_eight_skills);


        return [
            'occupation_1' => $occupation_1,
            'occupation_2' => $occupation_2,
            'matching' => $top_eight_skills,
            'match' => $match
        ];
    }
}
