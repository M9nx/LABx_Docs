# Lab Progress Tracking System - API Security

## Overview
All API Security labs have a **solved tracking system** that automatically tracks your progress.

## Features

### Main Index Page (API/index.php)
- Shows total number of solved labs in header stats
- Green "✓ Solved" badges appear next to completed labs
- Real-time progress tracking

### Individual Lab Pages (LabX/index.php)
- Displays "✅ Lab Already Solved!" banner if completed
- Reminds you to reset the lab to try again

### Success Pages (LabX/success.php)  
- Automatically marks lab as solved when you complete it
- Updates progress database

### Setup Scripts (LabX/setup_db.php)
- Resets lab solved status when you run setup
- Allows you to retry labs

## How It Works

### Database
- Uses `api_progress` database
- Table: `solved_labs` tracks which labs are completed
- Persistent across sessions

### Functions (progress.php)
- `markLabSolved($lab_number)` - Mark a lab as complete
- `isLabSolved($lab_number)` - Check if lab is solved
- `resetLab($lab_number)` - Reset lab status
- `getAllSolvedLabs()` - Get array of solved lab numbers
- `getSolvedCount()` - Get total count of solved labs

## Usage Flow

1. **Start Lab**: Setup database → Lab shows as unsolved
2. **Complete Lab**: Reach success.php → Automatically marked as solved
3. **Main Page**: Shows green badge and updates solved count
4. **Reset**: Run setup_db.php → Lab marked as unsolved again

## Database Configuration
Uses same MySQL credentials as labs:
- Host: localhost
- User: root
- Password: root
- Database: api_progress

## Planned Labs (OWASP API Security Top 10)

1. **API1** - Broken Object Level Authorization
2. **API2** - Broken Authentication
3. **API3** - Broken Object Property Level Authorization
4. **API4** - Unrestricted Resource Consumption
5. **API5** - Broken Function Level Authorization
6. **API6** - Unrestricted Access to Sensitive Business Flows
7. **API7** - Server Side Request Forgery
8. **API8** - Security Misconfiguration
9. **API9** - Improper Inventory Management
10. **API10** - Unsafe Consumption of APIs
