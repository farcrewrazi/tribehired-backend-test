<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use \GuzzleHttp\Client;

class TestRESTController extends Controller
{
    public function TopPosts (){
        $client = new Client();

        $guzReq = $client->get('https://jsonplaceholder.typicode.com/posts');
        $postData = json_decode($guzReq->getBody());

        $guzReq = $client->get('https://jsonplaceholder.typicode.com/comments');
        $commentData = json_decode($guzReq->getBody());

        $newPostData = [];
        foreach ($postData as $post) {
            $newCommentData = array_filter($commentData, function($comment) use ($post){
                return $comment->postId == $post->id;
            });
            
            array_push($newPostData, array(
                'post_id' => $post->id,
                'post_title' => $post->title,
                'post_body' => $post->body,
                'total_number_of_comments' => sizeof($newCommentData)
            ));
        }

        usort($newPostData, function($itemA, $itemB){
            return $itemB['total_number_of_comments'] <=> $itemA['total_number_of_comments'];
        });

        return response()->json(array( 'Top Post' => $newPostData));
    }

    public function searchComments (Request $request){
        $postIdField = isset($request->postIdField) ? $request->postIdField : null;
        $idField = isset($request->idField) ? $request->idField : null;
        $nameField = isset($request->nameField) ? $request->nameField : null;
        $emailField = isset($request->emailField) ? $request->emailField : null;
        $bodyField = isset($request->bodyField) ? $request->bodyField : null;

        $client = new Client();
        $guzReq = $client->get('https://jsonplaceholder.typicode.com/comments');
        $commentData = json_decode($guzReq->getBody());

        if($postIdField){
            $commentData = array_filter($commentData, function($comment) use ($postIdField){
                return $comment->postId == $postIdField;
            });
        }

        if($idField){
            $commentData = array_filter($commentData, function($comment) use ($idField){
                return $comment->id == $idField;
            });
        }

        if($nameField){
            $commentData = array_filter($commentData, function($comment) use ($nameField){
                return str_contains($comment->name, $nameField);
            });
        }

        if($emailField){
            $commentData = array_filter($commentData, function($comment) use ($emailField){
                return str_contains($comment->email, $emailField);
            });
        }

        if($bodyField){
            $commentData = array_filter($commentData, function($comment) use ($bodyField){
                return str_contains($comment->body, $bodyField);
            });
        }
        
        $commentData = array_values($commentData);

        return response()->json(array('Comments' => $commentData));
    }
}
