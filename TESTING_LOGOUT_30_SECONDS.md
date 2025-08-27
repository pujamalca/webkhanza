# 🧪 Testing Logout Functionality - 30 Second Timeout

## 📋 Test Scenario
Test automatic logout when session expires after 30 seconds of inactivity.

## 🚀 Quick Testing Steps

### 1️⃣ Set 30-second timeout
```bash
cd D:\laragon\www\webkhanza
php artisan session:timeout 30
```
**Expected output:**
```
✅ Session timeout set to 30 seconds (0.5 minutes)
📝 Updated .env file with SESSION_LIFETIME=0.5
🔄 Config cache cleared
⚠️  Very short session timeout set! Remember to restore it after testing.
💡 To restore: php artisan session:timeout 7200 (2 hours)
```

### 2️⃣ Start Laravel server
```bash
php artisan serve --port=8000
```

### 3️⃣ Test in Browser
1. **Login**: Go to `http://127.0.0.1:8000/admin` and login
2. **Check status**: After login, check database:
   ```bash
   php check_tables.php
   ```
   Should show:
   - `is_logged_in: true`
   - `device_token: has token`
   - `logged_in_at: [current time]`

3. **Wait 31 seconds**: Don't click anything, just wait
4. **Try to navigate**: Click any menu or refresh page
5. **Should redirect to login** with message: *"Session telah berakhir. Silakan login kembali."*

### 4️⃣ Verify Database Cleanup
```bash
php check_tables.php
```
**Expected after timeout:**
- `is_logged_in: false`
- `device_token: NULL`
- `device_info: NULL`
- `logged_in_at: NULL`
- `Sessions: No sessions found`

### 5️⃣ Restore Normal Timeout (IMPORTANT!)
```bash
php artisan session:timeout 7200
```
**This sets timeout back to 2 hours (normal)**

## 🧪 Alternative: Quick Script Test
```bash
# Run the automated test
php test_30_second_timeout.php

# Check results
php check_tables.php
```

## ✅ Success Criteria
- [ ] Login works normally
- [ ] After 30+ seconds inactivity → auto redirect to login
- [ ] Database fields cleared: `is_logged_in=false`, `device_token=null`, etc.
- [ ] All sessions removed from database
- [ ] Login page shows timeout message
- [ ] Can login again after timeout

## 🔧 Troubleshooting

**If timeout doesn't work:**
1. Check if SessionTimeoutHandler middleware is active
2. Verify session driver is 'database'
3. Clear all caches: `php artisan optimize:clear`

**If database not updating:**
1. Check event listeners are registered
2. Test with script: `php test_30_second_timeout.php`
3. Check logs: `tail -f storage/logs/laravel.log`

**Session not expiring:**
1. Verify .env updated: `grep SESSION_LIFETIME .env`
2. Clear config: `php artisan config:clear`
3. Restart server

## 📝 What Gets Tested
- ✅ SessionTimeoutHandler middleware
- ✅ Database cleanup on timeout
- ✅ User status updates (is_logged_in, device_token, etc.)
- ✅ Session removal from database
- ✅ Proper redirect with message
- ✅ Event listeners working

## ⚠️ Important Notes
- **Always restore timeout** after testing: `php artisan session:timeout 7200`
- Test in incognito/private browsing to avoid cache issues
- Each test login creates a new session
- Database cleanup is immediate when session expires

## 🎯 Expected Timeline
- **0-30 seconds**: Normal operation
- **31+ seconds**: Auto logout on next request
- **Database update**: Immediate after timeout detected