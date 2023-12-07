<?php

namespace App\Http\Controllers;

use App\Helpers\VisionAiHelper;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class VisionAiController extends Controller
{
    public function mark_script(Request $request)
    {
        // return json_decode($test,true);
        $command = <<<PROMPT
        Mark this script 
        As an examiner, mark and grade this assessment taken by a student
        and return your response in json string format below without adding the json markdown syntax.
            {
                "total_score" : "",
                "score_for_each_question" : {},
                "feed_back_for_each_question" : {},
                "each_question_in_the_image" : {},
                "answer_supplied_to_each_question_in_the_image" : {}
            }
        PROMPT;
        
        try{
            $aiHelper = new VisionAiHelper($command);

            $response = $aiHelper->generate_response($request->file('image')->getPathname());
    
            return response()->json([
                'status'=> true,
                'status_code' => '200',
                'data' => $response
            ], 200);
        } 
        catch(\Exception $e){
            $error_message = $e->getMessage();
            return response()->json(['status' => false, 'status_code' => "500", "message" => "Something went wrong: $error_message"] , 500);
        }
    }
}
