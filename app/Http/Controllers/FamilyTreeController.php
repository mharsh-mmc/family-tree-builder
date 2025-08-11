<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreFamilyMemberRequest;
use App\Http\Requests\UpdateFamilyMemberRequest;
use App\Models\FamilyMember;
use App\Models\FamilyConnection;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Illuminate\Support\Facades\DB;

class FamilyTreeController extends Controller
{
    /**
     * Display the family tree.
     */
    public function index()
    {
        $nodes = FamilyMember::all();
        $edges = FamilyConnection::all();
        
        // Calculate statistics
        $stats = [
            'totalMembers' => $nodes->count(),
            'livingMembers' => $nodes->where('is_alive', true)->count(),
            'totalConnections' => $edges->count(),
            'generations' => $this->calculateGenerations($nodes, $edges),
        ];
        
        return Inertia::render('FamilyTree/Index', [
            'nodes' => $nodes,
            'edges' => $edges,
            'stats' => $stats,
        ]);
    }

    /**
     * Store a newly created family member.
     */
    public function storeMember(StoreFamilyMemberRequest $request)
    {
        $validated = $request->validated();
        
        $member = FamilyMember::create($validated);
        
        return redirect()->back()->with('success', 'Family member added successfully!');
    }

    /**
     * Update the specified family member.
     */
    public function updateMember(UpdateFamilyMemberRequest $request, FamilyMember $member)
    {
        $validated = $request->validated();
        
        $member->update($validated);
        
        return redirect()->back()->with('success', 'Family member updated successfully!');
    }

    /**
     * Remove the specified family member.
     */
    public function destroyMember(FamilyMember $member)
    {
        // Delete all connections first
        FamilyConnection::where('source_node_id', $member->id)
            ->orWhere('target_node_id', $member->id)
            ->delete();
        
        $member->delete();
        
        return redirect()->back()->with('success', 'Family member deleted successfully!');
    }

    /**
     * Store a newly created family connection.
     */
    public function storeConnection(Request $request)
    {
        $validated = $request->validate([
            'source' => 'required|exists:family_members,id',
            'target' => 'required|exists:family_members,id|different:source',
            'relation_type' => 'nullable|string|max:255',
            'description' => 'nullable|string|max:1000',
        ]);
        
        // Check for duplicate connections
        $existingConnection = FamilyConnection::where(function ($query) use ($validated) {
            $query->where('source_node_id', $validated['source'])
                  ->where('target_node_id', $validated['target']);
        })->orWhere(function ($query) use ($validated) {
            $query->where('source_node_id', $validated['target'])
                  ->where('target_node_id', $validated['source']);
        })->first();
        
        if ($existingConnection) {
            return redirect()->back()->withErrors(['connection' => 'This connection already exists.']);
        }
        
        $connection = FamilyConnection::create([
            'source_node_id' => $validated['source'],
            'target_node_id' => $validated['target'],
            'relation_type' => $validated['relation_type'],
            'description' => $validated['description'],
        ]);
        
        return redirect()->back()->with('success', 'Family connection added successfully!');
    }

    /**
     * Remove the specified family connection.
     */
    public function destroyConnection(FamilyConnection $connection)
    {
        $connection->delete();
        
        return redirect()->back()->with('success', 'Family connection deleted successfully!');
    }

    /**
     * Update member positions.
     */
    public function updatePositions(Request $request)
    {
        $validated = $request->validate([
            'positions' => 'required|array',
            'positions.*.id' => 'required|exists:family_members,id',
            'positions.*.position_x' => 'required|numeric',
            'positions.*.position_y' => 'required|numeric',
        ]);
        
        foreach ($validated['positions'] as $position) {
            FamilyMember::where('id', $position['id'])->update([
                'position_x' => $position['position_x'],
                'position_y' => $position['position_y'],
            ]);
        }
        
        return response()->json(['message' => 'Positions updated successfully']);
    }

    /**
     * Export family tree data.
     */
    public function export()
    {
        $nodes = FamilyMember::all();
        $edges = FamilyConnection::all();
        
        $data = [
            'nodes' => $nodes,
            'edges' => $edges,
            'exported_at' => now()->toISOString(),
        ];
        
        return response()->json($data);
    }

    /**
     * Import family tree data.
     */
    public function import(Request $request)
    {
        $validated = $request->validate([
            'tree_data' => 'required|string',
        ]);
        
        try {
            $data = json_decode($validated['tree_data'], true);
            
            if (!$data || !isset($data['nodes']) || !isset($data['edges'])) {
                throw new \Exception('Invalid tree data format');
            }
            
            DB::transaction(function () use ($data) {
                // Clear existing data
                FamilyConnection::truncate();
                FamilyMember::truncate();
                
                // Import nodes
                foreach ($data['nodes'] as $node) {
                    FamilyMember::create([
                        'name' => $node['name'] ?? '',
                        'relation' => $node['relation'] ?? '',
                        'dob' => $node['dob'] ?? null,
                        'dod' => $node['dod'] ?? null,
                        'is_alive' => $node['is_alive'] ?? true,
                        'biodata' => $node['biodata'] ?? '',
                        'profile_pic' => $node['profile_pic'] ?? null,
                        'position_x' => $node['position_x'] ?? 0,
                        'position_y' => $node['position_y'] ?? 0,
                    ]);
                }
                
                // Import edges
                foreach ($data['edges'] as $edge) {
                    FamilyConnection::create([
                        'source_node_id' => $edge['source_node_id'] ?? $edge['source'],
                        'target_node_id' => $edge['target_node_id'] ?? $edge['target'],
                        'relation_type' => $edge['relation_type'] ?? '',
                        'description' => $edge['description'] ?? '',
                    ]);
                }
            });
            
            return redirect()->back()->with('success', 'Family tree imported successfully!');
            
        } catch (\Exception $e) {
            return redirect()->back()->withErrors(['import' => 'Failed to import tree data: ' . $e->getMessage()]);
        }
    }

    /**
     * Reset the entire family tree.
     */
    public function reset()
    {
        DB::transaction(function () {
            FamilyConnection::truncate();
            FamilyMember::truncate();
        });
        
        return redirect()->back()->with('success', 'Family tree has been reset successfully!');
    }

    /**
     * Calculate the number of generations in the family tree.
     */
    private function calculateGenerations($nodes, $edges)
    {
        if ($nodes->isEmpty()) {
            return 0;
        }
        
        // Simple calculation based on the maximum depth
        // This is a basic implementation and could be improved
        $maxDepth = 1;
        
        // Find root nodes (nodes with no incoming connections)
        $rootNodes = $nodes->filter(function ($node) use ($edges) {
            return !$edges->contains('target_node_id', $node->id);
        });
        
        if ($rootNodes->isEmpty()) {
            return 1;
        }
        
        // Calculate depth for each root node
        foreach ($rootNodes as $rootNode) {
            $depth = $this->calculateNodeDepth($rootNode->id, $edges, $nodes);
            $maxDepth = max($maxDepth, $depth);
        }
        
        return $maxDepth;
    }

    /**
     * Calculate the depth of a specific node.
     */
    private function calculateNodeDepth($nodeId, $edges, $nodes, $visited = [])
    {
        if (in_array($nodeId, $visited)) {
            return 0; // Prevent cycles
        }
        
        $visited[] = $nodeId;
        
        $children = $edges->where('source_node_id', $nodeId);
        
        if ($children->isEmpty()) {
            return 1;
        }
        
        $maxChildDepth = 0;
        foreach ($children as $child) {
            $childDepth = $this->calculateNodeDepth($child->target_node_id, $edges, $nodes, $visited);
            $maxChildDepth = max($maxChildDepth, $childDepth);
        }
        
        return $maxChildDepth + 1;
    }
}