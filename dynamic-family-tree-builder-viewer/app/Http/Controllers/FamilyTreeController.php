<?php

namespace App\Http\Controllers;

use App\Models\FamilyTreeNode;
use App\Models\FamilyTreeEdge;
use App\Services\TreeFormatterService;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Inertia\Inertia;
use Inertia\Response;

class FamilyTreeController extends Controller
{
    public function __construct(
        private TreeFormatterService $treeFormatter
    ) {}

    public function index(): Response
    {
        $userId = auth()->id();
        $formats = $this->treeFormatter->generateFormats($userId);
        
        return Inertia::render('FamilyTree/Builder', [
            'formats' => $formats,
            'user' => auth()->user(),
        ]);
    }

    public function store(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'relation' => 'required|string|max:255',
            'profile_pic' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'dob' => 'nullable|date',
            'is_alive' => 'boolean',
            'dod' => 'nullable|date|after:dob',
            'biodata' => 'nullable|string',
            'position_x' => 'required|numeric',
            'position_y' => 'required|numeric',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $data = $validator->validated();
        $data['user_id'] = auth()->id();

        if ($request->hasFile('profile_pic')) {
            $path = $request->file('profile_pic')->store('profile-pics', 'public');
            $data['profile_pic'] = $path;
        }

        $node = FamilyTreeNode::create($data);

        return response()->json([
            'message' => 'Family member added successfully',
            'node' => $node->load('sourceEdges', 'targetEdges'),
        ], 201);
    }

    public function show(FamilyTreeNode $node): JsonResponse
    {
        $this->authorize('view', $node);
        
        return response()->json([
            'node' => $node->load('sourceEdges', 'targetEdges'),
        ]);
    }

    public function update(Request $request, FamilyTreeNode $node): JsonResponse
    {
        $this->authorize('update', $node);

        $validator = Validator::make($request->all(), [
            'name' => 'sometimes|string|max:255',
            'relation' => 'sometimes|string|max:255',
            'profile_pic' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'dob' => 'nullable|date',
            'is_alive' => 'sometimes|boolean',
            'dod' => 'nullable|date|after:dob',
            'biodata' => 'nullable|string',
            'position_x' => 'sometimes|numeric',
            'position_y' => 'sometimes|numeric',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $data = $validator->validated();

        if ($request->hasFile('profile_pic')) {
            // Delete old profile picture
            if ($node->profile_pic) {
                Storage::disk('public')->delete($node->profile_pic);
            }
            
            $path = $request->file('profile_pic')->store('profile-pics', 'public');
            $data['profile_pic'] = $path;
        }

        $node->update($data);

        return response()->json([
            'message' => 'Family member updated successfully',
            'node' => $node->fresh()->load('sourceEdges', 'targetEdges'),
        ]);
    }

    public function destroy(FamilyTreeNode $node): JsonResponse
    {
        $this->authorize('delete', $node);

        // Delete profile picture
        if ($node->profile_pic) {
            Storage::disk('public')->delete($node->profile_pic);
        }

        $node->delete();

        return response()->json([
            'message' => 'Family member deleted successfully',
        ]);
    }

    public function storeEdge(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'source_node_id' => 'required|exists:family_tree_nodes,id',
            'target_node_id' => 'required|exists:family_tree_nodes,id|different:source_node_id',
            'relation_type' => 'required|string|max:255',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $data = $validator->validated();

        // Check if both nodes belong to the authenticated user
        $sourceNode = FamilyTreeNode::find($data['source_node_id']);
        $targetNode = FamilyTreeNode::find($data['target_node_id']);

        if ($sourceNode->user_id !== auth()->id() || $targetNode->user_id !== auth()->id()) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        // Check if edge already exists
        $existingEdge = FamilyTreeEdge::where('source_node_id', $data['source_node_id'])
            ->where('target_node_id', $data['target_node_id'])
            ->first();

        if ($existingEdge) {
            return response()->json(['message' => 'Relationship already exists'], 422);
        }

        $edge = FamilyTreeEdge::create($data);

        return response()->json([
            'message' => 'Relationship created successfully',
            'edge' => $edge->load('sourceNode', 'targetNode'),
        ], 201);
    }

    public function destroyEdge(FamilyTreeEdge $edge): JsonResponse
    {
        $sourceNode = $edge->sourceNode;
        if ($sourceNode->user_id !== auth()->id()) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $edge->delete();

        return response()->json([
            'message' => 'Relationship deleted successfully',
        ]);
    }

    public function viewer(int $userId): Response
    {
        $user = \App\Models\User::findOrFail($userId);
        $formats = $this->treeFormatter->generateFormats($userId);
        
        return Inertia::render('FamilyTree/Viewer', [
            'formats' => $formats,
            'user' => $user,
        ]);
    }

    public function getFormats(int $userId): JsonResponse
    {
        $formats = $this->treeFormatter->generateFormats($userId);
        
        return response()->json([
            'formats' => $formats,
        ]);
    }
}
