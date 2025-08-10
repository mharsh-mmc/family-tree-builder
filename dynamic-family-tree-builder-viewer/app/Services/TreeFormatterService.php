<?php

namespace App\Services;

use App\Models\FamilyTreeNode;
use App\Models\FamilyTreeEdge;
use Illuminate\Support\Collection;

class TreeFormatterService
{
    public function generateFormats(int $userId): array
    {
        $nodes = FamilyTreeNode::where('user_id', $userId)->get();
        $edges = FamilyTreeEdge::whereHas('sourceNode', function ($query) use ($userId) {
            $query->where('user_id', $userId);
        })->get();

        return [
            'custom' => $this->getCustomLayout($nodes, $edges),
            'vertical' => $this->generateVerticalLayout($nodes, $edges),
            'horizontal' => $this->generateHorizontalLayout($nodes, $edges),
            'circular' => $this->generateCircularLayout($nodes, $edges),
        ];
    }

    private function getCustomLayout(Collection $nodes, Collection $edges): array
    {
        return [
            'nodes' => $nodes->map(function ($node) {
                return [
                    'id' => $node->id,
                    'type' => 'familyNode',
                    'position' => ['x' => $node->position_x, 'y' => $node->position_y],
                    'data' => [
                        'name' => $node->name,
                        'relation' => $node->relation,
                        'profilePic' => $node->profile_pic_url,
                        'dob' => $node->dob?->format('Y-m-d'),
                        'isAlive' => $node->is_alive,
                        'dod' => $node->dod?->format('Y-m-d'),
                        'biodata' => $node->biodata,
                        'age' => $node->age,
                    ],
                ];
            }),
            'edges' => $edges->map(function ($edge) {
                return [
                    'id' => $edge->id,
                    'source' => $edge->source_node_id,
                    'target' => $edge->target_node_id,
                    'type' => 'smoothstep',
                    'data' => ['relationType' => $edge->relation_type],
                ];
            }),
        ];
    }

    private function generateVerticalLayout(Collection $nodes, Collection $edges): array
    {
        $hierarchy = $this->buildHierarchy($nodes, $edges);
        
        // Return empty layout if no nodes
        if ($nodes->count() === 0) {
            return [
                'nodes' => [],
                'edges' => [],
            ];
        }
        
        $formattedNodes = [];
        $level = 0;
        $maxLevel = $this->getMaxLevel($hierarchy);

        foreach ($hierarchy as $nodeId => $children) {
            $node = $nodes->find($nodeId);
            if ($node) {
                $formattedNodes[] = [
                    'id' => $node->id,
                    'type' => 'familyNode',
                    'position' => [
                        'x' => 400 + ($level * 50),
                        'y' => 100 + ($this->getNodeLevel($nodeId, $hierarchy) * 120)
                    ],
                    'data' => [
                        'name' => $node->name,
                        'relation' => $node->relation,
                        'profilePic' => $node->profile_pic_url,
                        'dob' => $node->dob?->format('Y-m-d'),
                        'isAlive' => $node->is_alive,
                        'dod' => $node->dod?->format('Y-m-d'),
                        'biodata' => $node->biodata,
                        'age' => $node->age,
                    ],
                ];
            }
        }

        return [
            'nodes' => $formattedNodes,
            'edges' => $edges->map(function ($edge) {
                return [
                    'id' => $edge->id,
                    'source' => $edge->source_node_id,
                    'target' => $edge->target_node_id,
                    'type' => 'smoothstep',
                    'data' => ['relationType' => $edge->relation_type],
                ];
            }),
        ];
    }

    private function generateHorizontalLayout(Collection $nodes, Collection $edges): array
    {
        $hierarchy = $this->buildHierarchy($nodes, $edges);
        
        // Return empty layout if no nodes
        if ($nodes->count() === 0) {
            return [
                'nodes' => [],
                'edges' => [],
            ];
        }
        
        $formattedNodes = [];
        $level = 0;
        $maxLevel = $this->getMaxLevel($hierarchy);

        foreach ($hierarchy as $nodeId => $children) {
            $node = $nodes->find($nodeId);
            if ($node) {
                $level = $this->getNodeLevel($nodeId, $hierarchy);
                $formattedNodes[] = [
                    'id' => $node->id,
                    'type' => 'familyNode',
                    'position' => [
                        'x' => 100 + ($level * 200),
                        'y' => 200 + ($this->getHorizontalPosition($nodeId, $hierarchy) * 150)
                    ],
                    'data' => [
                        'name' => $node->name,
                        'relation' => $node->relation,
                        'profilePic' => $node->profile_pic_url,
                        'dob' => $node->dob?->format('Y-m-d'),
                        'isAlive' => $node->is_alive,
                        'dod' => $node->dod?->format('Y-m-d'),
                        'biodata' => $node->biodata,
                        'age' => $node->age,
                    ],
                ];
            }
        }

        return [
            'nodes' => $formattedNodes,
            'edges' => $edges->map(function ($edge) {
                return [
                    'id' => $edge->id,
                    'source' => $edge->source_node_id,
                    'target' => $edge->target_node_id,
                    'type' => 'smoothstep',
                    'data' => ['relationType' => $edge->relation_type],
                ];
            }),
        ];
    }

    private function generateCircularLayout(Collection $nodes, Collection $edges): array
    {
        $hierarchy = $this->buildHierarchy($nodes, $edges);
        $formattedNodes = [];
        $centerX = 400;
        $centerY = 300;
        $radius = 200;
        $nodeCount = $nodes->count();
        
        // Return empty layout if no nodes
        if ($nodeCount === 0) {
            return [
                'nodes' => [],
                'edges' => [],
            ];
        }
        
        $angleStep = 2 * M_PI / $nodeCount;
        $currentAngle = 0;

        foreach ($hierarchy as $nodeId => $children) {
            $node = $nodes->find($nodeId);
            if ($node) {
                $formattedNodes[] = [
                    'id' => $node->id,
                    'type' => 'familyNode',
                    'position' => [
                        'x' => $centerX + ($radius * cos($currentAngle)),
                        'y' => $centerY + ($radius * sin($currentAngle))
                    ],
                    'data' => [
                        'name' => $node->name,
                        'relation' => $node->relation,
                        'profilePic' => $node->profile_pic_url,
                        'dob' => $node->dob?->format('Y-m-d'),
                        'isAlive' => $node->is_alive,
                        'dod' => $node->dod?->format('Y-m-d'),
                        'biodata' => $node->biodata,
                        'age' => $node->age,
                    ],
                ];
                $currentAngle += $angleStep;
            }
        }

        return [
            'nodes' => $formattedNodes,
            'edges' => $edges->map(function ($edge) {
                return [
                    'id' => $edge->id,
                    'source' => $edge->source_node_id,
                    'target' => $edge->target_node_id,
                    'type' => 'smoothstep',
                    'data' => ['relationType' => $edge->relation_type],
                ];
            }),
        ];
    }

    private function buildHierarchy(Collection $nodes, Collection $edges): array
    {
        $hierarchy = [];
        
        foreach ($nodes as $node) {
            $hierarchy[$node->id] = [];
        }

        foreach ($edges as $edge) {
            if (!isset($hierarchy[$edge->source_node_id])) {
                $hierarchy[$edge->source_node_id] = [];
            }
            $hierarchy[$edge->source_node_id][] = $edge->target_node_id;
        }

        return $hierarchy;
    }

    private function getNodeLevel(int $nodeId, array $hierarchy, int $currentLevel = 0): int
    {
        if (empty($hierarchy[$nodeId])) {
            return $currentLevel;
        }

        $maxLevel = $currentLevel;
        foreach ($hierarchy[$nodeId] as $childId) {
            $maxLevel = max($maxLevel, $this->getNodeLevel($childId, $hierarchy, $currentLevel + 1));
        }

        return $maxLevel;
    }

    private function getMaxLevel(array $hierarchy): int
    {
        $maxLevel = 0;
        foreach (array_keys($hierarchy) as $nodeId) {
            $maxLevel = max($maxLevel, $this->getNodeLevel($nodeId, $hierarchy));
        }
        return $maxLevel;
    }

    private function getHorizontalPosition(int $nodeId, array $hierarchy): int
    {
        $children = $hierarchy[$nodeId] ?? [];
        if (empty($children)) {
            return 0;
        }

        $totalPosition = 0;
        foreach ($children as $childId) {
            $totalPosition += $this->getHorizontalPosition($childId, $hierarchy);
        }

        return $totalPosition / count($children);
    }
}