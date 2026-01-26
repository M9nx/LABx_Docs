# Lab Progress Tracking System - Authentication

## Overview
All Authentication labs have a **solved tracking system** that automatically tracks your progress.

## Features

### Main Index Page (Authentication/index.php)
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
- Uses `auth_progress` database
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
- Database: auth_progress

## Planned Labs

### Apprentice Level
1. Username enumeration via different responses
2. 2FA simple bypass
3. Weak password reset tokens

### Practitioner Level
4. Brute force with rate limiting bypass
5. Password reset via Host header poisoning
6. 2FA broken logic
7. 2FA brute force via response timing
8. JWT none algorithm attack
9. JWT weak secret attack
10. Session fixation attack

### Expert Level
11. OAuth token theft via redirect URI
12. Advanced JWT attacks (kid injection)
13. Multi-step authentication bypass
