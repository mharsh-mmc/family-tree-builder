Project Name: Dynamic Family Tree Builder & Viewer

Part 1 — Overview & Tech Stack
Purpose:
A modern, interactive Family Tree Builder & Viewer application where users can drag-and-drop nodes (family members), connect them visually, and have the system automatically generate three additional relationship-sorted formats — Vertical, Horizontal, and Circular — in addition to the custom user-drawn tree.

Tech Stack:

Backend: Laravel 12

Frontend Integration: Inertia.js

Frontend Framework: Vue.js (latest stable)

Authentication & User Management: Laravel Jetstream

Family Tree Rendering & Interactions: VueFlow

Storage: Laravel Filesystem (for profile images)

Database: MySQL

Part 2 — Family Tree Builder (Interactive Mode)
Section Structure:

Full-screen canvas for building the tree

Center node: Auto-generated profile of the logged-in account holder from Jetstream-authenticated user data

Left-side toolbar: Add person, connect nodes, undo/redo, save tree

Right-side panel: Properties of selected node (edit biodata, profile pic, relation, etc.)

Node Fields:

Name (text)

Relation (dropdown — full kinship list)

Profile Picture (upload)

Date of Birth (calendar)

Alive? (boolean switch)

Date of Death (if applicable)

Additional biodata (textarea)

Features:

Drag-and-Drop Node Creation (VueFlow)

Edge Creation: Drag from one node to another

Sorting Algorithm: Generates hierarchical structure on save

Automatic Format Generation: Backend creates 3 extra layouts (Vertical, Horizontal, Circular)

Part 3 — Flexible CRUD Operations
Requirements:

Create: Add a new node

Read: Fetch node details

Update (Partial):

Update only changed fields without requiring all fields again

Implemented via PATCH requests

Vue detects changed fields & sends only them

Delete: Remove node and related edges

Laravel API Routes:

php
Copy
Edit
Route::middleware('auth')->group(function () {
    Route::get('/family-tree', [FamilyTreeController::class, 'index'])->name('family-tree.index');
    Route::post('/family-tree/node', [FamilyTreeController::class, 'store'])->name('family-tree.store');
    Route::get('/family-tree/node/{id}', [FamilyTreeController::class, 'show'])->name('family-tree.show');
    Route::patch('/family-tree/node/{id}', [FamilyTreeController::class, 'update'])->name('family-tree.update');
    Route::delete('/family-tree/node/{id}', [FamilyTreeController::class, 'destroy'])->name('family-tree.destroy');
    Route::post('/family-tree/edges', [FamilyTreeController::class, 'storeEdge'])->name('family-tree.edges.store');
});
Database Tables:

family_tree_nodes: id, user_id, name, relation, profile_pic, dob, is_alive, dod, biodata, position_x, position_y

family_tree_edges: id, source_node_id, target_node_id, relation_type

Part 4 — Family Tree Viewer (Public Mode)
Features:

Viewer mode defaults to read-only

Toolbar: Format selector, Zoom In/Out, Fullscreen, Edit (if allowed)

Dynamic format switching without reload

Node Appearance:

Circular profile picture

User’s name below image

Relation below name (smaller font)

Profile Popup Modal:

Opens on click/tap of node

Displays: Name, Relation, DOB, Alive/Deceased, Date of Death, Profile Picture, Additional Bio

Responsive & scrollable

Part 5 — Component Structure
Frontend Components (Vue.js with Inertia):

FamilyTreeBuilder.vue → Main builder canvas with VueFlow

NodeForm.vue → Form for adding/editing person details

ProfileModal.vue → Displays full person profile in viewer mode

Toolbar.vue → Actions for add node, connect, save, undo, redo

ViewerToolbar.vue → Format selector, zoom, fullscreen, edit toggle

FamilyTreeViewer.vue → Public view with read-only VueFlow integration

NodeComponent.vue → Custom VueFlow node with circular profile pic, name, relation

Backend Structure (Laravel):

app/Http/Controllers/FamilyTreeController.php → Handles CRUD and format generation

app/Services/TreeFormatterService.php → Generates vertical, horizontal, circular layouts

database/migrations/* → Tables for nodes and edges

app/Models/FamilyTreeNode.php & FamilyTreeEdge.php

Part 6 — Color Theme & UX Details
Color Palette:

Primary: Deep Blue #1E3A8A

Secondary: Warm Gold #EAB308

Background: Light Gray #F9FAFB

Edges: Soft Gray #9CA3AF

Node Highlight: Emerald Green #10B981

UX Enhancements:

Smooth drag animations

Hover tooltips for quick details

Auto-save every 30 seconds

Delete confirmation modal

Animated profile popups

Part 7 — Automatic Format Generation Logic
Triggered after saving the custom layout

TreeFormatterService processes relationship hierarchy

Generates JSON for:

Custom layout (user’s original)

Vertical layout

Horizontal layout

Circular layout

Stored for instant switching in viewer mode

This final prompt ensures:
✅ Drag-and-drop creation
✅ Flexible CRUD with partial updates
✅ Circular profile pics + relation in viewer
✅ Profile modal on click
✅ Multiple auto-generated layouts
✅ Full Laravel + Vue component structure
