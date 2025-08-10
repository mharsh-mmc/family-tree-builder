# 🌳 Dynamic Family Tree Builder & Viewer

A modern, interactive Family Tree Builder & Viewer application built with Laravel 12, Vue.js, and VueFlow. Users can drag-and-drop nodes (family members), connect them visually, and have the system automatically generate three additional relationship-sorted formats — Vertical, Horizontal, and Circular — in addition to the custom user-drawn tree.

## ✨ Features

### 🎨 Interactive Family Tree Builder
- **Full-screen Canvas**: Drag-and-drop interface for building family trees
- **Visual Node Creation**: Add family members with profile pictures and details
- **Relationship Connections**: Connect nodes to establish family relationships
- **Real-time Updates**: Immediate visual feedback as you build
- **Undo/Redo System**: Complete history management for all changes
- **Auto-save**: Automatic saving of tree changes

### 👥 Family Member Management
- **Comprehensive Profiles**: Name, relation, profile picture, date of birth, alive status
- **Profile Pictures**: Upload and manage profile images with Laravel Filesystem
- **Flexible Relations**: Full kinship list including step-family and in-law relationships
- **Biodata Support**: Additional information fields for each family member
- **Life Status Tracking**: Track living and deceased family members

### 🔄 Multiple Layout Formats
- **Custom Layout**: User's original drag-and-drop arrangement
- **Vertical Layout**: Hierarchical top-down family tree view
- **Horizontal Layout**: Left-to-right family tree representation
- **Circular Layout**: Radial family tree visualization
- **Instant Switching**: Seamless format changes without page reload

### 🌐 Public Family Tree Viewer
- **Read-only Mode**: Share family trees publicly
- **Profile Modals**: Detailed family member information on click
- **Zoom Controls**: Zoom in/out and fit-to-view functionality
- **Share Links**: Easy sharing of family tree URLs
- **Responsive Design**: Mobile-friendly interface

### 🛡️ Security & Authentication
- **Laravel Jetstream**: Complete authentication system
- **User Isolation**: Each user's family trees are private
- **Route Protection**: Secure API endpoints with middleware
- **File Upload Security**: Validated image uploads

## 🚀 Tech Stack

### Backend
- **Laravel 12** - Modern PHP framework
- **MySQL** - Database (latest version)
- **Laravel Filesystem** - Profile picture storage
- **Laravel Jetstream** - Authentication & user management

### Frontend
- **Vue.js 3** - Progressive JavaScript framework
- **Inertia.js** - Seamless SPA experience
- **VueFlow** - Interactive node-based diagrams
- **Tailwind CSS** - Utility-first CSS framework

### Development Tools
- **Vite** - Fast build tool
- **PHP 8.1+** - Modern PHP features
- **Composer** - PHP dependency management
- **npm** - JavaScript package management

## 📋 Requirements

- PHP 8.1 or higher
- Composer
- Node.js 16+ and npm
- MySQL 8.0+
- Web server (Apache/Nginx) or PHP built-in server

## 🛠️ Installation

### 1. Clone the Repository
```bash
git clone <repository-url>
cd dynamic-family-tree-builder-viewer
```

### 2. Install PHP Dependencies
```bash
composer install
```

### 3. Install JavaScript Dependencies
```bash
npm install
```

### 4. Environment Configuration
```bash
cp .env.example .env
php artisan key:generate
```

Configure your database in `.env`:
```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=family_tree_db
DB_USERNAME=your_username
DB_PASSWORD=your_password
```

### 5. Database Setup
```bash
php artisan migrate
php artisan storage:link
```

### 6. Build Frontend Assets
```bash
npm run build
```

### 7. Start Development Server
```bash
php artisan serve
```

Visit `http://localhost:8000` to access the application.

## 🗄️ Database Schema

### Family Tree Nodes
```sql
family_tree_nodes:
- id (primary key)
- user_id (foreign key to users)
- name (string)
- relation (string)
- profile_pic (string, nullable)
- dob (date, nullable)
- is_alive (boolean, default: true)
- dod (date, nullable)
- biodata (text, nullable)
- position_x (decimal)
- position_y (decimal)
- timestamps
```

### Family Tree Edges
```sql
family_tree_edges:
- id (primary key)
- source_node_id (foreign key to family_tree_nodes)
- target_node_id (foreign key to family_tree_nodes)
- relation_type (string)
- timestamps
```

## 🛣️ API Endpoints

### Web Routes (Protected)
- `GET /family-tree` - Interactive builder interface
- `POST /family-tree/node` - Create new family member
- `GET /family-tree/node/{id}` - Get family member details
- `PATCH /family-tree/node/{id}` - Update family member
- `DELETE /family-tree/node/{id}` - Delete family member
- `POST /family-tree/edges` - Create relationship connection
- `DELETE /family-tree/edges/{edge}` - Delete relationship

### Public Routes
- `GET /family-tree/viewer/{userId}` - Public family tree viewer

### API Routes
- `GET /api/family-tree/formats/{userId}` - Get all tree layout formats (JSON)

## 🎯 Usage Guide

### Creating Your First Family Tree

1. **Register/Login**: Create an account or sign in
2. **Access Builder**: Navigate to `/family-tree`
3. **Add Family Members**: Use the "Add Person" button to create nodes
4. **Connect Relationships**: Use "Connect Nodes" to establish family connections
5. **Customize Layout**: Drag nodes to position them as desired
6. **Save Changes**: Changes are automatically saved

### Managing Family Members

- **Edit Profiles**: Click on any node to edit its properties
- **Upload Photos**: Add profile pictures for family members
- **Update Information**: Modify names, relations, dates, and biodata
- **Delete Members**: Remove family members (with confirmation)

### Viewing Different Layouts

- **Custom**: Your original arrangement
- **Vertical**: Hierarchical top-down view
- **Horizontal**: Left-to-right family tree
- **Circular**: Radial visualization

### Sharing Your Family Tree

- **Public Viewer**: Share the viewer URL with family and friends
- **Profile Details**: Click on nodes to view detailed information
- **Zoom Controls**: Navigate large family trees easily

## 🔧 Customization

### Adding New Relationship Types
Edit `resources/js/Components/FamilyTree/NodeForm.vue` to add new relation options.

### Modifying Layout Algorithms
Customize the `TreeFormatterService` to adjust how automatic layouts are generated.

### Styling Changes
Modify Tailwind CSS classes in Vue components or add custom CSS.

## 🚀 Deployment

### Production Environment
1. Set `APP_ENV=production`