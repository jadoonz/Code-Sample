<?php

namespace Vteams\Kviz\Controllers;

use Vteams\Kviz\Models\Team;
use Vteams\Kviz\Models\Configuration;
use Vteams\Kviz\Models\Quiz;
use Vteams\Kviz\Models\Category;
use Vteams\Kviz\Models\Option;
use Vteams\Kviz\Models\QuizAnswer;
use Vteams\Kviz\Models\Question;
use Request;
use Redirect;
use View;
use Input;
use Validator;
use Session;

class RegularQuizController extends BaseController {

    /**
     * Shows Home page for selecting teams
     * 
     * For POST data
     * Handles saving of teams in dabase
     * And reidrect to Get question pag
     * 
     */
    public function home() {
        $teams = Team::lists('team_title', 'id');   // Get All teams for dropdown
        return View::make('kviz::home')->with(["teams" => $teams]);
    }

    public function homelogin() {
        if (Request::isMethod('post')) {
            $rules = Quiz::quizRules();  //Get Quiz Rules
            //Validaiton rules
            $validator = Validator::make(Input::all(), $rules); //Apply validation rules
            if ($validator->fails()) {  // If valdiations Fails redirect back to Team Selection
                return Redirect::to('/')
                                ->withErrors($validator)    //validation messages are passed to the form
                                ->withInput();  //form filed value to re po pulate
            } else {
                //Save Quiz in database
                if (Input::get('type') == 'death_match') {
                    Quiz::saveQuiz(Input::get('team_a'), Input::get('team_b'), Input::get('type'), 'in_progress', 0);
                } else {
                    Quiz::saveQuiz(Input::get('team_a'), Input::get('team_b'), Input::get('type'), 'in_progress');
                }
                // redirect
                return Redirect::to("/getQuestion");  //Rediurect to Get Question page
            }
        }
    }

    /**
     * Get Question page Start Quiz
     * 
     */
    public function getQuestion() {
        Session::put('quetion_for_team', Session::get('team_a_id'));
        if (Session::get('quiz_type') != 'death_match') {
            $this->getCategory();
        }


        return View::make('kviz::getQuestion');
    }

    /**
     * Display Questions
     * 
     * @return Load Quiz
     * 
     */
    public function Quiz() {
        $quiz_detail = Quiz::quizTeamsDetail();
        $quiz_detail['question'] = Quiz::quizQuestion();
        return View::make('kviz::quiz')->with($quiz_detail);    // Load Question Page
    }

    /**
     * Handle POST Answer of question
     * 
     */
    public function postAnswer() {

        if (isset($_POST['question_optsions'])) {
            $option_instance = Option::find(Input::get('question_optsions')); // options detail
        } else {  // time expired and no option is selected.
            $option_instance = new Option();
            $option_instance->is_right_option = '0';
        }

        //Save question Answer
        $answer = new QuizAnswer;
        $answer->quiz_id = Session::get('active_quiz_id');
        $answer->team_id = Session::get('quetion_for_team');
        $answer->question_id = Input::get('question_id');
        if (isset($_POST['question_optsions'])) {
            $answer->option_id = Input::get('question_optsions');
        } else {
            $answer->option_id = 0;
        }

        $answer->is_right = $option_instance->is_right_option;
        $answer->score = ($option_instance->is_right_option == 1) ? Configuration::getConfigValue(Configuration::Marks_Per_Correct_Answer) : '-' . Configuration::getConfigValue(Configuration::Marks_Per_Wrong_Answer);

        $answer->save();  // Save Answer

        if (Session::get('quiz_type') != 'death_match') {
            $this->decrementCat($answer->question_id); // Decrement Question count
        }
        //Switch question turn in between teams
        if (Session::get('quetion_for_team') == Session::get('team_a_id')) {
            Session::put('quetion_for_team', Session::get('team_b_id'));
        } elseif (Session::get('quetion_for_team') == Session::get('team_b_id')) {
            Session::put('quetion_for_team', Session::get('team_a_id'));
        }

        //Decrement from total question on Team 
        Session::put('total_question', Session::get('total_question') - 1);

        // check Remaining questions
        if (Session::get('total_question') <= 0) {
            //Quiz is completed

            $team_a = Quiz::find(Session::get('active_quiz_id'))->teamAId;
            $team_b = Quiz::find(Session::get('active_quiz_id'))->teamBId;


            $team_a_score = $team_a->quizAnswersScore(Session::get('active_quiz_id')); // Team A total Score
            $team_b_score = $team_b->quizAnswersScore(Session::get('active_quiz_id')); // Team B total Score              

            $quiz = Quiz::find(Session::get('active_quiz_id'));

            $quiz_draw = False;
            if ($team_a_score > $team_b_score) {
                $quiz->winner_team_id = $team_a->id;
                $quiz->status = 'completed';
                Session::forget('quiz_type');
            } elseif ($team_b_score > $team_a_score) {
                $quiz->winner_team_id = $team_b->id;
                $quiz->status = 'completed';
                Session::forget('quiz_type');
            } else {
                $quiz_draw = TRUE;
                if (Session::get('quiz_type') != 'death_match') {
                    $quiz->status = 'draw';
                }
                //  Session::put('quiz_type', "death_match");
            }

            $quiz->save();  //Update Quiz

            if ($quiz_draw == TRUE) {  //In Case if quiz remain Draw 
                //if(Session::get('quiz_type') == 'death_match' || Configuration::getConfigValue(Configuration::Start_Death_Match)== 1){
                if (Session::get('quiz_type') == 'death_match') {
                    Session::put("death_match_counter", (Session::get("death_match_counter") + 1));
                    Session::put('total_question', 2);
                    return Redirect::to("/quiz");  //Redirect to Quiz page
                    //return Redirect::to("/deathQuiz");
                } else {
                    //do not clear teams ids
                    Session::forget(array('active_quiz_id', 'total_question', 'quetion_for_team', 'cat_a_question','cat_b_question'));
                }
            } else {
                //Clear all session keys set for quiz
                Session::forget(array('active_quiz_id', 'team_a_id', 'team_b_id', 'total_question', 'quetion_for_team', 'cat_a_question','cat_b_question'));
            }
            return Redirect::route('quiz.result', array($quiz->id)); // redirect to result page
        } else {
            //Quiz is not complete
            return Redirect::to("/quiz");  //List Next Question
        }
    }

    /**
     * Shows Result
     * 
     * @param  int  $quiz_id
     * @return Load Result Page
     * 
     */
    public function result($quiz_id) {
        $quiz = Quiz::find($quiz_id);
        if ($quiz != NULL) {
            return View::make('kviz::showResult')->with('quiz_id', $quiz_id);
        } else {
            return "Something went wrong";
        }
    }

    /**
     * Clear Quiz Session
     * 
     */
    public function clearQuiz() {
        Session::forget(array('active_quiz_id', 'team_a_id', 'team_b_id', 'total_question', 'quetion_for_team', 'quiz_type', 'cat_a_question','cat_b_question'));
        return Redirect::route('home'); // redirect to result page
    }

    /*
     * Get category and question count per quiz
     * 
     */

    public function getCategory() {

        $categories = Category::select('id', 'max_question_per_quiz')
                ->where('max_question_per_quiz', '>', '0')
                ->where('id', '!=', session::get('deatch_match_category_id'))
                ->get();
        $cat = '';
        foreach ($categories as $key => $category) {
            $cat_a[$category->id] = $category->max_question_per_quiz;
            $cat_b[$category->id] = $category->max_question_per_quiz;
        }
        Session::put('cat_a_question', $cat_a);
        Session::put('cat_b_question', $cat_b);
    }

    /*
     * Decrement Category Count
     * @param  int  $question_id
     */

    public function decrementCat($question_id) {
        $question_detail = Question::find($question_id);
        $question_cat_id = $question_detail->category_id;
        if (Session::get('quetion_for_team') == Session::get('team_a_id')) {
            Session::put("cat_a_question.$question_cat_id", Session::get("cat_a_question.$question_cat_id") - 1);
            if (Session::get("cat_a_question.$question_cat_id") === 0) {
                Session::forget("cat_a_question.$question_cat_id");
            }
        } elseif (Session::get('quetion_for_team') == Session::get('team_b_id')) {
          
            Session::put("cat_b_question.$question_cat_id", Session::get("cat_b_question.$question_cat_id") - 1);
            if (Session::get("cat_b_question.$question_cat_id") === 0) {
                Session::forget("cat_b_question.$question_cat_id");
            }
        }
    }

}
