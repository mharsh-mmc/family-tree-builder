<?php

namespace App\Http\Controllers;

use App\Models\FamilyTreeNode;
use App\Models\FamilyTreeEdge;
use App\Services\TreeFormatterService;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Log;
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
            'biodata' => 'nullable|string|max:1000',
            'position_x' => 'required|numeric',
            'position_y' => 'required|numeric',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $data = $validator->validated();
        $data['user_id'] = auth()->id();

        // Sanitize biodata to prevent XSS
        if (isset($data['biodata'])) {
            $data['biodata'] = $this->sanitizeInput($data['biodata']);
        }

        if ($request->hasFile('profile_pic')) {
            $file = $request->file('profile_pic');
            
            // Enhanced file validation
            if (!$this->validateImageFile($file)) {
                return response()->json(['errors' => ['profile_pic' => ['Invalid image file. Please upload a valid image.']]], 422);
            }

            $path = $this->storeProfilePicture($file);
            if (!$path) {
                return response()->json(['errors' => ['profile_pic' => ['Failed to store image. Please try again.']]], 422);
            }
            
            $data['profile_pic'] = $path;
        }

        $node = FamilyTreeNode::create($data);

        // Log successful creation for audit
        Log::info('Family tree node created', [
            'user_id' => auth()->id(),
            'node_id' => $node->id,
            'name' => $node->name,
            'ip_address' => $request->ip()
        ]);

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
            'biodata' => 'nullable|string|max:1000',
            'position_x' => 'sometimes|numeric',
            'position_y' => 'sometimes|numeric',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $data = $validator->validated();

        // Sanitize biodata to prevent XSS
        if (isset($data['biodata'])) {
            $data['biodata'] = $this->sanitizeInput($data['biodata']);
        }

        if ($request->hasFile('profile_pic')) {
            $file = $request->file('profile_pic');
            
            // Enhanced file validation
            if (!$this->validateImageFile($file)) {
                return response()->json(['errors' => ['profile_pic' => ['Invalid image file. Please upload a valid image.']]], 422);
            }

            // Delete old profile picture
            if ($node->profile_pic) {
                Storage::disk('public')->delete($node->profile_pic);
            }
            
            $path = $this->storeProfilePicture($file);
            if (!$path) {
                return response()->json(['errors' => ['profile_pic' => ['Failed to store image. Please try again.']]], 422);
            }
            
            $data['profile_pic'] = $path;
        }

        $node->update($data);

        // Log successful update for audit
        Log::info('Family tree node updated', [
            'user_id' => auth()->id(),
            'node_id' => $node->id,
            'name' => $node->name,
            'ip_address' => $request->ip()
        ]);

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

        $nodeId = $node->id;
        $nodeName = $node->name;
        
        $node->delete();

        // Log successful deletion for audit
        Log::info('Family tree node deleted', [
            'user_id' => auth()->id(),
            'node_id' => $nodeId,
            'name' => $nodeName,
            'ip_address' => request()->ip()
        ]);

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

        // Log successful edge creation for audit
        Log::info('Family tree edge created', [
            'user_id' => auth()->id(),
            'edge_id' => $edge->id,
            'source_node' => $sourceNode->name,
            'target_node' => $targetNode->name,
            'ip_address' => $request->ip()
        ]);

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

        $edgeId = $edge->id;
        $sourceNodeName = $sourceNode->name;
        $targetNodeName = $edge->targetNode->name;
        
        $edge->delete();

        // Log successful edge deletion for audit
        Log::info('Family tree edge deleted', [
            'user_id' => auth()->id(),
            'edge_id' => $edgeId,
            'source_node' => $sourceNodeName,
            'target_node' => $targetNodeName,
            'ip_address' => request()->ip()
        ]);

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

    /**
     * Sanitize user input to prevent XSS attacks
     */
    private function sanitizeInput(string $input): string
    {
        // Remove potentially dangerous HTML tags and attributes
        $allowedTags = '<p><br><strong><em><u><ol><ul><li>';
        $input = strip_tags($input, $allowedTags);
        
        // Additional sanitization for common XSS vectors
        $input = str_replace(['javascript:', 'vbscript:', 'onload', 'onerror', 'onclick'], '', $input);
        
        return trim($input);
    }

    /**
     * Enhanced image file validation
     */
    private function validateImageFile($file): bool
    {
        try {
            // Check file size
            if ($file->getSize() > 2 * 1024 * 1024) { // 2MB limit
                return false;
            }

            // Check MIME type
            $mimeType = $file->getMimeType();
            $allowedMimes = ['image/jpeg', 'image/png', 'image/jpg', 'image/gif'];
            
            if (!in_array($mimeType, $allowedMimes)) {
                return false;
            }

            // Validate image dimensions
            $imageInfo = getimagesize($file->getPathname());
            if (!$imageInfo) {
                return false;
            }

            // Check for reasonable dimensions (prevent DoS through large images)
            if ($imageInfo[0] > 5000 || $imageInfo[1] > 5000) {
                return false;
            }

            // Additional content validation
            $fileContent = file_get_contents($file->getPathname());
            if (strpos($fileContent, '<?php') !== false || strpos($fileContent, '<?=') !== false) {
                return false; // Prevent PHP code execution
            }

            return true;
        } catch (\Exception $e) {
            Log::warning('Image validation failed', [
                'error' => $e->getMessage(),
                'file' => $file->getClientOriginalName(),
                'user_id' => auth()->id()
            ]);
            return false;
        }
    }

    /**
     * Secure profile picture storage
     */
    private function storeProfilePicture($file): ?string
    {
        try {
            // Generate unique filename to prevent path traversal
            $extension = $file->getClientOriginalExtension();
            $filename = uniqid('profile_', true) . '_' . time() . '.' . $extension;
            
            // Store in secure location
            $path = $file->storeAs('profile-pics', $filename, 'public');
            
            return $path;
        } catch (\Exception $e) {
            Log::error('Failed to store profile picture', [
                'error' => $e->getMessage(),
                'user_id' => auth()->id(),
                'file' => $file->getClientOriginalName()
            ]);
            return null;
        }
    }
}
