<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Auth;
use Response;
use App\Http\Controllers\Controller;
use App\Admin;
use App\User;
use App\Classes;
use App\Institution;
use App\Subject;
use App\Topic;
use Illuminate\support\Facades\Storage;
class HomeController extends Controller
{
    //
    function __construct(){
        $this->middleware('auth:web');
    }

    public function index(){
        $currentUser = Auth::user();
        $currentInstitution = Institution::where('id', $currentUser->institution_id)->first();
        $userClass = Classes::where('id', $currentUser ->classes_id)->first();
        $userClassSubjects = $userClass->subjects;
     //   dd($currentInstitution);
        return view('User.welcome', compact('currentUser', 'currentInstitution','userClassSubjects','userClass'));
    }

    public function createUserSubject(Request $resquest){
      
        $currentUser = User::where('id', Auth::user()->id)->first();
        $subjectClicked = $resquest->get('subject');
        $currentUser->subjects()->detach();
        if(!empty($subjectClicked)){
            foreach ($subjectClicked  as $key => $value) {
                
                    $userClassSubjects = Subject::where('Subjectname', $value)->first();
                    $currentUser->subjects()->attach($userClassSubjects->id);
                }
                
                
            //    $currentUser->subjects->each(function($subject){
             //       if($subject->pivot->subject_id != $userClassSubjects->id){
                //        $currentUser->subjects()->attach($userClassSubjects->id);
                //    }

             //   });
            
            }
            return redirect()->back();
        
      //  dd($subjectClicked);
    }

    public function displayUserSubjects(Request $resquest){
        $currentUser = User::where('id', Auth::user()->id)->first();
        $userSubjects = $currentUser->subjects;
        
       // dd($userSubjects);
        $currentInstitution = Institution::where('id', $currentUser->institution_id)->first();
        return view('User.subject', compact('currentInstitution','userSubjects'));
    }

    public function getTopic(Request $request)
    {
        $currentUser = User::where('id', Auth::user()->id)->first();
        $currentInstitution = Institution::where('id', $currentUser->institution_id)->first();
        // to get subject topics
        $subjectId = $request->id;
        $currentSubject = Subject::where('id', $subjectId)->first();
        $topics = $currentSubject->topics;
      
        return view('User.topic')->with([
            'topics'=>$topics,
            'currentSubject'=>$currentSubject,
            'currentInstitution'=>$currentInstitution,
        ]);
    }
    
    public function viewTopic(Request $request)
    {
        
        $topic = Topic::where('Title',$request->title)->first();
        $topicTitle = $topic->Title;
        $topicContent = $topic->content;
    /*    if($topic->filename != null){
            $topicFile = $topic->filename;
            $file = Storage::disk('files/'.$topicFile.'/'.$topicFile);
            
        }*/
        return response()->json([
            'topicTitle'=>$topicTitle,
            'topicContent'=>$topicContent, 
        ]);
        
    }
    public function downloadfile(Request $request)
    {
        $topic = Topic::where('Title',$request->title)->first();
        $topicTitle = $topic->Title;
        $topicContent = $topic->content;
        $file = null;
        if($topic->filename != null){
            $topicFile = $topic->filename;
            $file = storage_path('app\files'."\\".$topicFile."\\".$topicFile);
            return response()->download($file);
            
         //   dd($file);
        }
        return response()->json('No file');
    }
}
