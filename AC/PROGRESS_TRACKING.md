# Lab Progress Tracking System

## Overview
All Access Control labs now have a **solved tracking system** that automatically tracks your progress.

## Features

### Main Index Page (AC/index.php)
- Shows total number of solved labs in header stats
- Green "✓ Solved" badges appear next to completed labs
- Real-time progress tracking

### Individual Lab Pages (labX/index.php)
- Displays "✅ Lab Already Solved!" banner if completed
- Reminds you to reset the lab to try again

### Success Pages (labX/success.php)  
- Automatically marks lab as solved when you complete it
- Updates progress database

### Setup Scripts (labX/setup_db.php)
- Resets lab solved status when you run setup
- Allows you to retry labs

## How It Works

### Database
- Uses `ac_progress` database
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

## Files Modified

### New Files
- `AC/progress.php` - Progress tracking system

### Modified Files (All Labs 1-10)
- `labX/success.php` - Calls `markLabSolved(X)`
- `labX/setup_db.php` - Calls `resetLab(X)`
- `labX/index.php` - Shows solved banner
- `AC/index.php` - Shows solved badges

## Database Configuration
Uses same MySQL credentials as labs:
- Host: localhost
- User: root
- Password: root
- Database: ac_progress
