# BIMS - Barangay Information Management System

A comprehensive web-based information management system designed for Philippine Barangays (villages). Built with Laravel, Livewire, and a professional UI design system.

## Features

### Resident Management
- Complete resident registration with demographics, socio-economic profile, and emergency contacts
- Philippine-standard fields: Purok/Zone, Civil Status, Citizenship, Blood Type, Religion
- Resident classification: Senior Citizens, PWD, Solo Parents, Indigent, Registered Voters
- Resident archival (soft delete) with restore and permanent delete options
- Automatic resident ID number generation (BGY-YYYY-XXXX format)
- Photo, signature, and fingerprint attachment support
- Resident search by name, ID, purok, and status filters

### Household Management
- Optional household registration (skip for rural barangays without household numbers)
- DILG-standard household profiling fields
- Resident-to-household linking
- Household address management per purok

### Document Issuance & Printing
- Philippine-standard document templates with proper formatting:
  - Barangay Clearance
  - Certificate of Residency
  - Certificate of Indigency
  - Certificate of Good Moral Character
  - Business Permit Clearance
- Automatic document control number generation
- Print-ready PDF generation via DomPDF
- Header format: Left logo (Municipality), Center text (Republic of the Philippines), Right logo (Barangay)
- Configurable document fees and validity periods

### Barangay ID System
- Credit card-sized ID card (85.6mm x 53.98mm)
- Front and back design with resident photo, QR code, and barcode
- Two printing methods:
  - **Inkjet + Lamination Sheet**: Mirror image printing for lamination. Card size (1-up) or mass printing (multiple per A4/Letter/A5 sheet)
  - **Direct-to-Card**: Normal printing, 2-step process (print front, flip card, print back)
- Mass printing: Select multiple residents, print front+back side by side on sheets
- Sheet size options: A4, Letter, A5
- ID cancellation and reissuance with version tracking
- ID validity periods configurable per document template

### Blotter / Incident Records
- Incident recording with type, date, time, location, and narrative
- Complainant and respondent linking from resident database (or manual name entry)
- Status tracking: Pending, In Progress, Escalated, Resolved, Closed
- Blotter number auto-generation (BLT-YYYY-XXXX)
- Print-ready blotter report format
- Search by blotter number, party names, or location

### Business Registration
- Business registration with owner, type, address, and permit details
- Active/Inactive status tracking
- Search by business name or owner

### Revenue / Treasury
- Payment recording with OR (Official Receipt) number generation
- Category-based tracking: Document fees, ID fees, Penalties, Business permits
- Payment method tracking: Cash, GCash, Bank Transfer
- Total revenue summary and filtering
- Automatic revenue logging when documents or IDs are issued

### Health Records
- Health program participation tracking
- Vaccination records
- Checkup records
- Provider and scheduling information

### Service Requests
- Community service and maintenance request logging
- Categories: Road Repair, Drainage, Lighting, Flooding, etc.
- Priority levels: Low, Medium, Urgent
- Status tracking: Open, In Progress, Resolved, Closed
- Assign staff to requests

### Officials Management
- Elected officials tracking with position, category, and term dates
- Position types: Punong Barangay, Kagawad, SK Chairperson, Treasurer, Secretary, etc.
- Admin-only access (Level 4 privilege)

### Announcements & Meetings
- Community announcements with categories (Emergency, Health, Meeting, etc.)
- Urgent/Priority flagging
- Meeting minutes recording with attendees and resolutions
- Meeting number auto-generation (MN-YYYY-XXX)

### Reports & Analytics
- Demographics overview (age, gender, purok distribution)
- Special categories: PWD, Senior Citizens, Solo Parents, Registered Voters
- Operations summary: Documents issued, IDs, revenue, blotters
- Revenue breakdown by category and period
- Blotter status analysis

### Admin Dashboard
- System-wide statistics at a glance
- Recent activity feed
- Quick action buttons
- Demographics and operations overview
- Only visible to Level 4 (Administrator) accounts

### User Management & Privilege System
- 4-level privilege system:
  - **Level 4** - Administrator: Full access to everything
  - **Level 3** - Treasurer/Secretary: Revenue, Treasury, Reports, plus Level 2
  - **Level 2** - Barangay Official: Residents, Documents, Services, Blotter, etc.
  - **Level 1** - Staff: Basic operations (Dashboard, Residents, Documents, IDs)
- Free-form position/title fields (not limited to predefined roles)
- Account activation/deactivation

### User Preferences
- Theme selection: Light, Dark, System (auto-detect)
- Font family: Figtree, Inter, Roboto, Open Sans, Poppins, System
- Font size: Small, Default, Large, Extra Large
- Date format selection
- Table density: Compact, Comfortable, Spacious
- Per-account settings (does not affect other users)

### ID Scanner
- Barcode/QR code scanner for resident lookup via USB barcode scanner
- Manual ID number entry
- Quick resident profile display

### Document Templates
- Customizable templates per document type
- Template variables: Full name, first name, last name, purok, barangay, municipality, province, date fields, purpose
- Fee configuration per template
- Validity period configuration (days, months, years)
- Paper size and orientation settings

### System Settings
- Barangay information (name, municipality, province, region, contact)
- Barangay logo and official seal upload
- Document template management
- Purok management (add, edit, activate/deactivate)

### Dark Mode & Theming
- Full dark mode support with comprehensive CSS variable overrides
- All components adapt: tables, cards, badges, forms, buttons, modals
- Sidebar text visibility fixed for both themes
- User-selectable theme preference

### Search Functionality
- All list pages have working search bars
- Search across relevant fields (name, ID numbers, locations, etc.)
- Properly grouped SQL queries (no broken orWhere leaks)
- Filter dropdowns for status, type, purok, etc.

### Responsive Design
- Mobile-friendly with collapsible sidebar
- Desktop and tablet optimized
- Print-optimized layouts for documents and IDs

---

## Local Development Setup

### Prerequisites

You need the following tools installed on your computer:

1. **PHP 8.2+** (with extensions: pdo_pgsql, pgsql, mbstring, openssl, tokenizer, xml, ctype, json, bcmath)
   - **Windows**: Install [XAMPP](https://www.apachefriends.org/) (includes PHP + Apache) or use the standalone PHP binary from [php.net](https://windows.php.net/download/)
   - **macOS**: `brew install php`
   - **Linux**: `sudo apt install php php-pgsql php-mbstring php-xml php-curl`

2. **PostgreSQL 14+** (or use Neon PostgreSQL cloud database)
   - **Windows**: Install from [postgresql.org](https://www.postgresql.org/download/windows/) or use XAMPP with the included tools
   - **macOS**: `brew install postgresql`
   - **Linux**: `sudo apt install postgresql postgresql-contrib`
   - **Cloud (Recommended)**: Create a free account at [Neon](https://neon.tech) - no local install needed

3. **Composer** (PHP dependency manager)
   - Download from [getcomposer.org](https://getcomposer.org/download/)
   - Verify: `composer --version`

4. **Node.js 18+** and npm (for frontend assets)
   - Download from [nodejs.org](https://nodejs.org/)
   - Verify: `node --version && npm --version`

5. **Git** (version control)
   - Download from [git-scm.com](https://git-scm.com/downloads)

### Installation Steps

#### Step 1: Clone or Extract the Project
```bash
# If cloning from git:
git clone <repository-url>
cd barangay-registry

# Or extract the zip file and navigate to the folder:
cd barangay-registry
```

#### Step 2: Install PHP Dependencies
```bash
composer install
```

#### Step 3: Install Node.js Dependencies and Build Assets
```bash
npm install
npm run build
```

#### Step 4: Configure Environment
```bash
# Copy the example environment file
cp .env.example .env

# Generate an application key
php artisan key:generate
```

#### Step 5: Set Up Database

**Option A: Using Neon PostgreSQL (Recommended)**
1. Go to [neon.tech](https://neon.tech) and create a free account
2. Create a new project
3. Copy the connection string from your Neon dashboard
4. Update your `.env` file:
```
DB_CONNECTION=pgsql
DB_URL=postgresql://neondb_owner:your-password@ep-xxx.neon.tech/neondb?sslmode=require
```

**Option B: Using Local PostgreSQL**
1. Start PostgreSQL
2. Create a database:
```sql
CREATE DATABASE barangay_registry;
```
3. Update your `.env` file:
```
DB_CONNECTION=pgsql
DB_HOST=127.0.0.1
DB_PORT=5432
DB_DATABASE=barangay_registry
DB_USERNAME=postgres
DB_PASSWORD=your_password
DB_SSLMODE=prefer
```

#### Step 6: Run Database Migrations
```bash
php artisan migrate
```

#### Step 7: Seed Demo Data (Optional)
```bash
php artisan db:seed
```
This loads sample residents, households, documents, blotters, businesses, and officials.

#### Step 8: Create Storage Symlink
```bash
php artisan storage:link
```

#### Step 9: Start the Development Server
```bash
php artisan serve
```

Visit [http://localhost:8000](http://localhost:8000) in your browser.

### Default Login Credentials

| Role | Email | Password | Privilege Level |
|------|-------|----------|-----------------|
| Administrator | admin@barangay.local | password | Level 4 |
| Secretary | secretary@barangay.local | password | Level 3 |
| Staff | staff@barangay.local | password | Level 1 |

---

## Deployment to Vercel with Neon PostgreSQL

### Step 1: Push to GitHub
```bash
git add .
git commit -m "Initial commit"
git push origin main
```

### Step 2: Import to Vercel
1. Go to [vercel.com](https://vercel.com) and sign in
2. Click "Import Project" and select your GitHub repository
3. Vercel will detect the PHP project automatically

### Step 3: Set Environment Variables
In the Vercel dashboard, go to Settings > Environment Variables and add:

```
APP_KEY=base64:your-generated-key
APP_URL=https://your-project.vercel.app
APP_ENV=production
APP_DEBUG=false
DB_CONNECTION=pgsql
DB_URL=your_neon_connection_string_here
```

### Step 4: Deploy
Click "Deploy" and wait for the build to complete.

### Step 5: Run Migrations on Neon
After the first deploy, run migrations against your Neon database:
```bash
# Install Vercel CLI if you haven't
npm i -g vercel

# Link your project
vercel link

# Pull production environment variables
vercel env pull .env.production.local

# Run migrations
php artisan migrate --force

# Seed data (optional)
php artisan db:seed --force
```

### File Uploads on Vercel
Vercel's filesystem is read-only. For file uploads (resident photos, logos), configure an S3-compatible storage:
- **Cloudflare R2** (free tier): [dash.cloudflare.com](https://dash.cloudflare.com)
- **AWS S3**: [aws.amazon.com/s3](https://aws.amazon.com/s3/)

Add these environment variables:
```
FILESYSTEM_DISK=s3
AWS_ACCESS_KEY_ID=your-key
AWS_SECRET_ACCESS_KEY=your-secret
AWS_DEFAULT_REGION=auto
AWS_BUCKET=your-bucket
AWS_ENDPOINT=your-endpoint
AWS_URL=your-public-url
AWS_USE_PATH_STYLE_ENDPOINT=false
```

---

## Tech Stack

- **Backend**: Laravel 12 (PHP 8.2+)
- **Frontend**: Livewire 3 + Volt (full-stack Laravel components)
- **Styling**: Custom CSS design system (no Tailwind dependency for the main UI)
- **Icons**: Lucide Icons (MIT licensed)
- **Database**: PostgreSQL (Neon for cloud, local for development)
- **PDF Generation**: DomPDF via barryvdh/laravel-dompdf
- **QR Code**: endroid/qr-code
- **Export**: maatwebsite/excel
- **Deployment**: Vercel (serverless PHP)

## Project Structure

```
barangay-registry/
├── api/index.php              # Vercel entry point
├── app/
│   ├── Models/                # 20 Eloquent models
│   └── Http/Livewire/         # (Livewire components via Volt pages)
├── config/
│   ├── database.php           # PostgreSQL configuration
│   └── dompdf.php             # PDF generation config
├── database/
│   ├── migrations/            # 37 migration files
│   └── seeders/               # Demo data seeder
├── resources/
│   ├── css/                   # Design system (professional.css)
│   └── views/livewire/pages/  # All page components
│       ├── admin/             # Admin dashboard, user management
│       ├── announcements/     # Community announcements
│       ├── blotter/           # Incident records
│       ├── businesses/        # Business registration
│       ├── dashboard/         # Main dashboard
│       ├── documents/         # Document issuance & printing
│       ├── health/            # Health records
│       ├── households/        # Household management
│       ├── ids/               # Barangay ID system & mass printing
│       ├── meetings/          # Meeting minutes
│       ├── officials/         # Elected officials
│       ├── reports/           # Analytics & reports
│       ├── residents/         # Resident management
│       ├── revenue/           # Treasury & payments
│       ├── services/          # Service requests
│       └── settings/          # System settings & user preferences
├── routes/web.php             # Route definitions
├── vercel.json                # Vercel deployment config
└── .env.example               # Environment template
```

## License

This project is licensed under the MIT License. Created by **ameerdude**.
