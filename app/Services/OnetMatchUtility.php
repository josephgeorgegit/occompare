<?php

namespace App\Services;

class OnetMatchUtility {

    public function occupationMap($occupation){
        
        /**
         * Making a map because working with array indexes through this whole
         * process will be confusing. This makes the code more descriptive
         * and readable.
         */
        $res = [];
        foreach($occupation as $skill){
            $key = $skill[1];
            $res[$key] = [
                "score" => $skill[0],
                "description" => $skill[2]
            ];
        };
        return $res;
        
    }

    public function makeSkillAverages($occupation_1, $occupation_2){
        $averages = [];
        /**
         * Occupation 1 and 2 have all the same fields but with different scores
         * so we only need to iterate one of them.
         * 
         * we find the difference between them. if it works out to a negative int
         * we multiply by -1 to get the positive result.
         */
        foreach($occupation_1 as $skill => $value){

            $skill_similarity = $value['score'] - $occupation_2[$skill]['score'];

            if($skill_similarity < 0){
                $skill_similarity = $skill_similarity * -1;
            }
            $averages[$skill] = [
                'similarity' => $skill_similarity,
                "description" => $value['description']
            ];
        }
        return $averages;
    }

    public function makeFinalScore($skills_averages){

        $skills_length = count($skills_averages);
        $total_sum = 0;
        /**
         * We create the sum of all similarity scores. When 2 occupations
         * are very similar, we can know that this sum will be low.
         */
        foreach($skills_averages as $skill){
            $total_sum += intval($skill['similarity']);
        }

        /**
         * We want to display the result as a percentage to the end user,
         * so we take all the sum of the differences and divide it by the
         * length of the skills_averages array we passed in.
         * 
         * Once this algorithm is set, skills_length can become a constant,
         * but in the case we are tuning the algorithm a variable works
         * for building purposes.
         */
        
        $final_score = 100 - ($total_sum / $skills_length);

        /**
         * In the rare event final score is negative we return 0.
         */
        if($final_score < 0){
            $final_score = 0;
        }
        return $final_score;
    }
}