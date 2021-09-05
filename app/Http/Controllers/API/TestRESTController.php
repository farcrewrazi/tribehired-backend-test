<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use \GuzzleHttp\Client;

class TestRESTController extends Controller
{
    public function TopPosts (){
        // fetch data from API
        $client = new Client();
        $guzReq = $client->get('https://jsonplaceholder.typicode.com/posts');
        $guzReq = $client->get('https://jsonplaceholder.typicode.com/comments');
        $postData = json_decode($guzReq->getBody());
        $commentData = json_decode($guzReq->getBody());

        $newPostData = [];
        foreach ($postData as $post) {
            $newCommentData = array_filter($commentData, function($comment) use ($post){
                return $comment->postId == $post->id;
            });
            
            // restructure new post data
            array_push($newPostData, array(
                'post_id' => $post->id,
                'post_title' => $post->title,
                'post_body' => $post->body,
                'total_number_of_comments' => sizeof($newCommentData)
            ));
        }

        // sort post data
        usort($newPostData, function($itemA, $itemB){
            return $itemB['total_number_of_comments'] <=> $itemA['total_number_of_comments'];
        });

        return response()->json(array( 'Top Post' => $newPostData));
    }

    public function searchComments (Request $request){
        $data2send = array('msg' => '1221212');
        return response()->json($data2send);
    }
}
