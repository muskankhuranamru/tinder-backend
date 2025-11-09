<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Person;
use App\Models\Like;
use App\Models\Dislike;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

/**
 * @OA\Info(
 *     title="Tinder Backend API",
 *     version="1.0.0",
 *     description="API for Tinder-like app with like/dislike functionality"
 * )
 * @OA\Server(
 *     url="/api/v1",
 *     description="API Server"
 * )
 */
class PersonController extends Controller
{
    /**
     * @OA\Get(
     *     path="/people/recommended",
     *     summary="Get list of recommended people",
     *     tags={"People"},
     *     @OA\Parameter(
     *         name="page",
     *         in="query",
     *         description="Page number",
     *         required=false,
     *         @OA\Schema(type="integer", default=1)
     *     ),
     *     @OA\Parameter(
     *         name="per_page",
     *         in="query",
     *         description="Items per page",
     *         required=false,
     *         @OA\Schema(type="integer", default=10)
     *     ),
     *     @OA\Parameter(
     *         name="user_id",
     *         in="query",
     *         description="User ID to exclude already liked/disliked people",
     *         required=false,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Successful operation",
     *         @OA\JsonContent(
     *             @OA\Property(property="current_page", type="integer"),
     *             @OA\Property(property="data", type="array", @OA\Items(
     *                 @OA\Property(property="id", type="integer"),
     *                 @OA\Property(property="name", type="string"),
     *                 @OA\Property(property="age", type="integer"),
     *                 @OA\Property(property="pictures", type="array", @OA\Items(type="string")),
     *                 @OA\Property(property="location", type="string")
     *             )),
     *             @OA\Property(property="per_page", type="integer"),
     *             @OA\Property(property="total", type="integer")
     *         )
     *     )
     * )
     */
    public function recommended(Request $request): JsonResponse
    {
        $perPage = $request->get('per_page', 10);
        $userId = $request->get('user_id');
        
        $query = Person::query();
        
        // Exclude people already liked or disliked by this user
        if ($userId) {
            $likedIds = Like::where('user_id', $userId)->pluck('person_id');
            $dislikedIds = Dislike::where('user_id', $userId)->pluck('person_id');
            $excludedIds = $likedIds->merge($dislikedIds);
            
            if ($excludedIds->isNotEmpty()) {
                $query->whereNotIn('id', $excludedIds);
            }
        }
        
        $people = $query->orderBy('created_at', 'desc')->paginate($perPage);
        
        return response()->json($people);
    }

    /**
     * @OA\Post(
     *     path="/people/{person}/like",
     *     summary="Like a person",
     *     tags={"People"},
     *     @OA\Parameter(
     *         name="person",
     *         in="path",
     *         description="Person ID",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             @OA\Property(property="user_id", type="integer", example=1)
     *         )
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Person liked successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string"),
     *             @OA\Property(property="like", type="object")
     *         )
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="Already liked or validation error"
     *     )
     * )
     */
    public function like(Request $request, Person $person): JsonResponse
    {
        $request->validate(['user_id' => 'required|exists:users,id']);
        
        $userId = $request->user_id;
        
        // Check if already liked
        $existingLike = Like::where('user_id', $userId)
            ->where('person_id', $person->id)
            ->first();
            
        if ($existingLike) {
            return response()->json(['message' => 'You have already liked this person'], 400);
        }
        
        // Create like record
        $like = Like::create(['user_id' => $userId, 'person_id' => $person->id]);
        
        // Increment like count for email notification threshold
        $person->increment('like_count');
        
        // Remove any existing dislike (user changed their mind)
        Dislike::where('user_id', $userId)->where('person_id', $person->id)->delete();
        
        return response()->json(['message' => 'Person liked successfully', 'like' => $like], 201);
    }

    /**
     * @OA\Post(
     *     path="/people/{person}/dislike",
     *     summary="Dislike a person",
     *     tags={"People"},
     *     @OA\Parameter(
     *         name="person",
     *         in="path",
     *         description="Person ID",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             @OA\Property(property="user_id", type="integer", example=1)
     *         )
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Person disliked successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string"),
     *             @OA\Property(property="dislike", type="object")
     *         )
     *     )
     * )
     */
    public function dislike(Request $request, Person $person): JsonResponse
    {
        $request->validate(['user_id' => 'required|exists:users,id']);
        
        $userId = $request->user_id;
        
        // Check if already disliked
        $existingDislike = Dislike::where('user_id', $userId)
            ->where('person_id', $person->id)
            ->first();
            
        if ($existingDislike) {
            return response()->json(['message' => 'You have already disliked this person'], 400);
        }
        
        // Create dislike record
        $dislike = Dislike::create(['user_id' => $userId, 'person_id' => $person->id]);
        
        // Remove any existing like (user changed their mind)
        $like = Like::where('user_id', $userId)->where('person_id', $person->id)->first();
        if ($like) {
            $person->decrement('like_count');
            $like->delete();
        }
        
        return response()->json(['message' => 'Person disliked successfully', 'dislike' => $dislike], 201);
    }

    /**
     * @OA\Get(
     *     path="/people/liked",
     *     summary="Get list of liked people",
     *     tags={"People"},
     *     @OA\Parameter(
     *         name="user_id",
     *         in="query",
     *         description="User ID",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Successful operation",
     *         @OA\JsonContent(
     *             @OA\Property(property="data", type="array", @OA\Items(
     *                 @OA\Property(property="id", type="integer"),
     *                 @OA\Property(property="name", type="string"),
     *                 @OA\Property(property="age", type="integer"),
     *                 @OA\Property(property="pictures", type="array", @OA\Items(type="string")),
     *                 @OA\Property(property="location", type="string"),
     *                 @OA\Property(property="liked_at", type="string", format="date-time")
     *             ))
     *         )
     *     )
     * )
     */
    public function likedPeople(Request $request): JsonResponse
    {
        $request->validate(['user_id' => 'required|exists:users,id']);
        
        $userId = $request->user_id;
        
        // Get all people liked by this user with timestamps
        $likedPeople = Person::join('likes', 'people.id', '=', 'likes.person_id')
            ->where('likes.user_id', $userId)
            ->select('people.*', 'likes.created_at as liked_at')
            ->orderBy('likes.created_at', 'desc')
            ->get();
        
        return response()->json(['data' => $likedPeople]);
    }
}
